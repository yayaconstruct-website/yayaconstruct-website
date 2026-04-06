<?php

/* ─────────────────────────────────────────
   AUTO-CREATE REQUIRED PAGES
───────────────────────────────────────── */
function yaya_create_pages() {
    $pages = [
        [ 'title' => 'Home',     'slug' => 'home',     'template' => '' ],
        [ 'title' => 'About Us', 'slug' => 'about',    'template' => 'page-about.php' ],
        [ 'title' => 'Projects', 'slug' => 'projects', 'template' => 'page-projects.php' ],
        [ 'title' => 'Contact',  'slug' => 'contact',  'template' => 'page-contact.php' ],
    ];

    foreach ($pages as $p) {
        if (!get_page_by_path($p['slug'])) {
            wp_insert_post([
                'post_title'  => $p['title'],
                'post_name'   => $p['slug'],
                'post_status' => 'publish',
                'post_type'   => 'page',
                'meta_input'  => $p['template'] ? ['_wp_page_template' => $p['template']] : [],
            ]);
        }
    }
}
add_action('after_switch_theme', 'yaya_create_pages');

/* ─────────────────────────────────────────
   FORCE PAGE TEMPLATES BY SLUG
───────────────────────────────────────── */
function yaya_force_templates($template) {
    $map = [
        'projects' => 'page-projects.php',
        'about'    => 'page-about.php',
        'contact'  => 'page-contact.php',
    ];
    foreach ($map as $slug => $file) {
        $path = get_template_directory() . '/' . $file;
        if (is_page($slug) && file_exists($path)) {
            return $path;
        }
    }
    return $template;
}
add_filter('template_include', 'yaya_force_templates');

/* ─────────────────────────────────────────
   THEME SETUP
───────────────────────────────────────── */
function yaya_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('project-thumb', 800, 600, true);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 160,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
}
add_action('after_setup_theme', 'yaya_setup');

/* ─────────────────────────────────────────
   ENQUEUE STYLES & SCRIPTS
───────────────────────────────────────── */
function yaya_scripts() {
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@400;600;700&display=swap',
        [], null
    );
    wp_enqueue_style('yaya-style', get_stylesheet_uri(), ['google-fonts'], '1.2');
}
add_action('wp_enqueue_scripts', 'yaya_scripts');

/* ─────────────────────────────────────────
   CUSTOM POST TYPE: PROJECT
───────────────────────────────────────── */
function yaya_register_cpt() {
    register_post_type('project', [
        'labels' => [
            'name'          => 'Projects',
            'singular_name' => 'Project',
            'add_new'       => 'Add New Project',
            'add_new_item'  => 'Add New Project',
            'edit_item'     => 'Edit Project',
            'all_items'     => 'All Projects',
            'menu_name'     => 'Projects',
        ],
        'public'       => true,
        'show_in_menu' => true,
        'supports'     => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'menu_icon'    => 'dashicons-building',
        'has_archive'  => false,
    ]);
}
add_action('init', 'yaya_register_cpt');

/* ─────────────────────────────────────────
   TAXONOMY: PROJECT CATEGORY
───────────────────────────────────────── */
function yaya_register_taxonomy() {
    register_taxonomy('project_category', 'project', [
        'labels' => [
            'name'          => 'Project Categories',
            'singular_name' => 'Category',
            'add_new_item'  => 'Add New Category',
        ],
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
    ]);
}
add_action('init', 'yaya_register_taxonomy');

/* ─────────────────────────────────────────
   CONTACT FORM AJAX (with nonce)
───────────────────────────────────────── */
function yaya_contact_form() {
    check_ajax_referer('yaya_contact_nonce', 'nonce');
    $name    = sanitize_text_field($_POST['name']    ?? '');
    $email   = sanitize_email($_POST['email']        ?? '');
    $phone   = sanitize_text_field($_POST['phone']   ?? '');
    $type    = sanitize_text_field($_POST['type']    ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json(['success' => false, 'error' => 'Missing required fields']);
    }

    $to      = get_theme_mod('yaya_contact_email', 'info@yayaconstruct.com');
    $subject = 'New Message from ' . $name . ' – Yaya Construct';
    $body    = "Name: $name\nEmail: $email\nPhone: $phone\nProject Type: $type\n\nMessage:\n$message";
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: $name <$email>",
    ];

    $sent = wp_mail($to, $subject, $body, $headers);
    wp_send_json(['success' => $sent]);
}
add_action('wp_ajax_nopriv_yaya_contact', 'yaya_contact_form');
add_action('wp_ajax_yaya_contact',        'yaya_contact_form');

/* ─────────────────────────────────────────
   WORDPRESS CUSTOMIZER
───────────────────────────────────────── */
function yaya_customizer($wp_customize) {

    /* ── PANEL ── */
    $wp_customize->add_panel('yaya_panel', [
        'title'    => 'Yaya Construct Settings',
        'priority' => 10,
    ]);

    /* ══════════════ HERO SECTION ══════════════ */
    $wp_customize->add_section('yaya_hero', [
        'title' => 'Hero Section',
        'panel' => 'yaya_panel',
    ]);

    $hero_fields = [
        'yaya_hero_tag'   => ['label' => 'Tag Line',          'default' => 'Est. in Excellence'],
        'yaya_hero_line1' => ['label' => 'Heading — Line 1',  'default' => 'WE'],
        'yaya_hero_line2' => ['label' => 'Heading — Line 2 (Rust colour)', 'default' => 'BUILD'],
        'yaya_hero_line3' => ['label' => 'Heading — Line 3',  'default' => 'YOUR VISION'],
        'yaya_hero_sub'   => ['label' => 'Subtext',           'default' => 'From groundbreaking to grand opening — Yaya Construct delivers construction that lasts generations.'],
        'yaya_hero_cta1'  => ['label' => 'Button 1 Text',     'default' => 'View Our Work'],
        'yaya_hero_cta2'  => ['label' => 'Button 2 Text',     'default' => 'Get a Quote'],
    ];

    foreach ($hero_fields as $key => $args) {
        $wp_customize->add_setting($key, [
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control($key, [
            'label'   => $args['label'],
            'section' => 'yaya_hero',
            'type'    => 'text',
        ]);
    }

    // Hero background image
    $wp_customize->add_setting('yaya_hero_image', [
        'default'           => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1600&q=80',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'yaya_hero_image', [
        'label'   => 'Hero Background Image',
        'section' => 'yaya_hero',
    ]));

    /* ══════════════ STATS SECTION ══════════════ */
    $wp_customize->add_section('yaya_stats', [
        'title' => 'Stats Bar',
        'panel' => 'yaya_panel',
    ]);

    $stats_defaults = [
        ['150+', 'Projects Completed'],
        ['12+',  'Years of Experience'],
        ['98%',  'Client Satisfaction'],
        ['40+',  'Skilled Professionals'],
    ];

    for ($i = 1; $i <= 4; $i++) {
        $n = $i - 1;
        $wp_customize->add_setting("yaya_stat{$i}_num", [
            'default'           => $stats_defaults[$n][0],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("yaya_stat{$i}_num", [
            'label'   => "Stat $i — Number",
            'section' => 'yaya_stats',
            'type'    => 'text',
        ]);
        $wp_customize->add_setting("yaya_stat{$i}_label", [
            'default'           => $stats_defaults[$n][1],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("yaya_stat{$i}_label", [
            'label'   => "Stat $i — Label",
            'section' => 'yaya_stats',
            'type'    => 'text',
        ]);
    }

    /* ══════════════ CONTACT DETAILS ══════════════ */
    $wp_customize->add_section('yaya_contact_details', [
        'title' => 'Contact Details',
        'panel' => 'yaya_panel',
    ]);

    $contact_fields = [
        'yaya_contact_address1' => ['label' => 'Address Line 1',      'default' => '123 Construction Ave'],
        'yaya_contact_address2' => ['label' => 'Address Line 2',      'default' => 'Building District, City 10001'],
        'yaya_contact_phone'    => ['label' => 'Phone Number',         'default' => '+1 (555) 000-0000'],
        'yaya_contact_email'    => ['label' => 'Contact Email',        'default' => 'info@yayaconstruct.com'],
        'yaya_contact_hours1'   => ['label' => 'Hours — Weekday',     'default' => 'Mon–Fri: 7:00 AM – 6:00 PM'],
        'yaya_contact_hours2'   => ['label' => 'Hours — Saturday',    'default' => 'Sat: 8:00 AM – 2:00 PM'],
    ];

    foreach ($contact_fields as $key => $args) {
        $wp_customize->add_setting($key, [
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($key, [
            'label'   => $args['label'],
            'section' => 'yaya_contact_details',
            'type'    => 'text',
        ]);
    }

    /* ══════════════ SERVICES SECTION ══════════════ */
    $wp_customize->add_section('yaya_services', [
        'title' => 'Services',
        'panel' => 'yaya_panel',
    ]);

    $service_defaults = [
        1 => ['General Construction', 'Full-cycle construction management from planning to handover, delivered on time and within budget.'],
        2 => ['Commercial Buildings', 'Office complexes, retail centers, warehouses, and industrial facilities built to the highest standards.'],
        3 => ['Residential Projects', 'Custom homes, apartment buildings, and residential renovations crafted with care and precision.'],
        4 => ['Renovation & Refit',   'Breathing new life into existing structures with expert renovation, retrofitting, and restoration work.'],
        5 => ['Design & Build',       'Integrated design-build solutions combining architectural vision with construction expertise under one roof.'],
        6 => ['Project Management',   'Professional oversight, scheduling, and coordination for complex multi-phase construction projects.'],
    ];

    for ($i = 1; $i <= 6; $i++) {
        $wp_customize->add_setting("yaya_service{$i}_title", [
            'default'           => $service_defaults[$i][0],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("yaya_service{$i}_title", [
            'label'   => "Service $i — Title",
            'section' => 'yaya_services',
            'type'    => 'text',
        ]);
        $wp_customize->add_setting("yaya_service{$i}_text", [
            'default'           => $service_defaults[$i][1],
            'sanitize_callback' => 'sanitize_textarea_field',
        ]);
        $wp_customize->add_control("yaya_service{$i}_text", [
            'label'   => "Service $i — Description",
            'section' => 'yaya_services',
            'type'    => 'textarea',
        ]);
    }

    /* ══════════════ TEAM SECTION ══════════════ */
    $wp_customize->add_section('yaya_team', [
        'title' => 'Team Members',
        'panel' => 'yaya_panel',
    ]);

    $team_defaults = [
        1 => ['Yaya Diallo',  'Founder & CEO',    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
        2 => ['Sarah Mensah', 'Head of Projects',  'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80'],
        3 => ['Marc Koné',    'Lead Engineer',     'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80'],
    ];

    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting("yaya_team{$i}_name", [
            'default'           => $team_defaults[$i][0],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("yaya_team{$i}_name", [
            'label'   => "Member $i — Name",
            'section' => 'yaya_team',
            'type'    => 'text',
        ]);
        $wp_customize->add_setting("yaya_team{$i}_role", [
            'default'           => $team_defaults[$i][1],
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control("yaya_team{$i}_role", [
            'label'   => "Member $i — Role",
            'section' => 'yaya_team',
            'type'    => 'text',
        ]);
        $wp_customize->add_setting("yaya_team{$i}_photo", [
            'default'           => $team_defaults[$i][2],
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "yaya_team{$i}_photo", [
            'label'   => "Member $i — Photo",
            'section' => 'yaya_team',
        ]));
    }
}
add_action('customize_register', 'yaya_customizer');
