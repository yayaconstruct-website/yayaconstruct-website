#!/usr/bin/env python3
"""Preview the theme locally without a WordPress install.

    python3 tools/harness.py --serve

Fetches the live pages once, rewrites them to match the working tree, and
serves the set at http://localhost:8731. Nothing here is deployed — the deploy
workflow only mirrors `yayaconstruct-theme/`.

Three rewrites matter, and each of them once looked like a real bug:

1. **Stylesheet.** Production's CSS is stripped and `yayaconstruct-theme/style.css`
   is inlined, along with the branch's Google Fonts request (Archivo + Fragment
   Mono, not the Bebas/Barlow production still asks for).
2. **Tokens.** Production markup predates the palette rename, so inline
   `var(--rust)` survives in `style=` attributes and resolves to nothing —
   invisible text that looks exactly like a broken colour.
3. **Logo.** Production serves `images/logo.png`, a dark mark on an opaque
   near-white square, which lands on the dark nav as a white sticker. The
   branch replaced it with `images/logo-mark.png` — same mark, real alpha,
   recoloured to limewash. Without this swap the fixed logo looks unfixed.

Pages whose markup the working tree has changed can't be rewritten this way at
all; their body is rendered here from the live data instead. That currently
means the projects index, the project spec block, and — as of Batch 3 — the
home page's hero and featured-project block.
"""

import argparse
import http.server
import os
import pathlib
import re
import socketserver
import urllib.request

ROOT = pathlib.Path(__file__).resolve().parent.parent
THEME = ROOT / 'yayaconstruct-theme'
BUILD = ROOT / '.harness'
PORT = 8731

SITE = 'https://www.yayaconstruct.com'
FONTS = ('https://fonts.googleapis.com/css2?'
         'family=Archivo:wdth,wght@75..125,400..700&family=Fragment+Mono&display=swap')

LIVE_PAGES = {
    'live-home.html': 'https://yayaconstruct.com/',
    'live-projects.html': 'https://yayaconstruct.com/projects/',
    'live-project.html': 'https://yayaconstruct.com/project/inkim-suites/',
}

NAV_LOGO = '''<a class="nav-logo" href="/harness-home.html">
      <img src="images/logo-mark.png"
           alt="YAYA Construct"
           width="189" height="107"
           fetchpriority="high" decoding="async"
           class="nav-logo-img" />
    </a>'''

# Point the production chrome at the local pages so the set is clickable.
LINK_MAP = [
    (SITE + '/projects', '/harness-projects.html'),
    (SITE + '/project/inkim-suites/', '/harness-project.html'),
    (SITE + '/project/guzelbahce-x/', '/harness-project-three.html'),
    (SITE + '/project/z-suites/', '/harness-project-thin.html'),
    (SITE + '/', '/harness-home.html'),
]

# ── The five projects, as production publishes them today ────────────────────
UPLOADS = SITE + '/wp-content/uploads/2026/04/'
REGIONS = [
    ('aegean', 'Aegean', [
        ('Inkim Suites', '/harness-project.html', UPLOADS + 'IMG_0098-300x180.png', ['Ilica, Cesme', '2021']),
        ('Guzelbahce X', '/harness-project-three.html', UPLOADS + 'IMG_0090-300x225.png', ['Guzelbahce, Izmir', '2018']),
        ('Z-Suites', '/harness-project-thin.html', None, ['Izmir', 'Coming soon']),
    ]),
    ('low-countries', 'Low Countries', [
        ('Amsterdam', SITE + '/project/amsterdam/', UPLOADS + 'IMG_0120-300x225.jpg', []),
        ('Brussels', SITE + '/project/brussels/', UPLOADS + 'IMG_0112-225x300.jpg', []),
    ]),
]

SPEC_FULL = [('Location', 'Ilica, Cesme'), ('Year', '2021'), ('Scope', 'Conversion'),
             ('Area', '1,240 m²'), ('Status', 'Completed')]
SPEC_THREE = [('Location', 'Guzelbahce, Izmir'), ('Year', '2018'), ('Status', 'Completed')]
SPEC_THIN = [('Status', 'Coming soon')]   # Z-Suites: the flag is its only fact.

# ── Home page, as it renders today ───────────────────────────────────────────
# Hero text and the featured-project pick were tried under Batch 3 and rolled
# back at Emir's request; the real hero photo swap and the services-card
# removal stayed. See HANDOFF.md.
HERO = {
    'tag': 'Est. in Excellence',
    'line1': 'WE', 'line2': 'BUILD', 'line3': 'YOUR VISION',
    'sub': ('From groundbreaking to grand opening — Yaya Construct delivers '
            'construction that lasts generations.'),
    'cta1': 'View Our Work', 'cta1_url': '/harness-projects.html',
    'cta2': 'Get a Quote', 'cta2_url': SITE + '/contact/',
    # Real project photo (Inkim Suites, on the Aegean) — kept from Batch 3
    # even though the copy reverted; not the old generic Unsplash stand-in.
    'img': UPLOADS + 'IMG_0103.png',
}

STATS = [('150+', 'Projects Completed'), ('12+', 'Years of Experience'),
         ('98%', 'Client Satisfaction'), ('40+', 'Skilled Professionals')]

# Featured project: back to "the single newest non-coming-soon project",
# same as before Batch 3. Amsterdam is that project and has no description
# (confirmed live), so it falls back to the canned filler paragraph — the
# original behavior.
FEATURED = {
    'title': 'Amsterdam',
    'href': SITE + '/project/amsterdam/',
    'img': UPLOADS + 'IMG_0120-1024x768.jpg',
    'spec': [],
    'text': ('Every project we take on is a testament to our commitment to '
             'quality. Our team of experienced builders, engineers, and '
             'project managers ensure every detail is executed to perfection.'),
}


def fetch_live():
    BUILD.mkdir(exist_ok=True)
    for name, url in LIVE_PAGES.items():
        target = BUILD / name
        if target.exists():
            continue
        print('fetching', url)
        req = urllib.request.Request(url, headers={'User-Agent': 'harness/1.0'})
        with urllib.request.urlopen(req, timeout=30) as r:
            target.write_bytes(r.read())


def strip_assets(html):
    html = re.sub(r'<link[^>]+rel=[\'"]stylesheet[\'"][^>]*/?>', '', html, flags=re.I)
    html = re.sub(r'<script[^>]+src=[^>]*></script>', '', html, flags=re.I)
    html = re.sub(r'<style id=[\'"]?(wp-|global-|classic-|core-)[^>]*>.*?</style>', '', html,
                  flags=re.I | re.S)
    return html


def inject(html):
    css = (THEME / 'style.css').read_text(encoding='utf-8')
    html = html.replace('</head>', f'<link rel="stylesheet" href="{FONTS}">\n<style>\n{css}\n</style>\n</head>', 1)
    html = html.replace('var(--rust)', 'var(--aegean)')
    html = html.replace('var(--charcoal)', 'var(--slate)')
    html = html.replace('var(--cream)', 'var(--limewash)')
    html = re.sub(r'<a class="nav-logo".*?</a>', NAV_LOGO, html, count=1, flags=re.S)
    for live, local in LINK_MAP:
        html = html.replace(f'href="{live}"', f'href="{local}"')
    # The scroll reveals are real, but assertions must not race the observer.
    html = html.replace('</body>', '<script>document.querySelectorAll(".reveal")'
                                   '.forEach(function (el) { el.classList.add("visible"); });'
                                   '</script></body>', 1)
    return html


def render_index():
    rows = ['<div class="page-wrap">', '''
  <div class="projects-hero">
    <div class="section-label reveal" style="color:var(--aegean)">Portfolio</div>
    <h1 class="reveal">Projects</h1>
  </div>
''', '<div class="filter-bar" id="projects-filter">',
            '  <button type="button" class="filter-btn active" data-filter="all">All Projects</button>']
    for slug, name, _ in REGIONS:
        rows.append(f'  <button type="button" class="filter-btn" data-filter="{slug}">{name}</button>')
    rows += ['</div>', '<div class="project-index" id="project-index">']

    n = 0
    for slug, name, items in REGIONS:
        rows.append(f'<section class="project-region reveal" data-cat="{slug}" id="projects-{slug}">')
        rows.append(f'  <h2 class="project-region-title">{name}</h2>')
        rows.append('  <ol class="project-list">')
        for title, href, img, detail in items:
            n += 1
            parts = []
            for k, part in enumerate(detail):
                if k:
                    parts.append('<span class="project-row-sep">&middot;</span>')
                parts.append(part)
            detail_html = f'<span class="project-row-detail">{" ".join(parts)}</span>' if parts else ''
            thumb = (f'<span class="project-row-thumb" aria-hidden="true"><img src="{img}" alt="" loading="lazy" /></span>'
                     if img else
                     '<span class="project-row-thumb project-row-thumb--empty" aria-hidden="true"></span>')
            rows.append(f'''    <li class="project-list-item">
      <a class="project-row" href="{href}">
        <span class="project-row-index">{n:02d}</span>
        <span class="project-row-name">{title}</span>
        {detail_html}
        {thumb}
      </a>
    </li>''')
        rows += ['  </ol>', '</section>']
    rows += ['</div>', '</div>']
    return '\n'.join(rows)


def render_spec(rows):
    items = '\n'.join(f'      <div class="project-spec-item"><dt>{k}</dt><dd>{v}</dd></div>'
                      for k, v in rows)
    return f'<div class="project-spec">\n  <dl class="project-spec-grid">\n{items}\n  </dl>\n</div>'


def render_home():
    stats = '\n'.join(
        f'  <div class="stat-item reveal">\n'
        f'    <div class="stat-num">{n}</div>\n'
        f'    <div class="stat-label">{l}</div>\n'
        f'  </div>' for n, l in STATS)

    spec_line = ' &middot; '.join(v for _, v in FEATURED['spec'])

    return f'''<!-- Hero -->
<section class="hero" style="--hero-bg: url('{HERO['img']}')">
  <div class="hero-bg"></div>
  <p class="hero-tag">{HERO['tag']}</p>
  <h1>
    {HERO['line1']}<br>
    <span class="hero-accent">{HERO['line2']}</span><br>
    {HERO['line3']}
  </h1>
  <p class="hero-sub">{HERO['sub']}</p>
  <div class="hero-cta">
    <a href="{HERO['cta1_url']}" class="btn-primary">{HERO['cta1']}</a>
    <a href="{HERO['cta2_url']}" class="btn-outline">{HERO['cta2']}</a>
  </div>
  <div class="hero-scroll" aria-hidden="true">
    <div class="scroll-line"></div>
    Scroll
  </div>
</section>

<!-- Stats -->
<div class="stats-bar">
{stats}
</div>

<!-- Featured Project -->
<section class="home-project" aria-labelledby="featured-title">
  <div class="home-project-img reveal">
    <a href="{FEATURED['href']}" tabindex="-1" aria-hidden="true">
      <img src="{FEATURED['img']}" alt="" loading="lazy" decoding="async" />
    </a>
  </div>
  <div class="home-project-content reveal">
    <p class="section-label">Featured Work</p>
    <h2 id="featured-title" class="section-title">
      <a class="home-project-link" href="{FEATURED['href']}">{FEATURED['title']}</a>
    </h2>
    {f'<p class="home-project-meta">{spec_line}</p>' if spec_line else ''}
    <p>{FEATURED['text']}</p>
    <a href="/harness-projects.html" class="btn-primary">Explore All Projects</a>
  </div>
</section>

'''


def build_home():
    html = (BUILD / 'live-home.html').read_text(encoding='utf-8')
    start = html.find('<!-- Hero -->')
    end = html.find('<script type="application/ld+json">')
    assert start > 0 and end > start, 'live home markup did not match — refetch?'
    html = html[:start] + render_home() + html[end:]
    (BUILD / 'harness-home.html').write_text(inject(strip_assets(html)), encoding='utf-8')


def build_projects():
    html = (BUILD / 'live-projects.html').read_text(encoding='utf-8')
    start = html.find('<div class="page-wrap">')
    end = html.find('<script>', html.find('id="project-groups"'))
    assert start > 0 and end > start, 'live projects markup did not match — refetch?'
    html = html[:start] + render_index() + '\n' + html[end:]
    # The filter script is lifted straight out of the template so the harness
    # exercises the real one.
    template = (THEME / 'page-projects.php').read_text(encoding='utf-8')
    new_js = re.search(r'<script>\n\(function \(\) \{\n  var bar.*?\}\)\(\);\n</script>', template, re.S)
    assert new_js, 'could not lift the filter script out of page-projects.php'
    a = html.find('<script>\n(function () {\n  var bar')
    b = html.find('</script>', a) + len('</script>')
    html = html[:a] + new_js.group(0) + html[b:]
    (BUILD / 'harness-projects.html').write_text(inject(strip_assets(html)), encoding='utf-8')


def build_project(name, spec_rows):
    html = (BUILD / 'live-project.html').read_text(encoding='utf-8')
    # The hero lost its meta line; the spec block carries those values now.
    html = re.sub(r'<p class="project-detail-meta">.*?</p>', '', html, flags=re.S)
    block = render_spec(spec_rows) + '\n\n' if spec_rows else ''
    html = html.replace('<!-- Content -->', block + '<!-- Content -->', 1)
    (BUILD / name).write_text(inject(strip_assets(html)), encoding='utf-8')


def build():
    fetch_live()
    build_home()
    build_projects()
    build_project('harness-project.html', SPEC_FULL)
    build_project('harness-project-three.html', SPEC_THREE)
    build_project('harness-project-thin.html', SPEC_THIN)
    link = BUILD / 'images'
    if not link.exists():
        link.symlink_to(THEME / 'images')
    print('built', len(list(BUILD.glob('harness-*.html'))), 'pages in', BUILD)


def serve():
    os.chdir(BUILD)
    socketserver.TCPServer.allow_reuse_address = True
    with socketserver.TCPServer(('', PORT), http.server.SimpleHTTPRequestHandler) as httpd:
        print(f'http://localhost:{PORT}/harness-home.html   (ctrl-c to stop)')
        httpd.serve_forever()


if __name__ == '__main__':
    ap = argparse.ArgumentParser(description=__doc__,
                                 formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument('--serve', action='store_true', help='serve the harness after building')
    ap.add_argument('--refetch', action='store_true', help='re-download the live pages first')
    args = ap.parse_args()
    if args.refetch:
        for f in BUILD.glob('live-*.html'):
            f.unlink()
    build()
    if args.serve:
        serve()
