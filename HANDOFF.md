# Redesign handoff — state as of 2 Aug 2026

Working notes for the yayaconstruct.com redesign. Delete before merging to `main`
if you don't want it in the repo.

## Where things stand

| Branch | Contains | Deployed? |
|---|---|---|
| `main` @ `c96a7b7` | Accessibility + performance + SEO passes | **Yes — live** |
| `redesign/batch-1-reskin` @ `9c76390` | Batch 1 re-skin, logo + menu fixes | No |

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

## Batch 2 — project index (next up)

| # | Change | File |
|---|---|---|
| 8 | Replace the 3-column card grid with an indexed list | `page-projects.php` |
| 9 | Group by Aegean / Low Countries instead of per-city counts | `page-projects.php`, `functions.php` |
| 10 | Mono spec block (location · year · scope · area · status) | `single-project.php` |
| 11 | Admin fields for scope / area / status | `functions.php` |
| 12 | Retune the sticky filter bar to the new grouping | `page-projects.php` |

Rationale: five items in a three-column grid leaves a visible empty slot — the
layout announces that work is missing. An indexed list of five reads as a curated
catalogue. Grouping by region also hides that Amsterdam and Brussels are one
project each, and states the international position.

**Known bug to fix as part of this:** a single-project group renders at `21/9`
inside the two-column grid between 769–1100px, so the card is badly squashed.
`.project-group-grid--single` (0,1,0) loses to the `@media (max-width:1100px)`
rule on `.project-group-grid` (same specificity, later in file). Predates the branch.

**Cleanup available:** Batch 2 touches the templates that carry inline
`style="transition-delay:…"` on `.reveal`. Once removed, drop the
`transition-delay: 0s !important` in `style.css` that currently overrides them.

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
| 3 of 5 projects | No location or year, so the meta line renders empty and the Batch 2 spec block would have nothing to show |
| Site title | Still "YAYA Construct website" — now the description on every inner page in search and every shared link |
| Voice | "Inkim Suites *is described as*…", "Güzelbahçe X *is presented as*…" — rewrite in first person |

Only Inkim Suites (48 words, 10 images) and Güzelbahçe X (83 words, 7 images) are complete.

Photos are phone shots capped at 1024px, mixed orientation, some PNG. There's a
`Yaya CONSTRUCT Photos` folder beside the repo.

## How to verify — there is no local WordPress

Build a harness from production HTML and inject the working stylesheet:

1. `curl` the four page types into `live-*.html`
2. Strip `<link rel=stylesheet>` and `<script src>`; inline `style.css` + the Google Fonts link
3. **Apply the token rename to the fetched HTML** (`--rust`→`--aegean` etc.) — production
   markup predates it, and stale inline `var(--rust)` renders as invisible
   inherited colour. This bit me once and looked exactly like a real bug.
4. Add a script that adds `.visible` to every `.reveal`
5. Serve it and drive the browser against it

Screenshots after programmatic scroll are unreliable in this setup — prefer
`getComputedStyle` assertions. A DOM-walking contrast audit is the fastest check;
treat any ancestor with a `background-image` as opaque or heroes over photos
produce false failures.

## Design direction

Full write-up, with the palette, type specimen and a live grid-vs-list comparison:
https://claude.ai/code/artifact/be5d274c-d58c-4c4b-ba70-965c86db6c95
