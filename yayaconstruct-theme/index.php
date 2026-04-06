<?php get_header(); ?>

<main class="page-wrap">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article <?php post_class('default-page-content'); ?>>
        <?php if (!is_front_page()) : ?>
          <header class="default-page-header">
            <div class="section-label" style="color:var(--rust)">Page</div>
            <h1><?php the_title(); ?></h1>
          </header>
        <?php endif; ?>

        <div class="default-page-body">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <article class="default-page-content">
      <header class="default-page-header">
        <div class="section-label" style="color:var(--rust)">Not Found</div>
        <h1>Content unavailable</h1>
      </header>
      <div class="default-page-body">
        <p>The requested page could not be found.</p>
      </div>
    </article>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
