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
    if (!is_page()) {
        return $template;
    }

    $page = get_queried_object();
    if (!$page instanceof WP_Post) {
        return $template;
    }

    $page_slug  = $page->post_name;
    $page_title = sanitize_title($page->post_title);
    $map = [
        'projects' => ['page-projects.php', ['projects', 'our-projects', 'portfolio']],
        'about'    => ['page-about.php',    ['about', 'about-us', 'our-story']],
        'contact'  => ['page-contact.php',  ['contact', 'contact-us', 'get-in-touch']],
    ];

    foreach ($map as $config) {
        [$file, $aliases] = $config;
        $path = get_template_directory() . '/' . $file;

        if (file_exists($path) && (in_array($page_slug, $aliases, true) || in_array($page_title, $aliases, true))) {
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
    add_post_type_support('page', 'excerpt');
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
   ONE-TIME PROJECT IMPORT: ZABITCI SOURCE
───────────────────────────────────────── */
function yaya_zabitci_projects_seed_data() {
    return [
        [
            'title'      => 'Guzelbahce X',
            'slug'       => 'guzelbahce-x',
            'location'   => 'Guzelbahce, Izmir',
            'year'       => '2018',
            'category'   => 'Villa',
            'source_url' => 'https://www.zabitci.com/proje-guzelbahce-x.html',
            'content'    => implode("\n\n", [
                'Guzelbahce X is presented as a villa project that combines the comfort of site living with the privacy of an independent home.',
                'The source project page highlights 10 villas in a 4+1 concept, each with approximately 240 square meters of enclosed area, private gardens, and a layout designed to balance calm green surroundings with quick access to city life.',
                'The project emphasizes generous spacing between villas, landscaped outdoor areas, strong family suitability, and proximity to schools, shopping, restaurants, and Izmir\'s wider coastal destinations.',
            ]),
        ],
        [
            'title'      => 'NO3 Mavisehir',
            'slug'       => 'no3-mavisehir',
            'location'   => 'Mavisehir, Izmir',
            'year'       => '',
            'category'   => 'Residential',
            'source_url' => 'https://www.zabitci.com/projelerimiz.html',
            'content'    => implode("\n\n", [
                'NO3 Mavisehir is listed on the Zabıtçı projects page as a residential project shaped by modern architecture.',
                'The source describes it as a stylish development created for buyers seeking an exclusive home in one of the city\'s most prestigious neighborhoods.',
            ]),
        ],
        [
            'title'      => 'NO17 Bayrakli',
            'slug'       => 'no17-bayrakli',
            'location'   => 'Bayrakli, Izmir',
            'year'       => '2014',
            'category'   => 'Residential',
            'source_url' => 'https://www.zabitci.com/proje-no17.html',
            'content'    => implode("\n\n", [
                'NO17 Bayrakli is positioned in Izmir\'s developing new city center and is described by the source as a symbol of urban living in Bayrakli.',
                'The project page presents it as a design-led residential development with a distinctive identity, focused on modern city life and premium detailing.',
            ]),
        ],
        [
            'title'      => 'Alacati Qu4ttro',
            'slug'       => 'alacati-qu4ttro',
            'location'   => 'Alacati, Cesme',
            'year'       => '',
            'category'   => 'Villa',
            'source_url' => 'https://www.zabitci.com/projelerimiz.html',
            'content'    => implode("\n\n", [
                'Alacati Qu4ttro is featured on the Zabıtçı projects page as a project that reinterprets the distinctive spirit of Alacati through a modern architectural language.',
                'The listing frames it as a lifestyle-focused residential development designed to turn aspiration into everyday living.',
            ]),
        ],
        [
            'title'      => 'Inkim Suites',
            'slug'       => 'inkim-suites',
            'location'   => 'Ilica, Cesme',
            'year'       => '2021',
            'category'   => 'Residence',
            'source_url' => 'https://zabitci.com/proje-inkim-suites.html',
            'content'    => implode("\n\n", [
                'Inkim Suites is described as the transformation of the long-standing Inkim Hotel into a refreshed residence concept in the heart of Ilica, Cesme.',
                'The source highlights Zabıtçı\'s renovation work, a central location close to beaches and amenities, and a residence program built around comfort, design, and year-round use.',
            ]),
        ],
        [
            'title'      => 'E&E Villa',
            'slug'       => 'ee-villa',
            'location'   => '',
            'year'       => '',
            'category'   => 'Villa',
            'source_url' => 'https://www.zabitci.com/projelerimiz.html',
            'content'    => implode("\n\n", [
                'E&E Villa is listed on the source projects page as a villa development carrying the Zabıtçı signature.',
                'The listing positions it as a project created for buyers who expect a high-end residential standard and a more exclusive home experience.',
            ]),
        ],
    ];
}

function yaya_maybe_import_zabitci_projects() {
    if (get_option('yaya_zabitci_projects_imported_v1')) {
        return;
    }

    if (!post_type_exists('project') || !taxonomy_exists('project_category')) {
        return;
    }

    $projects = yaya_zabitci_projects_seed_data();

    foreach ($projects as $project) {
        $existing = get_posts([
            'post_type'      => 'project',
            'name'           => $project['slug'],
            'post_status'    => ['publish', 'draft', 'pending', 'private'],
            'numberposts'    => 1,
            'fields'         => 'ids',
            'suppress_filters' => false,
        ]);

        if (!empty($existing)) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_type'    => 'project',
            'post_status'  => 'publish',
            'post_title'   => $project['title'],
            'post_name'    => $project['slug'],
            'post_content' => $project['content'],
        ], true);

        if (is_wp_error($post_id)) {
            continue;
        }

        if (!empty($project['location'])) {
            update_post_meta($post_id, 'project_location', $project['location']);
        }

        if (!empty($project['year'])) {
            update_post_meta($post_id, 'project_year', $project['year']);
        }

        update_post_meta($post_id, '_yaya_project_source', 'zabitci');
        update_post_meta($post_id, '_yaya_project_source_url', esc_url_raw($project['source_url']));

        if (!empty($project['category'])) {
            wp_set_object_terms($post_id, $project['category'], 'project_category', false);
        }
    }

    update_option('yaya_zabitci_projects_imported_v1', gmdate('c'));
}
add_action('admin_init', 'yaya_maybe_import_zabitci_projects');

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

/* ─────────────────────────────────────────
   ABOUT PAGE EDITOR FIELDS
───────────────────────────────────────── */
function yaya_about_page_defaults() {
    return [
        'values' => [
            1 => [
                'title' => 'Quality First',
                'text'  => 'We never cut corners. Every joint, every pour, every finish is done right because your structure deserves nothing less.',
            ],
            2 => [
                'title' => 'Integrity',
                'text'  => 'Honest pricing, transparent timelines, and clear communication from day one to handover. No surprises.',
            ],
            3 => [
                'title' => 'Innovation',
                'text'  => 'We stay current with modern building techniques and materials to deliver solutions that are both durable and forward-thinking.',
            ],
            4 => [
                'title' => 'Community',
                'text'  => 'We build in communities we care about. Supporting local suppliers and creating opportunities for local talent is at our core.',
            ],
        ],
        'team' => [
            1 => [
                'name'  => 'Yaya Diallo',
                'role'  => 'Founder & CEO',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80',
            ],
            2 => [
                'name'  => 'Sarah Mensah',
                'role'  => 'Head of Projects',
                'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80',
            ],
            3 => [
                'name'  => 'Marc Koné',
                'role'  => 'Lead Engineer',
                'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80',
            ],
        ],
    ];
}

function yaya_get_about_page_field($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function yaya_add_about_meta_box() {
    add_meta_box(
        'yaya_about_details',
        'About Page Details',
        'yaya_render_about_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'yaya_add_about_meta_box');

function yaya_render_about_meta_box($post) {
    $template = get_page_template_slug($post->ID);
    $slug     = $post->post_name;

    if ($template !== 'page-about.php' && !in_array($slug, ['about', 'about-us', 'our-story'], true)) {
        echo '<p>This panel is used by the About page. Assign the About template to this page to use these fields.</p>';
        return;
    }

    $defaults = yaya_about_page_defaults();
    wp_nonce_field('yaya_about_meta_box', 'yaya_about_meta_nonce');

    echo '<p>Use the regular page title, excerpt, and content editor for the hero heading, intro text, and main story. Use the fields below for Values and Team.</p>';
    echo '<style>
        .yaya-meta-grid{display:grid;gap:16px}
        .yaya-meta-section{border:1px solid #dcdcde;padding:16px;background:#fff}
        .yaya-meta-row{display:grid;gap:12px;grid-template-columns:1fr 2fr;margin-bottom:12px}
        .yaya-meta-row:last-child{margin-bottom:0}
        .yaya-meta-row label{font-weight:600}
        .yaya-meta-row input,.yaya-meta-row textarea{width:100%}
      </style>';

    echo '<div class="yaya-meta-grid">';

    echo '<div class="yaya-meta-section"><h3>Values Section</h3>';
    for ($i = 1; $i <= 4; $i++) {
        $title = yaya_get_about_page_field($post->ID, "_yaya_about_value_{$i}_title", $defaults['values'][$i]['title']);
        $text  = yaya_get_about_page_field($post->ID, "_yaya_about_value_{$i}_text", $defaults['values'][$i]['text']);
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_about_value_' . $i . '_title">Value ' . $i . ' Title</label>';
        echo '<input type="text" id="yaya_about_value_' . $i . '_title" name="yaya_about_value_' . $i . '_title" value="' . esc_attr($title) . '">';
        echo '</div>';
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_about_value_' . $i . '_text">Value ' . $i . ' Description</label>';
        echo '<textarea rows="3" id="yaya_about_value_' . $i . '_text" name="yaya_about_value_' . $i . '_text">' . esc_textarea($text) . '</textarea>';
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="yaya-meta-section"><h3>Team Section</h3>';
    for ($i = 1; $i <= 3; $i++) {
        $name  = yaya_get_about_page_field($post->ID, "_yaya_about_team_{$i}_name", $defaults['team'][$i]['name']);
        $role  = yaya_get_about_page_field($post->ID, "_yaya_about_team_{$i}_role", $defaults['team'][$i]['role']);
        $photo = yaya_get_about_page_field($post->ID, "_yaya_about_team_{$i}_photo", $defaults['team'][$i]['photo']);
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_about_team_' . $i . '_name">Member ' . $i . ' Name</label>';
        echo '<input type="text" id="yaya_about_team_' . $i . '_name" name="yaya_about_team_' . $i . '_name" value="' . esc_attr($name) . '">';
        echo '</div>';
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_about_team_' . $i . '_role">Member ' . $i . ' Role</label>';
        echo '<input type="text" id="yaya_about_team_' . $i . '_role" name="yaya_about_team_' . $i . '_role" value="' . esc_attr($role) . '">';
        echo '</div>';
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_about_team_' . $i . '_photo">Member ' . $i . ' Photo URL</label>';
        echo '<input type="url" id="yaya_about_team_' . $i . '_photo" name="yaya_about_team_' . $i . '_photo" value="' . esc_attr($photo) . '" placeholder="https://">';
        echo '</div>';
    }
    echo '</div>';

    echo '</div>';
}

function yaya_save_about_meta_box($post_id) {
    if (!isset($_POST['yaya_about_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yaya_about_meta_nonce'])), 'yaya_about_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    for ($i = 1; $i <= 4; $i++) {
        if (isset($_POST["yaya_about_value_{$i}_title"])) {
            update_post_meta($post_id, "_yaya_about_value_{$i}_title", sanitize_text_field(wp_unslash($_POST["yaya_about_value_{$i}_title"])));
        }
        if (isset($_POST["yaya_about_value_{$i}_text"])) {
            update_post_meta($post_id, "_yaya_about_value_{$i}_text", sanitize_textarea_field(wp_unslash($_POST["yaya_about_value_{$i}_text"])));
        }
    }

    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["yaya_about_team_{$i}_name"])) {
            update_post_meta($post_id, "_yaya_about_team_{$i}_name", sanitize_text_field(wp_unslash($_POST["yaya_about_team_{$i}_name"])));
        }
        if (isset($_POST["yaya_about_team_{$i}_role"])) {
            update_post_meta($post_id, "_yaya_about_team_{$i}_role", sanitize_text_field(wp_unslash($_POST["yaya_about_team_{$i}_role"])));
        }
        if (isset($_POST["yaya_about_team_{$i}_photo"])) {
            update_post_meta($post_id, "_yaya_about_team_{$i}_photo", esc_url_raw(wp_unslash($_POST["yaya_about_team_{$i}_photo"])));
        }
    }
}
add_action('save_post_page', 'yaya_save_about_meta_box');

/* ─────────────────────────────────────────
   PROJECTS PAGE EDITOR FIELDS
───────────────────────────────────────── */
function yaya_projects_page_defaults() {
    return [
        'hero_label'   => 'Portfolio',
        'filter_label' => 'All Projects',
        'empty_state'  => 'Our portfolio is being updated. Check back soon.',
    ];
}

function yaya_get_projects_page_field($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function yaya_add_projects_meta_box() {
    add_meta_box(
        'yaya_projects_details',
        'Projects Page Details',
        'yaya_render_projects_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'yaya_add_projects_meta_box');

function yaya_render_projects_meta_box($post) {
    $template = get_page_template_slug($post->ID);
    $slug     = $post->post_name;

    if ($template !== 'page-projects.php' && !in_array($slug, ['projects', 'our-projects', 'portfolio'], true)) {
        echo '<p>This panel is used by the Projects page. Assign the Projects template to this page to use these fields.</p>';
        return;
    }

    $defaults = yaya_projects_page_defaults();
    wp_nonce_field('yaya_projects_meta_box', 'yaya_projects_meta_nonce');

    echo '<p>Use the regular page title, excerpt, and content editor for the hero heading, intro text, and optional content block above the project grid. Use the fields below for the remaining Projects page copy.</p>';
    echo '<style>
        .yaya-meta-grid{display:grid;gap:16px}
        .yaya-meta-section{border:1px solid #dcdcde;padding:16px;background:#fff}
        .yaya-meta-row{display:grid;gap:12px;grid-template-columns:1fr 2fr;margin-bottom:12px}
        .yaya-meta-row:last-child{margin-bottom:0}
        .yaya-meta-row label{font-weight:600}
        .yaya-meta-row input,.yaya-meta-row textarea{width:100%}
      </style>';

    $hero_label = yaya_get_projects_page_field($post->ID, '_yaya_projects_hero_label', $defaults['hero_label']);
    $filter_label = yaya_get_projects_page_field($post->ID, '_yaya_projects_filter_label', $defaults['filter_label']);
    $empty_state = yaya_get_projects_page_field($post->ID, '_yaya_projects_empty_state', $defaults['empty_state']);

    echo '<div class="yaya-meta-grid">';
    echo '<div class="yaya-meta-section"><h3>Projects Page Copy</h3>';
    echo '<div class="yaya-meta-row">';
    echo '<label for="yaya_projects_hero_label">Hero Label</label>';
    echo '<input type="text" id="yaya_projects_hero_label" name="yaya_projects_hero_label" value="' . esc_attr($hero_label) . '">';
    echo '</div>';
    echo '<div class="yaya-meta-row">';
    echo '<label for="yaya_projects_filter_label">All Filter Label</label>';
    echo '<input type="text" id="yaya_projects_filter_label" name="yaya_projects_filter_label" value="' . esc_attr($filter_label) . '">';
    echo '</div>';
    echo '<div class="yaya-meta-row">';
    echo '<label for="yaya_projects_empty_state">Empty State Message</label>';
    echo '<textarea rows="3" id="yaya_projects_empty_state" name="yaya_projects_empty_state">' . esc_textarea($empty_state) . '</textarea>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function yaya_save_projects_meta_box($post_id) {
    if (!isset($_POST['yaya_projects_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yaya_projects_meta_nonce'])), 'yaya_projects_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['yaya_projects_hero_label'])) {
        update_post_meta($post_id, '_yaya_projects_hero_label', sanitize_text_field(wp_unslash($_POST['yaya_projects_hero_label'])));
    }
    if (isset($_POST['yaya_projects_filter_label'])) {
        update_post_meta($post_id, '_yaya_projects_filter_label', sanitize_text_field(wp_unslash($_POST['yaya_projects_filter_label'])));
    }
    if (isset($_POST['yaya_projects_empty_state'])) {
        update_post_meta($post_id, '_yaya_projects_empty_state', sanitize_textarea_field(wp_unslash($_POST['yaya_projects_empty_state'])));
    }
}
add_action('save_post_page', 'yaya_save_projects_meta_box');

/* ─────────────────────────────────────────
   HOME PAGE EDITOR FIELDS
───────────────────────────────────────── */
function yaya_home_page_defaults() {
    return [
        'hero' => [
            'tag'      => 'Est. in Excellence',
            'line1'    => 'WE',
            'line2'    => 'BUILD',
            'line3'    => 'YOUR VISION',
            'sub'      => 'From groundbreaking to grand opening — Yaya Construct delivers construction that lasts generations.',
            'cta1'     => 'View Our Work',
            'cta1_url' => home_url('/projects'),
            'cta2'     => 'Get a Quote',
            'cta2_url' => home_url('/contact'),
        ],
        'stats' => [
            1 => ['num' => '150+', 'label' => 'Projects Completed'],
            2 => ['num' => '12+',  'label' => 'Years of Experience'],
            3 => ['num' => '98%',  'label' => 'Client Satisfaction'],
            4 => ['num' => '40+',  'label' => 'Skilled Professionals'],
        ],
        'services' => [
            'section_label' => 'What We Do',
            'section_title' => 'OUR SERVICES',
            1 => ['title' => 'General Construction', 'text' => 'Full-cycle construction management from planning to handover, delivered on time and within budget.'],
            2 => ['title' => 'Commercial Buildings', 'text' => 'Office complexes, retail centers, warehouses, and industrial facilities built to the highest standards.'],
            3 => ['title' => 'Residential Projects', 'text' => 'Custom homes, apartment buildings, and residential renovations crafted with care and precision.'],
            4 => ['title' => 'Renovation & Refit',   'text' => 'Breathing new life into existing structures with expert renovation, retrofitting, and restoration work.'],
            5 => ['title' => 'Design & Build',       'text' => 'Integrated design-build solutions combining architectural vision with construction expertise under one roof.'],
            6 => ['title' => 'Project Management',   'text' => 'Professional oversight, scheduling, and coordination for complex multi-phase construction projects.'],
        ],
        'featured' => [
            'label'         => 'Featured Work',
            'button_text'   => 'Explore All Projects',
            'button_url'    => home_url('/projects'),
            'empty_title'   => 'BUILT WITH PURPOSE, CRAFTED WITH PRIDE',
            'empty_text'    => 'Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.',
        ],
    ];
}

function yaya_get_home_page_field($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function yaya_is_home_editor_page($post) {
    if (!$post instanceof WP_Post || $post->post_type !== 'page') {
        return false;
    }

    $front_page_id = (int) get_option('page_on_front');
    return $post->ID === $front_page_id || $post->post_name === 'home';
}

function yaya_add_home_meta_box() {
    add_meta_box(
        'yaya_home_details',
        'Home Page Details',
        'yaya_render_home_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'yaya_add_home_meta_box');

function yaya_render_home_meta_box($post) {
    if (!yaya_is_home_editor_page($post)) {
        echo '<p>This panel is used by the page assigned as the static homepage in WordPress Reading settings.</p>';
        return;
    }

    $defaults = yaya_home_page_defaults();
    wp_nonce_field('yaya_home_meta_box', 'yaya_home_meta_nonce');

    echo '<p>Use these fields to manage homepage text from the page editor. Existing Customizer values remain as fallback if a field is left blank.</p>';
    echo '<style>
        .yaya-meta-grid{display:grid;gap:16px}
        .yaya-meta-section{border:1px solid #dcdcde;padding:16px;background:#fff}
        .yaya-meta-row{display:grid;gap:12px;grid-template-columns:1fr 2fr;margin-bottom:12px}
        .yaya-meta-row:last-child{margin-bottom:0}
        .yaya-meta-row label{font-weight:600}
        .yaya-meta-row input,.yaya-meta-row textarea{width:100%}
      </style>';

    echo '<div class="yaya-meta-grid">';

    echo '<div class="yaya-meta-section"><h3>Hero Section</h3>';
    $hero_fields = [
        'tag'      => 'Hero Tag',
        'line1'    => 'Hero Heading Line 1',
        'line2'    => 'Hero Heading Line 2',
        'line3'    => 'Hero Heading Line 3',
        'sub'      => 'Hero Subtext',
        'cta1'     => 'Primary Button Text',
        'cta1_url' => 'Primary Button URL',
        'cta2'     => 'Secondary Button Text',
        'cta2_url' => 'Secondary Button URL',
    ];
    foreach ($hero_fields as $key => $label) {
        $value = yaya_get_home_page_field($post->ID, "_yaya_home_hero_{$key}", $defaults['hero'][$key]);
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_home_hero_' . $key . '">' . esc_html($label) . '</label>';
        if (in_array($key, ['sub'], true)) {
            echo '<textarea rows="3" id="yaya_home_hero_' . $key . '" name="yaya_home_hero_' . $key . '">' . esc_textarea($value) . '</textarea>';
        } else {
            $type = str_ends_with($key, '_url') ? 'url' : 'text';
            echo '<input type="' . esc_attr($type) . '" id="yaya_home_hero_' . $key . '" name="yaya_home_hero_' . $key . '" value="' . esc_attr($value) . '">';
        }
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="yaya-meta-section"><h3>Stats Bar</h3>';
    for ($i = 1; $i <= 4; $i++) {
        $num = yaya_get_home_page_field($post->ID, "_yaya_home_stat_{$i}_num", $defaults['stats'][$i]['num']);
        $label = yaya_get_home_page_field($post->ID, "_yaya_home_stat_{$i}_label", $defaults['stats'][$i]['label']);
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_home_stat_' . $i . '_num">Stat ' . $i . ' Number</label>';
        echo '<input type="text" id="yaya_home_stat_' . $i . '_num" name="yaya_home_stat_' . $i . '_num" value="' . esc_attr($num) . '">';
        echo '</div>';
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_home_stat_' . $i . '_label">Stat ' . $i . ' Label</label>';
        echo '<input type="text" id="yaya_home_stat_' . $i . '_label" name="yaya_home_stat_' . $i . '_label" value="' . esc_attr($label) . '">';
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="yaya-meta-section"><h3>Services Section</h3>';
    $service_section_label = yaya_get_home_page_field($post->ID, '_yaya_home_services_section_label', $defaults['services']['section_label']);
    $service_section_title = yaya_get_home_page_field($post->ID, '_yaya_home_services_section_title', $defaults['services']['section_title']);
    echo '<div class="yaya-meta-row"><label for="yaya_home_services_section_label">Section Label</label><input type="text" id="yaya_home_services_section_label" name="yaya_home_services_section_label" value="' . esc_attr($service_section_label) . '"></div>';
    echo '<div class="yaya-meta-row"><label for="yaya_home_services_section_title">Section Title</label><input type="text" id="yaya_home_services_section_title" name="yaya_home_services_section_title" value="' . esc_attr($service_section_title) . '"></div>';
    for ($i = 1; $i <= 6; $i++) {
        $title = yaya_get_home_page_field($post->ID, "_yaya_home_service_{$i}_title", $defaults['services'][$i]['title']);
        $text = yaya_get_home_page_field($post->ID, "_yaya_home_service_{$i}_text", $defaults['services'][$i]['text']);
        echo '<div class="yaya-meta-row"><label for="yaya_home_service_' . $i . '_title">Service ' . $i . ' Title</label><input type="text" id="yaya_home_service_' . $i . '_title" name="yaya_home_service_' . $i . '_title" value="' . esc_attr($title) . '"></div>';
        echo '<div class="yaya-meta-row"><label for="yaya_home_service_' . $i . '_text">Service ' . $i . ' Description</label><textarea rows="3" id="yaya_home_service_' . $i . '_text" name="yaya_home_service_' . $i . '_text">' . esc_textarea($text) . '</textarea></div>';
    }
    echo '</div>';

    echo '<div class="yaya-meta-section"><h3>Featured Project Section</h3>';
    $featured_fields = [
        'label'       => 'Section Label',
        'button_text' => 'Button Text',
        'button_url'  => 'Button URL',
        'empty_title' => 'Fallback Title',
        'empty_text'  => 'Fallback Text',
    ];
    foreach ($featured_fields as $key => $label) {
        $value = yaya_get_home_page_field($post->ID, "_yaya_home_featured_{$key}", $defaults['featured'][$key]);
        echo '<div class="yaya-meta-row">';
        echo '<label for="yaya_home_featured_' . $key . '">' . esc_html($label) . '</label>';
        if (in_array($key, ['empty_text'], true)) {
            echo '<textarea rows="3" id="yaya_home_featured_' . $key . '" name="yaya_home_featured_' . $key . '">' . esc_textarea($value) . '</textarea>';
        } else {
            $type = $key === 'button_url' ? 'url' : 'text';
            echo '<input type="' . esc_attr($type) . '" id="yaya_home_featured_' . $key . '" name="yaya_home_featured_' . $key . '" value="' . esc_attr($value) . '">';
        }
        echo '</div>';
    }
    echo '</div>';

    echo '</div>';
}

function yaya_save_home_meta_box($post_id) {
    if (!isset($_POST['yaya_home_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yaya_home_meta_nonce'])), 'yaya_home_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $hero_keys = ['tag', 'line1', 'line2', 'line3', 'sub', 'cta1', 'cta1_url', 'cta2', 'cta2_url'];
    foreach ($hero_keys as $key) {
        $field = "yaya_home_hero_{$key}";
        if (isset($_POST[$field])) {
            $value = wp_unslash($_POST[$field]);
            $value = str_ends_with($key, '_url') ? esc_url_raw($value) : ($key === 'sub' ? sanitize_textarea_field($value) : sanitize_text_field($value));
            update_post_meta($post_id, "_yaya_home_hero_{$key}", $value);
        }
    }

    for ($i = 1; $i <= 4; $i++) {
        if (isset($_POST["yaya_home_stat_{$i}_num"])) {
            update_post_meta($post_id, "_yaya_home_stat_{$i}_num", sanitize_text_field(wp_unslash($_POST["yaya_home_stat_{$i}_num"])));
        }
        if (isset($_POST["yaya_home_stat_{$i}_label"])) {
            update_post_meta($post_id, "_yaya_home_stat_{$i}_label", sanitize_text_field(wp_unslash($_POST["yaya_home_stat_{$i}_label"])));
        }
    }

    if (isset($_POST['yaya_home_services_section_label'])) {
        update_post_meta($post_id, '_yaya_home_services_section_label', sanitize_text_field(wp_unslash($_POST['yaya_home_services_section_label'])));
    }
    if (isset($_POST['yaya_home_services_section_title'])) {
        update_post_meta($post_id, '_yaya_home_services_section_title', sanitize_text_field(wp_unslash($_POST['yaya_home_services_section_title'])));
    }

    for ($i = 1; $i <= 6; $i++) {
        if (isset($_POST["yaya_home_service_{$i}_title"])) {
            update_post_meta($post_id, "_yaya_home_service_{$i}_title", sanitize_text_field(wp_unslash($_POST["yaya_home_service_{$i}_title"])));
        }
        if (isset($_POST["yaya_home_service_{$i}_text"])) {
            update_post_meta($post_id, "_yaya_home_service_{$i}_text", sanitize_textarea_field(wp_unslash($_POST["yaya_home_service_{$i}_text"])));
        }
    }

    $featured_keys = ['label', 'button_text', 'button_url', 'empty_title', 'empty_text'];
    foreach ($featured_keys as $key) {
        $field = "yaya_home_featured_{$key}";
        if (isset($_POST[$field])) {
            $value = wp_unslash($_POST[$field]);
            if ($key === 'button_url') {
                $value = esc_url_raw($value);
            } elseif ($key === 'empty_text') {
                $value = sanitize_textarea_field($value);
            } else {
                $value = sanitize_text_field($value);
            }
            update_post_meta($post_id, "_yaya_home_featured_{$key}", $value);
        }
    }
}
add_action('save_post_page', 'yaya_save_home_meta_box');
