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

**Batch 3 is split across two models.** New hero, the featured-block rework, and
the `front-page.php` transition-delay cleanup were done under Claude Sonnet 5 —
mechanical/content work with a clear spec. Deleting the six services cards and
retiring the stats bar (items 14–15) are held for Claude Opus 5: they remove
content that makes claims about the business, and Emir asked to review those
specifically before they're shipped. See "Batch 3" below for the exact split.

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

| # | Change | File | Model |
|---|---|---|---|
| 13 | New hero: real project photo + copy grounded in the Aegean/Low Countries positioning, replacing the generic stock photo and "Est. in Excellence" tagline | `front-page.php`, `functions.php` | Sonnet 5 — done |
| — | Featured block: skip empty-excerpt candidates instead of always taking the single newest project; meta line now uses the Batch 2 spec-field system instead of a bare location/year string | `front-page.php` | Sonnet 5 — done |
| — | Cleared the five inline `transition-delay` attributes in `front-page.php` (see Batch 2's note — they were already dead under the `!important` override; this is hygiene, not a visual change) | `front-page.php` | Sonnet 5 — done |
| 14 | Delete the six services cards | `front-page.php`, `functions.php`, `style.css` | **Opus 5 — reserved** |
| 15 | Retire the stats bar | `front-page.php`, `functions.php`, `style.css` | **Opus 5 — reserved** |

Items 14 and 15 are still business decisions — they remove content that makes
claims about the business (what services are offered, the stats bar's trust
signals) — and still need Emir's sign-off before shipping, independent of
which model does the implementation.

**New hero, concretely:**

- `yaya_hero_image` default is now `.../uploads/2026/04/IMG_0103.png` — a real
  Inkim Suites render (the Aegean coastline, palm trees, sailboats), pulled
  from the project's own live page rather than uploaded fresh. Still
  overridable via the Customizer.
- Copy (`yaya_home_page_defaults()['hero']` in `functions.php`, single source
  for both the front end and the admin meta-box placeholders): tag "Two
  Latitudes, One Standard", headline "BUILDING / ACROSS / TWO LATITUDES", and
  a sub-line naming the two regions instead of the old "lasts generations"
  boilerplate. No layout or CSS change — the hero was already fully wired into
  the Batch 1 bounded-grid and dark-band systems.

**Featured-block rework, concretely:**

- The query now pulls every non-coming-soon project (was: just the single
  newest) and picks the first one, in date order, with a non-empty
  `get_the_excerpt()`. This is the same emptiness check the block already had —
  just applied across candidates instead of only the newest one — so it can't
  regress the "nothing has content" case, which still falls through to the
  existing empty-state copy.
  **Why:** Amsterdam is the newest non-coming-soon project and has no
  description at all, so before this change the homepage's most visible slot
  led with the canned filler paragraph. Confirmed live: `get_the_excerpt()` on
  Amsterdam returns empty (both `post_excerpt` and `post_content` are empty),
  so this isn't guessing — it's the same emptiness signal the block already
  trusted, just checked before committing to a project instead of after.
  **Not fully verifiable locally:** which project the fix actually lands on
  depends on `post_date` ordering among Güzelbahçe X / Brussels / Inkim
  Suites, which isn't exposed by REST or the sitemap (only `post_modified` is,
  and it doesn't match `post_date` order here) — no `php` binary or DB access
  to check directly. Read the live result after this deploys.
- The meta line under the title now renders `yaya_project_spec_rows()` (the
  same Batch 2 field set — location · year · scope · area · status) instead of
  a bare "location, year" string, and moved from inline text inside the `<h2>`
  to its own `<p class="home-project-meta">` sibling — same fix Batch 2 made
  on the single-project page, for the same reason (a location caption
  shouldn't be part of the heading's accessible name).
  **CSS note:** `.home-project-content p:not(.section-label)` is the body-copy
  rule for that column; it now also excludes `.home-project-meta`
  (`:not(.home-project-meta)`), or its higher specificity would overwrite the
  spec line's mono styling with body-copy styling. Keep both exclusions if
  another paragraph type is added to that column.

**Harness:** `tools/harness.py` now renders the home page's hero and featured
block from hardcoded real data (`render_home()`), the same approach
`render_index()`/`render_spec()` already used for the projects page — injecting
CSS into the fetched production markup stopped being enough once the markup
itself changed. The featured project shown in the harness (Inkim Suites) is
illustrative — real content, but not a claim about which project the live
"skip empty excerpts" logic will land on (see above).

## Batch 4 — performance & hygiene (not started)

`srcset` + WebP, disable WP emoji (a 📍 in project copy loads Twemoji SVGs), drop
front-end dashicons (35 KB), register image sizes.

## Content blockers — nobody's code can fix these

Verified by reading all five project pages on 2 Aug 2026.

| Page | Published right now |
|---|---|
| `/project/brussels` | Description is the literal string `sdsd`, with 5 photos under it |
| `/project/amsterdam` | No description at all. Was the newest non-coming-soon project, so it used to be what the homepage featured block led with (falling back to filler text); Batch 3's featured-block fix now skips it for a project with real content — see Batch 3 notes above. |
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
