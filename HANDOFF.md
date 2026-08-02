# Redesign handoff — state as of 2 Aug 2026 (Batch 3 partially landed)

Working notes for the yayaconstruct.com redesign. Delete before merging to `main`
if you don't want it in the repo.

## Where things stand

| Branch | Contains | Deployed? |
|---|---|---|
| `main` @ `c96a7b7` | Accessibility + performance + SEO passes | **Yes — live** |
| `redesign/batch-1-reskin` @ `9c76390` | Batch 1 re-skin, logo + menu fixes | No |
| `redesign/batch-1-reskin` @ `aaabf6e` | Batch 2 project index | No |
| working tree | Batch 3, Sonnet-scoped items — **uncommitted** | No |

**Batch 3 status:** the hero photo swap, the services-card removal, and the
`front-page.php` transition-delay cleanup are done. The hero copy, the
featured-block selection logic, and retiring the stats bar (item 15) were
all tried and then rolled back at Emir's request — see "Batch 3" below. Item
15 is the one item from the original four still open.

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

## Batch 4 — performance & hygiene (not started)

`srcset` + WebP, disable WP emoji (a 📍 in project copy loads Twemoji SVGs), drop
front-end dashicons (35 KB), register image sizes.

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

Photos are phone shots capped at 1024px, mixed orientation, some PNG. There's a
`Yaya CONSTRUCT Photos` folder beside the repo.

## How to see it — there is no local WordPress

```bash
python3 tools/harness.py --serve
```

Then open <http://localhost:8731/harness-home.html>. It fetches the live pages
once into `.harness/` (gitignored), rewrites them against the working tree, and
serves home / projects / three project pages, cross-linked so you can click
between them. About and Contact still point at the live site. Re-run it after
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
page's hero and featured block.

Screenshots after programmatic scroll are unreliable in this setup — prefer
`getComputedStyle` assertions. A DOM-walking contrast audit is the fastest check;
treat any ancestor with a `background-image` as opaque or heroes over photos
produce false failures. Two more that paid off in Batch 2: sweep the breakpoints
in an offscreen `<iframe>` (the outer viewport stays put and you can assert
cell-rect collisions at ten widths in one pass), and composite `rgba()` hover
backgrounds over the body colour by hand rather than trying to hold a real
`:hover` across a JS round-trip, which doesn't survive.

There is no `php` binary on this machine and no Docker daemon running, so
template changes get no syntax check before they reach the server. Read the PHP
twice.

## Design direction

Full write-up, with the palette, type specimen and a live grid-vs-list comparison:
https://claude.ai/code/artifact/be5d274c-d58c-4c4b-ba70-965c86db6c95
