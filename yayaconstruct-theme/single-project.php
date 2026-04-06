<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<?php
  $location  = get_post_meta( get_the_ID(), 'project_location', true );
  $year      = get_post_meta( get_the_ID(), 'project_year',     true );
  $cats      = get_the_terms( get_the_ID(), 'project_category' );
  $cat_name  = $cats ? $cats[0]->name : '';
  $img       = get_the_post_thumbnail_url( get_the_ID(), 'large' );
?>

<!-- Hero -->
<div class="project-detail-hero"<?php if ( $img ) : ?> style="--project-bg: url('<?php echo esc_url( $img ); ?>')"<?php endif; ?>>
  <div class="project-detail-bg"></div>
  <div class="project-detail-header">
    <?php if ( $cat_name ) : ?>
      <div class="section-label" style="color:var(--rust)"><?php echo esc_html( $cat_name ); ?></div>
    <?php endif; ?>
    <h1><?php the_title(); ?></h1>
    <?php if ( $location || $year ) : ?>
      <p class="project-detail-meta">
        <?php echo esc_html( $location ); ?>
        <?php if ( $location && $year ) : ?> &middot; <?php endif; ?>
        <?php echo esc_html( $year ); ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<!-- Content -->
<?php if ( get_the_content() ) : ?>
<div class="project-detail-body">
  <div class="project-detail-content">
    <?php the_content(); ?>
  </div>
</div>
<?php endif; ?>

<!-- Back link -->
<div class="project-detail-back">
  <a href="<?php echo home_url( '/projects' ); ?>" class="btn-outline">&larr; Back to All Projects</a>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
