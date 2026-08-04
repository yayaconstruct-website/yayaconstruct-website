<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<?php
  $cats         = get_the_terms( get_the_ID(), 'project_category' );
  $cat_name     = $cats ? $cats[0]->name : '';
  $img          = get_the_post_thumbnail_url( get_the_ID(), 'large' );
  $coming_soon  = (bool) get_post_meta( get_the_ID(), '_yaya_project_coming_soon', true );
  $gallery_ids  = get_post_meta( get_the_ID(), '_yaya_project_gallery', true );
  $gallery_ids  = $gallery_ids ? array_filter( array_map( 'absint', explode( ',', $gallery_ids ) ) ) : [];
  // Resolve the full-size URLs up front. An attachment that has since been
  // deleted drops out here rather than mid-loop, so the "photo N of M" labels
  // below count the photos that actually render, and an all-deleted gallery
  // no longer prints an empty grid.
  $gallery_items = [];
  if ( ! $coming_soon ) {
    foreach ( $gallery_ids as $attachment_id ) {
      $full = wp_get_attachment_image_url( $attachment_id, 'large' );
      if ( $full ) {
        $gallery_items[] = [ 'id' => $attachment_id, 'url' => $full ];
      }
    }
  }
  $gallery_total = count( $gallery_items );
  // Location and year moved out of the hero and into the spec block below, so
  // the same two values no longer appear twice within a screen height.
  $spec_rows    = function_exists( 'yaya_project_spec_rows' ) ? yaya_project_spec_rows( get_the_ID() ) : [];
?>

<!-- Hero -->
<div class="project-detail-hero"<?php if ( $img ) : ?> style="--project-bg: url('<?php echo esc_url( $img ); ?>')"<?php endif; ?>>
  <div class="project-detail-bg"></div>
  <div class="project-detail-header">
    <?php if ( $cat_name ) : ?>
      <div class="section-label" style="color:var(--aegean)"><?php echo esc_html( $cat_name ); ?></div>
    <?php endif; ?>
    <h1><?php the_title(); ?></h1>
    <?php if ( $coming_soon ) : ?>
      <div class="project-coming-soon-badge">Coming Soon</div>
    <?php endif; ?>
  </div>
</div>

<!-- Spec -->
<?php if ( $spec_rows ) : ?>
<div class="project-spec">
  <dl class="project-spec-grid">
    <?php foreach ( $spec_rows as $row ) : ?>
      <div class="project-spec-item">
        <dt><?php echo esc_html( $row['label'] ); ?></dt>
        <dd><?php echo esc_html( $row['value'] ); ?></dd>
      </div>
    <?php endforeach; ?>
  </dl>
</div>
<?php endif; ?>

<!-- Content -->
<?php if ( get_the_content() ) : ?>
<div class="project-detail-body">
  <div class="project-detail-content">
    <?php the_content(); ?>
  </div>
</div>
<?php endif; ?>

<!-- Gallery -->
<?php if ( $gallery_items ) : ?>
<div class="project-gallery">
  <div class="project-gallery-grid" id="project-gallery-grid">
    <?php
    // The photographs are decorative here: the <h1> above already names the
    // project, so repeating it as alt text once per image only makes a screen
    // reader read the same words ten times over. The accessible name belongs
    // on the wrapping link instead — it goes somewhere, and with an empty alt
    // it would otherwise have no name at all.
    //
    // The href stays a real link to the full-size file. With JS the script
    // below intercepts the click and opens the viewer in place; without it,
    // the link still works exactly as it did before. Nothing here depends on
    // the script having run.
    foreach ( $gallery_items as $i => $item ) :
    ?>
      <a class="project-gallery-item"
         href="<?php echo esc_url( $item['url'] ); ?>"
         data-index="<?php echo esc_attr( $i ); ?>"
         target="_blank"
         rel="noopener"
         aria-label="<?php echo esc_attr( sprintf( 'View photo %d of %d', $i + 1, $gallery_total ) ); ?>">
        <?php echo wp_get_attachment_image( $item['id'], 'yaya-gallery', false, [
          'alt'      => '',
          'loading'  => 'lazy',
          'decoding' => 'async',
          'sizes'    => '(max-width: 768px) 50vw, 33vw',
        ] ); ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php
/* The viewer.

   A <dialog> rather than a div, for three things it gives for free and one it
   avoids. Free: focus is trapped inside it, Escape closes it, the rest of the
   page goes inert, and ::backdrop is a real element. Avoided: the nav is
   position:fixed at z-index 200 with a backdrop-filter, which makes it a
   stacking context — a plain overlay has to out-index that, and every previous
   overlay on this site has had to fight it. showModal() puts the dialog in the
   top layer, which is above the whole stacking hierarchy by definition, so
   there is no z-index to get wrong.

   Rendered only when there is a gallery, and left empty until it opens: the
   <img> has no src, so nothing here costs a request unless someone clicks. */
?>
<dialog class="lightbox" id="project-lightbox" aria-label="<?php echo esc_attr( sprintf( '%s photos', get_the_title() ) ); ?>">
  <div class="lightbox-stage">
    <button type="button" class="lightbox-close" data-lightbox-close aria-label="Close photo viewer">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 5l14 14M19 5L5 19" /></svg>
    </button>

    <?php /* Hidden from AT when there is only one photo — the buttons are
             removed from the DOM in that case by the script, but the markup
             is written for the common case. */ ?>
    <button type="button" class="lightbox-nav lightbox-prev" data-lightbox-prev aria-label="Previous photo">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 4L7 12l8 8" /></svg>
    </button>

    <figure class="lightbox-figure">
      <img class="lightbox-img" alt="" decoding="async" />
      <figcaption class="lightbox-caption" data-lightbox-caption></figcaption>
    </figure>

    <button type="button" class="lightbox-nav lightbox-next" data-lightbox-next aria-label="Next photo">
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 4l8 8-8 8" /></svg>
    </button>
  </div>

  <?php /* The caption is visible text, but it is not announced when it changes
           — a <figcaption> is not a live region. Photo N of M is the only
           feedback a screen-reader user gets that the arrow key did anything,
           so it is mirrored here as well. */ ?>
  <p class="visually-hidden" role="status" aria-live="polite" data-lightbox-status></p>
</dialog>

<script>
(function () {
  var dialog = document.getElementById('project-lightbox');
  var grid   = document.getElementById('project-gallery-grid');
  if (!dialog || !grid) { return; }

  // No showModal, no interception: every link keeps its href and opens the
  // full-size file the way it always did. Checked before anything is wired up
  // so a browser without <dialog> never lands in a half-built viewer.
  if (typeof dialog.showModal !== 'function') { return; }

  var links   = Array.prototype.slice.call(grid.querySelectorAll('.project-gallery-item'));
  if (!links.length) { return; }

  var img     = dialog.querySelector('.lightbox-img');
  var caption = dialog.querySelector('[data-lightbox-caption]');
  var status  = dialog.querySelector('[data-lightbox-status]');
  var prevBtn = dialog.querySelector('[data-lightbox-prev]');
  var nextBtn = dialog.querySelector('[data-lightbox-next]');
  var total   = links.length;
  var current = 0;
  var opener  = null;

  // One photo needs no way to get to a second one.
  if (total < 2) {
    if (prevBtn) { prevBtn.remove(); prevBtn = null; }
    if (nextBtn) { nextBtn.remove(); nextBtn = null; }
  }

  // Decode the neighbours off-screen so stepping through is instant rather
  // than a blank frame per photo. Browser cache does the rest.
  function preload(i) {
    if (total < 2) { return; }
    [(i + 1) % total, (i - 1 + total) % total].forEach(function (n) {
      var pre = new Image();
      pre.src = links[n].href;
    });
  }

  function show(i) {
    current = (i + total) % total;
    var label = 'Photo ' + (current + 1) + ' of ' + total;

    // The class comes off before the src changes and goes back on in the load
    // handler, so each photo fades in on arrival instead of the previous one
    // sitting there under the new caption while the next decodes.
    img.classList.remove('is-loaded');
    img.src = links[current].href;
    caption.textContent = label;
    status.textContent = label;
    preload(current);
  }

  img.addEventListener('load', function () { img.classList.add('is-loaded'); });

  function open(i) {
    opener = links[i];
    show(i);
    dialog.showModal();
    // Scrollbar compensation: showModal locks the page, and without this the
    // layout jumps by the scrollbar width behind the backdrop.
    var gap = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.overflow = 'hidden';
    if (gap > 0) { document.body.style.paddingRight = gap + 'px'; }
  }

  grid.addEventListener('click', function (e) {
    var link = e.target.closest('.project-gallery-item');
    if (!link || !grid.contains(link)) { return; }
    // Leave the modified clicks alone — a middle-click or cmd-click is someone
    // asking for a new tab on purpose, and the href still points at the file.
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) { return; }
    e.preventDefault();
    open(links.indexOf(link));
  });

  if (prevBtn) { prevBtn.addEventListener('click', function () { show(current - 1); }); }
  if (nextBtn) { nextBtn.addEventListener('click', function () { show(current + 1); }); }

  dialog.querySelectorAll('[data-lightbox-close]').forEach(function (btn) {
    btn.addEventListener('click', function () { dialog.close(); });
  });

  // Clicking the backdrop closes. The backdrop is not a child element, so
  // there is nothing to bind to — a click that lands on the dialog itself
  // rather than on the stage inside it is a backdrop click.
  dialog.addEventListener('click', function (e) {
    if (e.target === dialog) { dialog.close(); }
  });

  dialog.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowRight') { e.preventDefault(); show(current + 1); }
    if (e.key === 'ArrowLeft')  { e.preventDefault(); show(current - 1); }
  });

  // Fires for the close button, the backdrop and Escape alike, so unlocking
  // the page and giving focus back lives here rather than in three handlers.
  dialog.addEventListener('close', function () {
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    img.removeAttribute('src');
    img.classList.remove('is-loaded');
    status.textContent = '';
    if (opener) { opener.focus(); opener = null; }
  });
})();
</script>
<?php endif; ?>

<!-- Back link -->
<div class="project-detail-back">
  <a href="<?php echo home_url( '/projects' ); ?>" class="btn-outline">&larr; Back to All Projects</a>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
