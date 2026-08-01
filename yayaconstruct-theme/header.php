<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php // The fonts are render-blocking and third-party; warm the connections early. ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <?php
  // Open Graph & Twitter Card
  if ( is_singular() ) {
    $og_title = get_the_title() . ' — ' . get_bloginfo( 'name' );
    $og_desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30, '…' );
    $og_url   = get_permalink();
    $og_img   = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '';
    // A static front page is singular too, so the home page was being tagged
    // as an article and shared with an empty description — all of its copy
    // lives in post meta, not post content.
    $og_type  = is_front_page() ? 'website' : 'article';

    if ( is_front_page() ) {
      $og_title = get_bloginfo( 'name' ) . ( get_bloginfo( 'description' ) ? ' — ' . get_bloginfo( 'description' ) : '' );
      $og_url   = home_url( '/' );

      if ( ! trim( $og_desc ) && function_exists( 'yaya_get_home_page_field' ) ) {
        $home_defaults = function_exists( 'yaya_home_page_defaults' ) ? yaya_home_page_defaults() : [];
        $og_desc = yaya_get_home_page_field(
          get_queried_object_id(),
          '_yaya_home_hero_sub',
          get_theme_mod( 'yaya_hero_sub', $home_defaults['hero']['sub'] ?? '' )
        );
      }
    }
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
  // Last resort so no page ships an empty description.
  if ( ! trim( (string) $og_desc ) ) {
    $og_desc = get_bloginfo( 'description' );
  }
  $og_desc = trim( wp_strip_all_tags( (string) $og_desc ) );
  ?>
  <?php // Search engines had no description to work with — only Open Graph tags. ?>
  <?php if ( $og_desc ) : ?>
  <meta name="description"        content="<?php echo esc_attr( $og_desc ); ?>">
  <?php endif; ?>
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
  <?php // .reveal starts at opacity:0 and only JS turns it on, so without
        // scripting everything below the hero rendered as a blank page. ?>
  <noscript>
    <style>.reveal { opacity: 1 !important; transform: none !important; }</style>
  </noscript>
</head>
<body <?php body_class(); ?>>

<?php // Keyboard and screen-reader users had to tab the whole nav on every page. ?>
<a class="skip-link" href="#main-content">Skip to main content</a>

<nav id="main-nav" aria-label="Main">

  <!-- Logo -->
  <?php if (has_custom_logo()): ?>
    <div class="nav-logo"><?php the_custom_logo(); ?></div>
  <?php else: ?>
    <a class="nav-logo" href="<?php echo home_url('/'); ?>">
      <?php // Transparent, limewash-on-nothing variant so the mark sits on the
            // bar itself. Intrinsic size declared so the nav does not reflow. ?>
      <img src="<?php echo get_template_directory_uri(); ?>/images/logo-mark.png"
           alt="<?php bloginfo('name'); ?>"
           width="189" height="107"
           fetchpriority="high" decoding="async"
           class="nav-logo-img" />
    </a>
  <?php endif; ?>

  <!-- Desktop & mobile nav links -->
  <ul class="nav-links" id="nav-links">
    <li><a href="<?php echo home_url('/'); ?>"         <?php if (is_front_page())     echo 'class="active" aria-current="page"'; ?>>Home</a></li>
    <li><a href="<?php echo home_url('/about'); ?>"    <?php if (is_page('about'))    echo 'class="active" aria-current="page"'; ?>>About Us</a></li>
    <li><a href="<?php echo home_url('/projects'); ?>" <?php if (is_page('projects')) echo 'class="active" aria-current="page"'; ?>>Projects</a></li>
    <li><a href="<?php echo home_url('/contact'); ?>"  <?php if (is_page('contact'))  echo 'class="active" aria-current="page"'; ?>>Contact</a></li>
  </ul>

  <!-- Hamburger (mobile only) -->
  <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="nav-links">
    <span></span>
    <span></span>
    <span></span>
  </button>

</nav>

<!-- Overlay behind mobile menu -->
<div class="nav-overlay" id="nav-overlay"></div>

<main id="main-content">

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
    // The label described the button's icon, not the action it performs, so it
    // still said "Open menu" while the menu was open.
    hamburger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    document.body.style.overflow = open ? 'hidden' : '';
  }

  hamburger.addEventListener('click', function () {
    toggleMenu(!navLinks.classList.contains('open'));
  });
  overlay.addEventListener('click', function () { toggleMenu(false); });
  navLinks.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () { toggleMenu(false); });
  });

  // Escape is the expected way out of an overlay menu; without it the only exit
  // was hitting the hamburger or the overlay.
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && navLinks.classList.contains('open')) {
      toggleMenu(false);
      hamburger.focus();
    }
  });
})();
</script>
