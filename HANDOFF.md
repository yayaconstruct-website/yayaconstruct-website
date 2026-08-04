# Redesign handoff — state as of 2 Aug 2026 (Batches 1–4, then A, B, C)

Working notes for the yayaconstruct.com redesign. Delete before merging to `main`
if you don't want it in the repo.

## Where things stand

| Branch | Contains | Deployed? |
|---|---|---|
| `main` @ `c96a7b7` | Accessibility + performance + SEO passes | **Yes — live** |
| `redesign/batch-1-reskin` @ `9c76390` | Batch 1 re-skin, logo + menu fixes | No |
| `redesign/batch-1-reskin` @ `aaabf6e` | Batch 2 project index | No |
| `redesign/batch-1-reskin` @ `46c2c8f` | Batch 3 (net of reverts) + hero-image default fixes | No |
| `redesign/batch-1-reskin` @ `fa6dad4` | Batch 4 — performance & hygiene | No |
| working tree | About/contact alignment pass — **uncommitted** | No |

**Batch 3 status:** the hero photo swap, the services-card removal, and the
`front-page.php` transition-delay cleanup are done. The hero copy, the
featured-block selection logic, and retiring the stats bar (item 15) were
all tried and then rolled back at Emir's request — see "Batch 3" below. Item
15 is the one item from the original four still open.

**Batch 4 status: all four items done** — see "Batch 4" below.

`.github/workflows/deploy.yml` FTPs `yayaconstruct-theme/` to production **on every
push to `main`**. Pushing the branch does not deploy. There is no staging.

## Already on main (live)

- `<main>` landmark, skip link, `h1→h2→h3` outline (the page had one heading)
- Focus rings; contrast fixes; reduced-motion; `<noscript>` reveal fallback
- Logo 1.1 MB → 54 KB; hero preloaded from `wp_head`; font preconnects
- Meta description, `og:type=website` on the front page, `GeneralContractor` JSON-LD
- Escape closes the mobile menu; `aria-label` toggles

## Batch 1 (branch, not merged)

Palette, three type roles, bounded grid, motion cut to one gesture, plus the logo
and menu fixes. **Two mechanisms in `style.css` that Batch 2 must not break:**

1. **Dark bands rebind tokens.** A selector list near the top sets
   `--aegean: var(--aegean-light)` and `--zinc: var(--zinc-on-dark)` on every dark
   band. Custom properties inherit, so this also reaches the `style="color:var(--aegean)"`
   attributes still inline in the templates — a normal stylesheet rule cannot.
   **Any new dark container must be added to that list** or its accent drops to 2.1:1.
2. **Filled surfaces use `--accent-fill`**, deliberately exempt from that rebinding.
   Buttons, filter pills and flags read it. Use it for any new filled surface;
   using `--aegean` gives light-teal-on-limewash at 2:1.

Also: bands use `padding-inline` (shared rule, outer margin defined once) and
`padding-block` (per component) so the two never collide. Don't reintroduce
shorthand `padding` on a band.

Type roles: `--font-display` (Archivo, `font-stretch: 112%`), `--font-body`
(Archivo), `--font-mono` (Fragment Mono — the data layer). Archivo's weight axis
starts at 400, so no `font-weight: 300`. Fragment Mono is wide: track in `em`, never `px`.

## Batch 2 — project index (done, committed)

| # | Change | File |
|---|---|---|
| 8 | 3-column card grid → indexed list | `page-projects.php`, `style.css` |
| 9 | Grouped by Aegean / Low Countries, counts dropped | `page-projects.php`, `functions.php` |
| 10 | Mono spec block (location · year · scope · area · status) | `single-project.php`, `style.css` |
| 11 | Project Details meta box for all five spec fields | `functions.php` |
| 12 | Filter bar retuned to the regions | `page-projects.php`, `style.css` |

The `21/9` single-group bug went with the grid — every `.project-card*` and
`.project-group*` rule is gone, and nothing else used them.

**Three mechanisms Batch 3 must not break:**

1. **`yaya_project_regions()` is the region map.** A city category that isn't in
   it gets a group of its own named after the category rather than vanishing, so
   adding a city in the admin degrades safely — but it won't join a region until
   it's added here.
2. **`yaya_project_spec_definition()` is the single list of spec fields.** It
   drives the admin meta box, the spec block on the project page, and the detail
   line in the index. Add a field there and all three follow. Location and year
   deliberately keep their unprefixed meta keys (`project_location`,
   `project_year`) because live data is already stored under them.
3. **`--zinc` has no contrast headroom on a washed ground.** It is 4.9:1 on
   limewash, so *any* tint behind it fails AA — a 3% wash lands at 4.75, the 7%
   `--accent-wash` on a hovered row at 4.47. The hovered row raises the detail
   line to `--slate` instead. Don't put `--zinc` text on `--accent-wash`.

**Two judgement calls, worth knowing they were calls:**

- The design demo's index is text only. The rows here carry a 7.5rem thumbnail
  column, because a builder's projects page with no photographs on it reads as
  broken to a client. Rows without an image get a hatched placeholder tile.
- `single-project.php` lost its hero meta line. Location and year now appear once,
  in the spec block, rather than twice within a screen height.

**Still open, adjacent to this work:** `.project-detail-body` is a centred 800px
column while `.project-detail-header` and the new `.project-spec` are in the
bounded-band system, so the prose starts at a different left edge from everything
above it. Pre-existing, but the spec band makes it obvious. Reconcile in Batch 3.

**Cleanup, partially available:** the inline `style="transition-delay:…"`
attributes were five in `front-page.php` and one in `page-about.php` — Batch 2
removed the two in `page-projects.php`, and Batch 3 has now removed the five
in `front-page.php` (they were already dead under the `!important` override
below; this was hygiene, not a behavior change). One remains, in
`page-about.php`, which no batch covers — so the
`transition-delay: 0s !important` override in `style.css` still has to stay
until someone clears that one too.

## Batch 3 — homepage restructure (in progress, uncommitted)

| # | Change | File | Status |
|---|---|---|---|
| 13 | New hero photo (real Inkim Suites render, replacing the generic stock photo) | `front-page.php` | Done |
| — | Cleared the five inline `transition-delay` attributes in `front-page.php` (see Batch 2's note — they were already dead under the `!important` override; this is hygiene, not a visual change) | `front-page.php` | Done |
| 14 | Delete the six services cards | `front-page.php`, `functions.php`, `style.css` | Done |
| 15 | Retire the stats bar | `front-page.php`, `functions.php`, `style.css` | Tried, reverted — see below |

**Tried and rolled back, at Emir's request (2 Aug 2026):**

- **Hero copy.** Rewrote the tagline/headline/sub-line to move off the generic
  "Est. in Excellence" / "WE BUILD YOUR VISION" boilerplate. Reverted — the
  hero now runs the original copy again, in both `functions.php`
  (`yaya_home_page_defaults()['hero']`, the single source for the front end
  and the admin meta-box placeholders) and `front-page.php`'s fallback
  literals. **The photo swap stayed** — `yaya_hero_image`'s default is still
  `.../uploads/2026/04/IMG_0103.png` (a real Inkim Suites render), not the old
  Unsplash stock photo. Only the text was in scope for the revert.
- **Featured-block selection.** Tried: skip candidates with an empty excerpt
  instead of always taking the single newest project, so Amsterdam (no
  description) wouldn't lead the homepage with filler text. Reverted — the
  query is back to "single newest non-coming-soon project," so Amsterdam is
  featured again with the canned filler paragraph, exactly as before Batch 3.
  **Kept:** the meta line still renders via `yaya_project_spec_rows()` (the
  Batch 2 field set) into its own `<p class="home-project-meta">` sibling,
  rather than a bare "location, year" string inline in the `<h2>` — this
  wasn't part of what was asked to revert, and it's a no-op for Amsterdam
  specifically (no location/year/scope/area/status set, so the row renders
  nothing either way). It matters the next time this block features a project
  that *has* those fields filled in.
  **CSS note, still relevant:** `.home-project-content p:not(.section-label)`
  is the body-copy rule for that column; it now also excludes
  `.home-project-meta` (`:not(.home-project-meta)`), or its higher specificity
  would overwrite the spec line's mono styling with body-copy styling once a
  project does have spec fields. Keep both exclusions if another paragraph
  type is added to that column.
- **Stats bar (item 15).** Removed the section, its Customizer/admin/save
  code, and the CSS, the same way the services cards were removed — see the
  commit for the full list if it's needed again. Reverted via `git revert` on
  request, so the stats bar is back exactly as it was: same markup, same
  `functions.php` fields, same CSS, nothing rebuilt from scratch. Not part of
  Batch 3 anymore — item 15 is back to not-started.

**Services cards, concretely (item 14, done):** removed the whole section
from `front-page.php` (the markup and its `$service_icons`/`$service_defaults`
setup), plus everything that fed it in `functions.php` — the `services` key
in `yaya_home_page_defaults()`, the Customizer `yaya_services` section and its
12 settings/controls, the "Services Section" block in the admin home-page
meta box, and its save-handler code. In `style.css`: removed `.services-grid`,
`.service-card` (base + `:nth-child`/`:hover` variants), `.service-icon` (+ the
`svg` sizing rule), `.service-title`, `.service-text`, and the now-fully-unused
`.section` wrapper class — including its entries in the two shared selector
lists (`padding-inline` bounded-grid list, and the running-text `max-width`
list). `.section-label`/`.section-title` **stayed** — they're generic classes
reused on every other page (about, contact, projects, single-project) and
were never services-specific. Also trimmed the tablet and mobile media
queries down to the rules that don't reference the removed classes, without
touching the unrelated `.home-project`/`.stat-item` tweaks living in the same
blocks.

**Hero image default, fixed (2 Aug 2026):** the photo swap in item 13
originally only updated `front-page.php`'s own `get_theme_mod()` fallback.
Three more places had their own hardcoded copy of the old Unsplash URL and
were still stale: `yaya_preload_hero_image()` in `functions.php` (this one had
real impact — it drives the `<link rel="preload">` LCP hint in `wp_head`, so
it was silently preloading a different image than the one actually rendered),
the Customizer's `yaya_hero_image` setting `default` (cosmetic — only visible
if an admin opens the Customizer without ever having set a custom image), and
the CSS `var(--hero-bg, url(...))` fallback in `style.css` (only fires if the
custom property is ever unset, which doesn't happen in practice since
`front-page.php` always sets it inline — but fixed for consistency). All four
now point at the same real photo. **Left alone, deliberately:** `.hero-bg`
also has an *earlier*, fully dead rule (`style.css` line ~243) still carrying
the old URL in a shorthand `background:` — a later rule's longhand
`background-image` already overrides it, so it renders nothing and predates
this work; not touched.

**Harness:** `tools/harness.py`'s `render_home()` (added for Batch 3, still
needed since the home page's markup no longer matches the fetched production
page) mirrors the current file: original hero copy with the new photo, the
stats bar restored, Amsterdam featured with the filler paragraph and no spec
line, no services section. Verified via `getComputedStyle`/DOM query — the
stats bar's four values render correctly, the hero background resolves to the
real photo, zero `.services-grid`/`.service-card` nodes, zero stale
`transition-delay` attributes.

## Batch 4 — performance & hygiene (done, uncommitted)

| Change | File |
|---|---|
| Register real image sizes, use them for `srcset` | `functions.php`, `front-page.php`, `page-projects.php`, `single-project.php` |
| Generate WebP sub-sizes for new uploads | `functions.php` |
| Disable WP's emoji detection script/style | `functions.php` |
| Drop dashicons for anonymous front-end visitors | `functions.php` |

**Image sizes + `srcset`, concretely:** the theme had exactly one registered
size (`project-thumb`, 800×600) and it was dead — nothing referenced it.
Registered three sized to how each context actually displays a photo (all
three are 4:3 in the CSS): `yaya-featured` (1200×900, home featured block),
`yaya-index-thumb` (240×180, project index row), `yaya-gallery` (640×480,
project gallery grid). `yaya_project_card_image()` (the featured-image / index
row resolver) now returns `['id' => attachment ID or null, 'url' => resolved
URL or '']` instead of a bare URL string — the ID is set for every resolution
path except the regex-extracted `<img src>` fallback (an arbitrary, possibly
external, URL pulled from post content, not necessarily a WP attachment). A
new `yaya_render_project_image($image, $size, $sizes_attr, $attrs = [])`
helper renders a real `wp_get_attachment_image()` (real `srcset`) when there's
an ID, and falls back to a plain `<img src>` when there's only a URL. All
three template call sites (`front-page.php` featured block, `page-projects.php`
index rows, `single-project.php` gallery) now go through this. The single-
project gallery loop already had attachment IDs directly (no
`yaya_project_card_image()` involved there) and switches straight to
`wp_get_attachment_image()`.

**Two things this doesn't do, and can't from a code change alone:**
- **New sizes don't apply retroactively.** All five projects' photos are
  already uploaded; WordPress doesn't regenerate existing attachments' sub-
  sizes just because a theme registers a new one. Until someone runs a
  regenerate-thumbnails pass (a plugin or `wp media regenerate` — routine WP
  maintenance, not a code change), `wp_get_attachment_image()` on these
  photos falls back to whatever sizes WordPress already generated at upload
  time (`thumbnail`, `medium`, `medium_large`, `large`, `full`) — which is
  still a real improvement over the single fixed URL every template rendered
  before, just not using the new custom sizes yet.
- **WebP generation depends on the server's image library.** The
  `image_editor_output_format` filter only takes effect if the PHP image
  library in use (GD or Imagick) actually supports encoding WebP — if it
  doesn't, WordPress silently keeps generating the original format. No way to
  check which the production server has from here.

**Emoji, concretely:** removed the two hooks that print WP's emoji-detection
script and inline style on every page load (`print_emoji_detection_script` on
`wp_head`, `print_emoji_styles` on `wp_print_styles`), plus the `s.w.org`
DNS-prefetch hint that went with them. Front-end only — didn't touch the
admin-side hooks, so the block editor's own emoji handling is unaffected. The
literal 📍 that motivated this item isn't in any of the five projects' current
content (checked directly) — doesn't matter; the detection script loads
unconditionally on every page regardless of whether the content actually
contains an emoji, so removing it is a real saving either way.

**Dashicons, concretely:** confirmed first, not assumed — grepped the
rendered HTML of all five page types (home, projects, project detail, about,
contact) for any element with a `dashicons` class. Zero, everywhere; the only
occurrence anywhere is the stylesheet `<link>` tag itself. It's enqueued by
two third-party plugins (`cookieadmin`, `socialfeeds`) as a dependency they
never actually draw an icon from. Dequeued at priority 100 on
`wp_enqueue_scripts`, gated on `!is_admin_bar_showing()` — logged-in visitors
with the admin toolbar visible keep it, since the toolbar's own icons need it.
Deregistering doesn't break the plugins' own stylesheets; WordPress's
dependency resolver just skips a dependency that no longer exists.

**Not verifiable via the harness, at all, this round:** none of these four
changes touch anything the harness actually executes. `tools/harness.py`
fakes the home and projects-index page bodies in pure Python (they don't run
`front-page.php`/`page-projects.php`), and the project-detail page only has
its spec block rewritten — the gallery section is whatever the *already-
fetched, pre-Batch-4* production HTML contains, untouched by that rewrite.
Emoji and dashicons are `wp_head`/enqueue-level output the harness's
`inject()` never touches either. Verification here is manual code review
only — re-read all three edited templates and the full `functions.php` diff
after making each change, twice, since there's still no `php` binary on this
machine.

## About + Contact pages — aligned with Batches 1–4 (done, uncommitted)

Not a numbered batch — a pass to bring `page-about.php`/`page-contact.php` in
line with what the four batches already established elsewhere, at Emir's
request. Scoped to `page-about.php`, `style.css`; `page-contact.php` needed no
changes (see below).

| Change | File |
|---|---|
| Cleared the last `transition-delay` inline style anywhere in the theme (the `.value-card` loop) | `page-about.php` |
| Removed the now-fully-unused `transition-delay: 0s !important` override from `.reveal` | `style.css` |
| Swapped the generic stock hero photos for real Inkim Suites renders | `style.css` |
| Removed `.contact-form-wrap` from the shared `padding-inline` list — dead: the template's actual class is `.contact-form`, this name was never used anywhere | `style.css` |

**Transition-delay, concretely:** `page-about.php`'s value-card loop was the
one instance HANDOFF flagged as "no batch covers" back when Batch 2 cleared
`page-projects.php` and Batch 3 cleared `front-page.php`. Confirmed via grep
across every template — zero remain anywhere — so the site-wide
`!important` override in `.reveal` (which existed purely to countermand
inline `transition-delay` values) is now provably dead weight and comes out
too. Transition-delay's browser default is already `0s`, so this changes
nothing observable — pure cleanup, not a behavior change.

**Hero photos, concretely:** `.about-hero` and `.contact-hero` had the same
issue the home hero had before Batch 3 — a generic Unsplash stock photo
hardcoded directly in the CSS `background:` shorthand, with no theme_mod or
per-page override at all (unlike the home hero, which reads `yaya_hero_image`).
Replaced both with real Inkim Suites photos — a different one per page, and
different from the home hero's, so the same building doesn't appear three
times in an obvious repeat: `IMG_0100.png` (poolside/amenities) for About,
`IMG_0098.png` (street-level facade) for Contact. Deliberately did **not**
add theme_mod configurability to match the home hero's mechanism — that's a
bigger feature than "align with what's already there," and nobody asked for
admin-configurable about/contact hero images. Copy on both pages is
untouched — this was scoped to photography and dead CSS, not prose, given
the hero-copy revert earlier in this project.

**Left alone, deliberately, out of the requested scope:** `page-projects.php`'s
own `.projects-hero` and `single-project.php`'s `.project-detail-hero` fallback
still carry stock Unsplash photos too — same issue, but Emir asked specifically
about the about and contact pages this time, not projects. `.hero-bg`'s
earlier, fully-dead rule (`style.css` line ~242, documented in the Batch 3
hero-image-fix note above) also still carries its own stale URL — untouched,
same reasoning as before: a later rule already overrides it, so it renders
nothing.

**Harness extended to cover this** (Emir asked to review before merging to
`main`): `tools/harness.py` didn't fetch or render About/Contact at all before
this — they linked straight to the live site. Added `build_static()`, which
just injects the working tree's CSS into the fetched page with no custom
rendering, the same treatment `build_home()` used before Batch 3 changed the
home page's own markup. **Caught a real bug writing it:** the fetched live
pages are still running the pre-this-branch template, so About's HTML still
had the four inline `transition-delay` attributes this pass removed —
injecting the new CSS (with the now-gone `!important` override) into that old
markup would have made the value cards visibly stagger in the harness, a
behavior the deployed code will never actually produce. Same failure shape
as HANDOFF's own stale-token warning below, just an attribute instead of a
custom property. Fixed with a small regex (`ABOUT_MARKUP_FIXES`) that strips
the attribute from the fetched HTML before injecting, so the harness matches
what actually ships rather than what's live right now. Verified after the
fix: `harness-about.html` and `harness-contact.html` both render with the
real hero photos and zero stale `transition-delay` attributes.

## Batches A, B, C — 2 Aug 2026 (committed on the branch)

A later round, batched by whether the work needed judgement. All three are
committed; none is deployed (only pushes to `main` deploy).

**Batch A — PHP hygiene** (`97add9c`). Four mechanical fixes.

| Change | File |
|---|---|
| Nested `<main>`: `header.php` already opens one and `footer.php` closes it, but `index.php`/`page.php` each opened their own inside it | `index.php`, `page.php` |
| `wp_enqueue_style` shipped a hardcoded `'2.0'` never bumped across four batches, so every deploy served stale CSS. Now keyed to the file's mtime | `functions.php` |
| Last two stock stand-in photos, in the featured block's fallbacks | `front-page.php` |
| `date('Y')` → `wp_date('Y')` (server TZ, not the site's); hardcoded site name → `get_bloginfo('name')` | `footer.php` |

**Batch B — one column down the project page** (`3af181b`). The page ran four
centring systems: header and spec on the bounded band, prose in a centred 800px
column, gallery in a centred 1100px one, back link on bare 3rem padding — four
left edges at 120 / 448 / 298 / 650 at 1600px. All three now sit on the shared
band with `padding-block` only; measured equal to within 0.5px across eleven
widths from 375 to 1920.

- The prose measure moved to `.project-detail-content`, already in the
  running-text `max-width` list — bounded as text (68ch) rather than by
  squeezing the band, so line length is one decision made once.
- Gallery keeps three columns, now a ~440px plate. **Not verifiable from here:**
  the harness renders placeholder SVGs and the uploads host is blocked, so
  whether the real phone photos (capped at 1024px) hold up at 440px wants a
  look on the live site.
- Back link is flush left; centring put it on an axis nothing else used.
- **`footer` had the shorthand bug the rules exist to prevent** — it was in the
  bounded-band `padding-inline` list but set `padding: 3rem` further down the
  file, which overrode it, so the footer sat 48px from the viewport edge while
  every band sat at 280px. Now `padding-block`.
- Last three stock photos gone; `.hero-bg`'s long-dead `background` shorthand
  deleted (its `background-repeat: no-repeat` was the one live declaration and
  moved to the rule that actually paints).
- Gallery images each carried the project title as alt, so a ten-photo project
  read the same words ten times. Now decorative, with the accessible name on the
  wrapping link as "View photo N of M".

**Batch C — the two interactive surfaces** (`9bc637b`). See the commit message
for the full list. The shape of it: the contact form's fields were not in a
`<form>` at all, errors were signalled by colour alone in the same hue as the
focus ring, and the success/error boxes were `display:none` divs that no screen
reader ever announced. The projects filter never set `aria-pressed`, announced
nothing when the list changed, and passed `behavior:'smooth'` regardless of
`prefers-reduced-motion` (a JS option beats the CSS property — the media query
at the top of the file does not cover it). Adds the site's first
`.visually-hidden` utility; both live regions need one.

**Three known-failing things Batch C found and deliberately did not fix** —
each is a design decision, not an a11y patch:

1. **Form field borders fail WCAG 1.4.11.** `--dust` on the white input fill is
   **1.37:1** against a 3:1 requirement. Every input boundary is effectively
   invisible as a UI component. Fixing it changes how the whole form looks.
2. **`.form-success` has no headroom** — `#2e7d32` on `#e8f5e9` is 4.56:1. It
   passes, but any tint drift breaks it. Its colours and the error box's are the
   only non-token literals left in the form; they predate the palette.
3. **3–4px horizontal overflow on the projects page at ≤430px**, from the
   off-canvas mobile nav panel in `header.php`. Pre-existing, unchanged by this
   work, and masked by `body { overflow-x: hidden }`.

## Content blockers — nobody's code can fix these

Verified by reading all five project pages on 2 Aug 2026.

| Page | Published right now |
|---|---|
| `/project/brussels` | Description is the literal string `sdsd`, with 5 photos under it |
| `/project/amsterdam` | No description at all. It's the newest non-coming-soon project, so the homepage featured block leads with it and falls back to filler text. A fix for this was tried in Batch 3 and rolled back — see Batch 3 notes above. |
| `/project/z-suites` | "Details for Z-Suites are coming soon." No images. |
| 3 of 5 projects | No location or year. The spec block now has five fields to fill and these three can fill none of them, so it doesn't render at all on Brussels, Amsterdam or Z-Suites, and their index rows show a bare name. The fields are in the editor now — **Projects → edit → Project Details** |
| Site title | Still "YAYA Construct website" — now the description on every inner page in search and every shared link |
| Voice | "Inkim Suites *is described as*…", "Güzelbahçe X *is presented as*…" — rewrite in first person |

Only Inkim Suites (48 words, 10 images) and Güzelbahçe X (83 words, 7 images) are complete.

**Brussels and Amsterdam descriptions — fixed in code (4 Aug 2026, revised same day):**
added `yaya_maybe_seed_brussels_amsterdam_descriptions()` in `functions.php`, following
the same one-time-migration shape as the Zabıtçı import and the city-category fixes above
it. Replaces Brussels' `sdsd` and Amsterdam's empty body with real copy (both ~50 m²
renovations, Brussels completed 2023, Amsterdam 2024), and fills in `project_location`,
`project_year`, and `_yaya_project_area` for both — location and area weren't set for
either project before this. **First shipped hooked on `admin_init` like its neighbors, but
that only fires inside `/wp-admin` — after deploy the descriptions stayed stale because
nobody had logged into wp-admin yet, while the (pure-code, no DB write) Benelux rename below
went live immediately.** Moved to `init` instead, the same hook this file already uses to
register the `project` post type and taxonomy, so it self-triggers on the next front-end
request with no admin visit required. Scope and status are still unset for both — no scope
was specified for Brussels, so it wasn't guessed. `z-suites` is the only remaining project
with no location/year and no real
description.

Photos are phone shots capped at 1024px, mixed orientation, some PNG. There's a
`Yaya CONSTRUCT Photos` folder beside the repo.

## How to see it — there is no local WordPress

```bash
python3 tools/harness.py --serve
```

Then open <http://localhost:8731/harness-home.html>. It fetches the live pages
once into `.harness/` (gitignored), rewrites them against the working tree, and
serves all seven pages — home / projects / three project pages / about /
contact — cross-linked so you can click between them. Re-run it after
editing `style.css`; add `--refetch` if production content changed. `tools/` is
outside the FTP mirror, so none of this can deploy.

Three rewrites do the work, and **each one, when missing, looked exactly like a
real bug**:

1. **Stylesheet** — strip production's CSS, inline `style.css` plus the branch's
   fonts (Archivo + Fragment Mono; production still requests Bebas/Barlow).
2. **Tokens** — `var(--rust)`→`var(--aegean)` etc. Production markup predates the
   rename and keeps those in `style=` attributes, where they resolve to nothing.
3. **Logo** — production serves `images/logo.png`, a dark mark on an *opaque*
   near-white square, which lands on the dark nav as a white sticker. The branch
   swapped it for `images/logo-mark.png` (real alpha, recoloured to limewash) in
   `header.php`. Miss this and the logo fix that shipped in Batch 1 looks like it
   never happened — it cost a round trip in Batch 2.

Injecting CSS into fetched HTML only works while the markup is unchanged. Where a
batch rewrote it, the harness renders that body from the live data instead —
currently the projects index, the spec block, and (as of Batch 3) the home
page's hero and featured block. Where the markup changed only slightly — the
about/contact alignment pass removed one inline attribute, nothing structural
— `build_static()`'s `markup_fixes` parameter patches the fetched HTML with a
small regex instead of a full custom render. Same principle as the token
rename below: production is running the old template until this deploys, so
its markup still has whatever the branch removed.

> **⚠️ Both claims in this section were wrong as of 2 Aug 2026 — read
> "Verification, corrected" below before following any of it.** `tools/harness.py`
> cannot fetch in the current environment, and there *is* a `php` binary.

Screenshots after programmatic scroll are unreliable in this setup — prefer
`getComputedStyle` assertions. A DOM-walking contrast audit is the fastest check;
treat any ancestor with a `background-image` as opaque or heroes over photos
produce false failures. Two more that paid off in Batch 2: sweep the breakpoints
in an offscreen `<iframe>` (the outer viewport stays put and you can assert
cell-rect collisions at ten widths in one pass), and composite `rgba()` hover
backgrounds over the body colour by hand rather than trying to hold a real
`:hover` across a JS round-trip, which doesn't survive.

## Verification, corrected (2 Aug 2026)

Two things this document told four batches of people, both false. They cost
real time, so they are corrected here rather than quietly edited above.

**1. There *is* a `php` binary.** `/usr/bin/php` is PHP 8.4.19. The claim that
there wasn't is why Batches 1–4 hand-read templates twice instead of linting,
shipping unchecked PHP straight to a live FTP deploy. **Run `php -l` on every
template you touch.** All ten currently lint clean.

It also makes a real server-side test possible without WordPress: `functions.php`
has no top-level statements except `add_action`/`add_filter`, so it can be
included against a small stub and its functions called directly. Batch C tested
`yaya_contact_form()` that way — nonce, honeypot, throttle, validation — with no
database and no HTTP.

**2. `tools/harness.py` does not work here.** The agent proxy denies CONNECT to
`yayaconstruct.com:443`, so `fetch_live()` 403s and there is nothing to inject
into. Every instruction above about `--serve` and `--refetch` is dead in this
environment. It is not broken code — it just cannot reach production.

What replaces it is better, and the reason is worth keeping: because `php`
exists, a harness can **execute the real templates** against a WordPress stub
instead of retyping their markup in Python. That removes the whole class of bug
the "three rewrites" section above exists to warn about — there is no fetched
production HTML to drift from the working tree, because the markup *is* the
working tree. Local webfonts and inline-SVG photos keep it off the network
entirely. Playwright lives at `/opt/node22/lib/node_modules/playwright` and must
launch with `executablePath: '/opt/pw-browsers/chromium'`; never run
`playwright install`.

Two traps found the hard way while building it, both of which produce confident
wrong answers rather than errors:

- **Rebuild before you re-measure.** A stale results JSON reported a fix as
  having no effect, twice, before anyone checked the timestamp.
- **Wait out the transitions.** Reading `getComputedStyle` colours mid-transition
  returns interpolated values — an error border measured at `rgb(171,58,51)`
  instead of `rgb(164,35,27)`, which is a different contrast ratio.

Screenshots after programmatic scroll remain unreliable; assert on
`getComputedStyle` / `getBoundingClientRect` instead.

**Measure things you are not changing.** The footer bug in Batch B was found
only because `footer` was included as a control column in a sweep of the project
page — four batches, including the one that wrote the rule it violated, had read
that file without spotting it.

## Design direction

Full write-up, with the palette, type specimen and a live grid-vs-list comparison:
https://claude.ai/code/artifact/be5d274c-d58c-4c4b-ba70-965c86db6c95
