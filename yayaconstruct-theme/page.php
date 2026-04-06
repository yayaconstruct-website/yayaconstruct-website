<?php
get_header();

if (have_posts()) :
  while (have_posts()) :
    the_post();
    ?>
    <main class="page-wrap">
      <article <?php post_class('default-page-content'); ?>>
        <header class="default-page-header">
          <div class="section-label" style="color:var(--rust)">Page</div>
          <h1><?php the_title(); ?></h1>
        </header>

        <div class="default-page-body">
          <?php the_content(); ?>
        </div>
      </article>
    </main>
    <?php
  endwhile;
endif;

get_footer();
