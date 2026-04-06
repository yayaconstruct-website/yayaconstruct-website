<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<nav>
  <a class="logo" href="<?php echo home_url('/'); ?>">YAYA<span>.</span>CONSTRUCT</a>
  <ul class="nav-links">
    <li><a href="<?php echo home_url('/'); ?>" <?php if(is_front_page()) echo 'class="active"'; ?>>Home</a></li>
    <li><a href="<?php echo home_url('/about'); ?>" <?php if(is_page('about')) echo 'class="active"'; ?>>About Us</a></li>
    <li><a href="<?php echo home_url('/projects'); ?>" <?php if(is_page('projects')) echo 'class="active"'; ?>>Projects</a></li>
    <li><a href="<?php echo home_url('/contact'); ?>" <?php if(is_page('contact')) echo 'class="active"'; ?>>Contact</a></li>
  </ul>
</nav>
