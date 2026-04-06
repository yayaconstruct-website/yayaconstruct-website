<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
  // Open Graph & Twitter Card
  if ( is_singular() ) {
    $og_title = get_the_title() . ' — ' . get_bloginfo( 'name' );
    $og_desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30, '…' );
    $og_url   = get_permalink();
    $og_img   = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '';
    $og_type  = 'article';
  } else {
    $og_title = get_bloginfo( 'name' ) . ( get_bloginfo( 'description' ) ? ' — ' . get_bloginfo( 'description' ) : '' );
    $og_desc  = get_bloginfo( 'description' );
    $og_url   = home_url( '/' );
    $og_img   = '';
    $og_type  = 'website';
  }
  if ( ! $og_img && has_custom_logo() ) {
    $logo_id = get_theme_mod( 'custom_logo' );
    $logo    = wp_get_attachment_image_src( $logo_id, 'full' );
    $og_img  = $logo ? $logo[0] : '';
  }
  ?>
  <meta property="og:type"        content="<?php echo esc_attr( $og_type ); ?>">
  <meta property="og:title"       content="<?php echo esc_attr( $og_title ); ?>">
  <meta property="og:description" content="<?php echo esc_attr( $og_desc ); ?>">
  <meta property="og:url"         content="<?php echo esc_url( $og_url ); ?>">
  <meta property="og:site_name"   content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
  <?php if ( $og_img ) : ?>
  <meta property="og:image"       content="<?php echo esc_url( $og_img ); ?>">
  <?php endif; ?>
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?php echo esc_attr( $og_title ); ?>">
  <meta name="twitter:description" content="<?php echo esc_attr( $og_desc ); ?>">
  <?php if ( $og_img ) : ?>
  <meta name="twitter:image"       content="<?php echo esc_url( $og_img ); ?>">
  <?php endif; ?>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<nav id="main-nav">

  <!-- Logo -->
  <?php if (has_custom_logo()): ?>
    <div class="nav-logo"><?php the_custom_logo(); ?></div>
  <?php else: ?>
    <a class="nav-logo" href="<?php echo home_url('/'); ?>">
      <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png"
           alt="<?php bloginfo('name'); ?>"
           class="nav-logo-img" />
    </a>
  <?php endif; ?>

  <!-- Desktop & mobile nav links -->
  <ul class="nav-links" id="nav-links">
    <li><a href="<?php echo home_url('/'); ?>"         <?php if (is_front_page())     echo 'class="active"'; ?>>Home</a></li>
    <li><a href="<?php echo home_url('/about'); ?>"    <?php if (is_page('about'))    echo 'class="active"'; ?>>About Us</a></li>
    <li><a href="<?php echo home_url('/projects'); ?>" <?php if (is_page('projects')) echo 'class="active"'; ?>>Projects</a></li>
    <li><a href="<?php echo home_url('/contact'); ?>"  <?php if (is_page('contact'))  echo 'class="active"'; ?>>Contact</a></li>
  </ul>

  <!-- Hamburger (mobile only) -->
  <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
    <span></span>
    <span></span>
    <span></span>
  </button>

</nav>

<!-- Overlay behind mobile menu -->
<div class="nav-overlay" id="nav-overlay"></div>

<script>
(function () {
  var hamburger = document.getElementById('hamburger');
  var navLinks  = document.getElementById('nav-links');
  var overlay   = document.getElementById('nav-overlay');

  function toggleMenu(open) {
    hamburger.classList.toggle('open', open);
    navLinks.classList.toggle('open', open);
    overlay.classList.toggle('open', open);
    hamburger.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  }

  hamburger.addEventListener('click', function () {
    toggleMenu(!navLinks.classList.contains('open'));
  });
  overlay.addEventListener('click', function () { toggleMenu(false); });
  navLinks.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () { toggleMenu(false); });
  });
})();
</script>
