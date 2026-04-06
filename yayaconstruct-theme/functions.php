<?php
function yaya_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('project-thumb', 800, 600, true);
}
add_action('after_setup_theme', 'yaya_setup');

function yaya_scripts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@400;600;700&display=swap', [], null);
    wp_enqueue_style('yaya-style', get_stylesheet_uri(), ['google-fonts'], '1.0');
}
add_action('wp_enqueue_scripts', 'yaya_scripts');

// Register Project Custom Post Type
function yaya_register_cpt() {
    register_post_type('project', [
        'labels' => [
            'name' => 'Projects',
            'singular_name' => 'Project',
            'add_new' => 'Add New Project',
            'add_new_item' => 'Add New Project',
            'edit_item' => 'Edit Project',
            'all_items' => 'All Projects',
            'menu_name' => 'Projects',
        ],
        'public' => true,
        'show_in_menu' => true,
        'supports' => ['title', 'thumbnail', 'custom-fields'],
        'menu_icon' => 'dashicons-building',
        'has_archive' => false,
    ]);
}
add_action('init', 'yaya_register_cpt');

// Register Project Category Taxonomy
function yaya_register_taxonomy() {
    register_taxonomy('project_category', 'project', [
        'labels' => [
            'name' => 'Project Categories',
            'singular_name' => 'Category',
            'add_new_item' => 'Add New Category',
        ],
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
    ]);
}
add_action('init', 'yaya_register_taxonomy');

// Handle contact form AJAX
function yaya_contact_form() {
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $to = 'info@yayaconstruct.com';
    $subject = 'New Contact Form Message – Yaya Construct';
    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = ['Content-Type: text/plain; charset=UTF-8', "Reply-To: $email"];
    $sent = wp_mail($to, $subject, $body, $headers);
    wp_send_json(['success' => $sent]);
}
add_action('wp_ajax_nopriv_yaya_contact', 'yaya_contact_form');
add_action('wp_ajax_yaya_contact', 'yaya_contact_form');
