<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<?php
  $cats         = get_the_terms( get_the_ID(), 'project_category' );
  $cat_name     = $cats ? $cats[0]->name : '';
  $img          = get_the_post_thumbnail_url( get_the_ID(), 'large' );
  $coming_soon  = (bool) get_post_meta( get_the_ID(), '_yaya_project_coming_soon', true );
  $gallery_ids  = get_post_meta( get_the_ID(), '_yaya_project_gallery', true );
  $gallery_ids  = $gallery_ids ? array_filter( array_map( 'absint', explode( ',', $gallery_ids ) ) ) : [];
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
<?php if ( ! $coming_soon && ! empty( $gallery_ids ) ) : ?>
<div class="project-gallery">
  <div class="project-gallery-grid">
    <?php foreach ( $gallery_ids as $attachment_id ) :
      $full  = wp_get_attachment_image_url( $attachment_id, 'large' );
      $thumb = wp_get_attachment_image_url( $attachment_id, 'medium' );
      if ( ! $full ) continue;
    ?>
      <a class="project-gallery-item" href="<?php echo esc_url( $full ); ?>" target="_blank" rel="noopener">
        <img src="<?php echo esc_url( $thumb ?: $full ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Back link -->
<div class="project-detail-back">
  <a href="<?php echo home_url( '/projects' ); ?>" class="btn-outline">&larr; Back to All Projects</a>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
