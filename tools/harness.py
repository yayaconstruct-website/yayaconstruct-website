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

# ── Home page, as Batch 3 renders it ─────────────────────────────────────────
HERO = {
    'tag': 'Two Latitudes, One Standard',
    'line1': 'BUILDING', 'line2': 'ACROSS', 'line3': 'TWO LATITUDES',
    'sub': ('Yaya Construct builds and renovates across two regions — the '
            'Aegean coast and the Low Countries — with the same standard '
            'of craft on every site.'),
    'cta1': 'View Our Work', 'cta1_url': '/harness-projects.html',
    'cta2': 'Get a Quote', 'cta2_url': SITE + '/contact/',
    # Real project photo (Inkim Suites, on the Aegean) — same asset the
    # template now defaults to — not the old generic Unsplash stand-in.
    'img': UPLOADS + 'IMG_0103.png',
}

STATS = [('150+', 'Projects Completed'), ('12+', 'Years of Experience'),
         ('98%', 'Client Satisfaction'), ('40+', 'Skilled Professionals')]

SERVICE_ICONS = [
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20"/><path d="M6 20V9"/><path d="M18 20V9"/><path d="M1 9l11-7 11 7"/><path d="M9 20v-6h6v6"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V9.5L12 3l7 6.5V21"/><path d="M9 21v-6h6v6"/><path d="M9 12h.01M15 12h.01"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="22"/><line x1="2" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="22" y2="12"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6M9 16h4"/></svg>',
]
SERVICES = [
    ('General Construction', 'Full-cycle construction management from planning to handover, delivered on time and within budget.'),
    ('Commercial Buildings', 'Office complexes, retail centers, warehouses, and industrial facilities built to the highest standards.'),
    ('Residential Projects', 'Custom homes, apartment buildings, and residential renovations crafted with care and precision.'),
    ('Renovation & Refit', 'Breathing new life into existing structures with expert renovation, retrofitting, and restoration work.'),
    ('Design & Build', 'Integrated design-build solutions combining architectural vision with construction expertise under one roof.'),
    ('Project Management', 'Professional oversight, scheduling, and coordination for complex multi-phase construction projects.'),
]

# Featured project: Amsterdam has no description (confirmed live — its
# get_the_excerpt() is genuinely empty, content and excerpt both), so the
# new "skip empty-excerpt candidates" logic passes over it. Which project
# WP_Query lands on next depends on post_date, which isn't observable
# without DB access — Inkim Suites stands in here as "a candidate with real
# content," not a claim about exactly which slug goes live.
FEATURED = {
    'title': 'Inkim Suites',
    'href': '/harness-project.html',
    'img': UPLOADS + 'IMG_0098-1024x614.png',
    'spec': SPEC_FULL,
    'text': ("Inkim Suites is described as the transformation of the "
             "long-standing Inkim Hotel into a refreshed residence concept "
             "in the heart of Ilica, Cesme. The source highlights "
             "Zabıtçı's renovation work, a central location "
             "close to beaches and amenities, and a residence program built "
             "around comfort, design, and year-round use."),
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

    cards = '\n'.join(
        f'    <div class="service-card reveal">\n'
        f'      <div class="service-icon" aria-hidden="true">{SERVICE_ICONS[i]}</div>\n'
        f'      <h3 class="service-title">{title}</h3>\n'
        f'      <p class="service-text">{text}</p>\n'
        f'    </div>' for i, (title, text) in enumerate(SERVICES))

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

<!-- Services -->
<section class="section" aria-labelledby="services-title">
  <p class="section-label reveal">What We Do</p>
  <h2 id="services-title" class="section-title reveal">OUR SERVICES</h2>
  <div class="services-grid">
{cards}
  </div>
</section>

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
    <p class="home-project-meta">{spec_line}</p>
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
