# Redesign handoff — state as of 2 Aug 2026 (Batch 2 landed in the working tree)

Working notes for the yayaconstruct.com redesign. Delete before merging to `main`
if you don't want it in the repo.

## Where things stand

| Branch | Contains | Deployed? |
|---|---|---|
| `main` @ `c96a7b7` | Accessibility + performance + SEO passes | **Yes — live** |
| `redesign/batch-1-reskin` @ `9c76390` | Batch 1 re-skin, logo + menu fixes | No |
| working tree | Batch 2 project index — **uncommitted** | No |

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

## Batch 2 — project index (done, uncommitted)

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

**Cleanup NOT available yet** (the old note here was wrong): the inline
`style="transition-delay:…"` attributes are five in `front-page.php` and one in
`page-about.php` — Batch 2 only removed the two in `page-projects.php`. The
`transition-delay: 0s !important` in `style.css` has to stay until Batch 3 clears
`front-page.php` *and* someone clears `page-about.php`, which no batch covers.

## Batches 3 and 4 (not started)

- **3 — Homepage restructure:** new hero, delete the six services cards, retire the
  stats bar, rework the featured block. *Items 14 and 15 are business decisions —
  confirm before shipping.*
- **4 — Performance & hygiene:** `srcset` + WebP, disable WP emoji (a 📍 in project
  copy loads Twemoji SVGs), drop front-end dashicons (35 KB), register image sizes.

## Content blockers — nobody's code can fix these

Verified by reading all five project pages on 2 Aug 2026.

| Page | Published right now |
|---|---|
| `/project/brussels` | Description is the literal string `sdsd`, with 5 photos under it |
| `/project/amsterdam` | No description at all — which is why the homepage featured block falls back to filler. This is the project the homepage leads with. |
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
currently the projects index and the spec block. Batch 3 will need the same for
`front-page.php`.

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
