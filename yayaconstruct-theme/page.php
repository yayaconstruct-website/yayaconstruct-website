<?php
/**
 * Default page template — routes to page-{slug}.php if it exists.
 * This handles the case where WordPress pages are set to "Default Template"
 * instead of explicitly selecting the named template.
 */
$slug     = get_post_field('post_name', get_the_ID());
$specific = get_template_directory() . '/page-' . $slug . '.php';

if ($slug && file_exists($specific)) {
    include($specific);
} else {
    get_header();
    get_footer();
}
