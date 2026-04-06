<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<nav id="main-nav">

  <!-- Logo -->
  <a class="nav-logo" href="<?php echo home_url('/'); ?>">
    <?php if (has_custom_logo()): ?>
      <?php the_custom_logo(); ?>
    <?php else: ?>
      <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png"
           alt="<?php bloginfo('name'); ?>"
           class="nav-logo-img" />
    <?php endif; ?>
  </a>

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
