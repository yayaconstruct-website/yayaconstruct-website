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
    // Archivo is served as a variable font carrying both a width and a weight
    // axis, so the expanded display cut is font-stretch rather than a second
    // file. Fragment Mono is the data layer. Three faces became two requests.
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@75..125,400..700&family=Fragment+Mono&display=swap',
        [], null
    );
    wp_enqueue_style('yaya-style', get_stylesheet_uri(), ['google-fonts'], '2.0');
}
add_action('wp_enqueue_scripts', 'yaya_scripts');

/**
 * Preload the home page hero image.
 *
 * It is the LCP element, but it is a CSS background, so the browser cannot
 * discover it until the stylesheet has downloaded and parsed. Naming it in the
 * head lets the preload scanner start the fetch immediately.
 */
function yaya_preload_hero_image() {
    if (!is_front_page()) {
        return;
    }

    $hero_img = get_theme_mod('yaya_hero_image', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1600&q=80');
    if (!$hero_img) {
        return;
    }

    printf(
        '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
        esc_url($hero_img)
    );
}
add_action('wp_head', 'yaya_preload_hero_image', 2);

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
   PROJECT CATEGORIES: CITY ORDER
───────────────────────────────────────── */
function yaya_project_city_order() {
    return ['Brussels', 'Amsterdam', 'Izmir'];
}

function yaya_seed_project_categories() {
    if (!taxonomy_exists('project_category')) {
        return;
    }

    foreach (yaya_project_city_order() as $city) {
        if (!term_exists($city, 'project_category')) {
            wp_insert_term($city, 'project_category');
        }
    }
}
add_action('init', 'yaya_seed_project_categories', 20);

/* ─────────────────────────────────────────
   PROJECT REGIONS
   The projects index groups by the two latitudes the practice builds in
   rather than by city. City categories stay as they are — they are still how
   a project is filed, and they still label the individual entry — but a
   region is what the page is organised around. Amsterdam and Brussels are one
   project each; as cities they read as gaps, as "Low Countries" they read as
   an international position.
───────────────────────────────────────── */
function yaya_project_regions() {
    return [
        'aegean' => [
            'name'   => 'Aegean',
            'cities' => ['Izmir'],
        ],
        'low-countries' => [
            'name'   => 'Low Countries',
            'cities' => ['Amsterdam', 'Brussels'],
        ],
    ];
}

/**
 * The region a city category belongs to, or '' when it is not mapped.
 *
 * An unmapped category is not an error — a new city can be added in the admin
 * before anyone touches this file — so callers group those under their own
 * name instead of dropping them.
 */
function yaya_project_region_for_category($category_name) {
    $category_name = trim((string) $category_name);
    if ($category_name === '') {
        return '';
    }

    foreach (yaya_project_regions() as $slug => $region) {
        foreach ($region['cities'] as $city) {
            if (strcasecmp($city, $category_name) === 0) {
                return $slug;
            }
        }
    }

    return '';
}

/* ─────────────────────────────────────────
   PROJECT CARD IMAGE
   Featured image first, then the project's own gallery, then anything in its
   content — so a project shows a card image however its photos were added.
───────────────────────────────────────── */
function yaya_project_card_image($post_id, $size = 'large') {
    static $cache = [];

    $post_id = (int) $post_id;
    if (!$post_id) {
        return '';
    }

    $key = $post_id . '|' . (is_array($size) ? implode('x', $size) : $size);
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    // 1. Featured image always wins, so a project can override its cover.
    $url = get_the_post_thumbnail_url($post_id, $size);

    // 2. Otherwise the first image of the Project Gallery meta box — the way
    //    photos are normally added to a project in this theme.
    if (!$url) {
        $gallery_ids = get_post_meta($post_id, '_yaya_project_gallery', true);
        $gallery_ids = $gallery_ids
            ? array_values(array_filter(array_map('absint', explode(',', $gallery_ids))))
            : [];
        if ($gallery_ids) {
            $url = wp_get_attachment_image_url($gallery_ids[0], $size);
        }
    }

    $content = $url ? '' : (string) get_post_field('post_content', $post_id);

    // 3. First image in the content. Editor-inserted images carry a
    //    wp-image-<ID> class, which lets us ask for the right size instead of
    //    reusing whatever size happens to be embedded.
    if (!$url && $content !== '' && preg_match('/<img\b[^>]*>/i', $content, $tag)) {
        if (preg_match('/wp-image-(\d+)/i', $tag[0], $m)) {
            $url = wp_get_attachment_image_url((int) $m[1], $size);
        }
        // Leading whitespace required so lazy-loader attributes such as
        // data-src (often a placeholder) are not mistaken for the real src.
        if (!$url && preg_match('/\ssrc=["\']([^"\']+)["\']/i', $tag[0], $m)) {
            $url = $m[1];
        }
    }

    // 4. Gallery shortcodes reference attachments by ID and render no <img>.
    if (!$url && $content !== '' && preg_match('/\[gallery[^\]]*ids=["\']([0-9,\s]+)["\']/i', $content, $m)) {
        $ids = array_values(array_filter(array_map('intval', explode(',', $m[1]))));
        if ($ids) {
            $url = wp_get_attachment_image_url($ids[0], $size);
        }
    }

    // 5. Last resort: the first image uploaded to this project.
    if (!$url) {
        $attached = get_attached_media('image', $post_id);
        if ($attached) {
            $first = reset($attached);
            $url = wp_get_attachment_image_url($first->ID, $size);
        }
    }

    $cache[$key] = $url ? $url : '';

    return $cache[$key];
}

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
            'category'   => 'Izmir',
            'source_url' => 'https://www.zabitci.com/proje-guzelbahce-x.html',
            'content'    => implode("\n\n", [
                'Guzelbahce X is presented as a villa project that combines the comfort of site living with the privacy of an independent home.',
                'The source project page highlights 10 villas in a 4+1 concept, each with approximately 240 square meters of enclosed area, private gardens, and a layout designed to balance calm green surroundings with quick access to city life.',
                'The project emphasizes generous spacing between villas, landscaped outdoor areas, strong family suitability, and proximity to schools, shopping, restaurants, and Izmir\'s wider coastal destinations.',
            ]),
        ],
        [
            'title'      => 'Inkim Suites',
            'slug'       => 'inkim-suites',
            'location'   => 'Ilica, Cesme',
            'year'       => '2021',
            'category'   => 'Izmir',
            'source_url' => 'https://zabitci.com/proje-inkim-suites.html',
            'content'    => implode("\n\n", [
                'Inkim Suites is described as the transformation of the long-standing Inkim Hotel into a refreshed residence concept in the heart of Ilica, Cesme.',
                'The source highlights Zabıtçı\'s renovation work, a central location close to beaches and amenities, and a residence program built around comfort, design, and year-round use.',
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
   ONE-TIME MIGRATION: CITY CATEGORIES (BRUSSELS / AMSTERDAM / IZMIR)
───────────────────────────────────────── */
function yaya_maybe_migrate_project_city_categories() {
    if (get_option('yaya_project_city_categories_migrated_v1')) {
        return;
    }

    if (!post_type_exists('project') || !taxonomy_exists('project_category')) {
        return;
    }

    $removed_slugs = ['no3-mavisehir', 'no17-bayrakli', 'alacati-qu4ttro'];
    foreach ($removed_slugs as $slug) {
        $existing = get_posts([
            'post_type'   => 'project',
            'name'        => $slug,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);
        foreach ($existing as $post_id) {
            wp_delete_post($post_id, true);
        }
    }

    $izmir_slugs = ['guzelbahce-x', 'inkim-suites', 'ee-villa'];
    foreach ($izmir_slugs as $slug) {
        $existing = get_posts([
            'post_type'   => 'project',
            'name'        => $slug,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);
        foreach ($existing as $post_id) {
            wp_set_object_terms($post_id, 'Izmir', 'project_category', false);
        }
    }

    update_option('yaya_project_city_categories_migrated_v1', gmdate('c'));
}
add_action('admin_init', 'yaya_maybe_migrate_project_city_categories', 20);

/* ─────────────────────────────────────────
   ONE-TIME MIGRATION: CITY PROJECT LINEUP
───────────────────────────────────────── */
function yaya_maybe_finalize_city_project_lineup() {
    if (get_option('yaya_city_project_lineup_finalized_v1')) {
        return;
    }

    if (!post_type_exists('project') || !taxonomy_exists('project_category')) {
        return;
    }

    // Final Izmir lineup is INKIM Suites, Guzelbahce X, and Z-Suites (placeholder) — drop E&E Villa.
    $existing = get_posts([
        'post_type'   => 'project',
        'name'        => 'ee-villa',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);
    foreach ($existing as $post_id) {
        wp_delete_post($post_id, true);
    }

    $z_suites = get_posts([
        'post_type'   => 'project',
        'name'        => 'z-suites',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);

    if (empty($z_suites)) {
        $post_id = wp_insert_post([
            'post_type'    => 'project',
            'post_status'  => 'publish',
            'post_title'   => 'Z-Suites',
            'post_name'    => 'z-suites',
            'post_content' => 'Details for Z-Suites are coming soon.',
        ], true);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_yaya_project_coming_soon', 1);
            wp_set_object_terms($post_id, 'Izmir', 'project_category', false);
        }
    }

    // One placeholder project each for Brussels and Amsterdam, to be filled in later.
    $city_placeholders = [
        ['title' => 'Brussels',  'slug' => 'brussels',  'category' => 'Brussels'],
        ['title' => 'Amsterdam', 'slug' => 'amsterdam', 'category' => 'Amsterdam'],
    ];

    foreach ($city_placeholders as $placeholder) {
        $existing_placeholder = get_posts([
            'post_type'   => 'project',
            'name'        => $placeholder['slug'],
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);

        if (!empty($existing_placeholder)) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_type'    => 'project',
            'post_status'  => 'publish',
            'post_title'   => $placeholder['title'],
            'post_name'    => $placeholder['slug'],
        ], true);

        if (!is_wp_error($post_id)) {
            wp_set_object_terms($post_id, $placeholder['category'], 'project_category', false);
        }
    }

    update_option('yaya_city_project_lineup_finalized_v1', gmdate('c'));
}
add_action('admin_init', 'yaya_maybe_finalize_city_project_lineup', 20);

/* ─────────────────────────────────────────
   ONE-TIME FIX: ENFORCE CORRECT CITY CATEGORY PER PROJECT
───────────────────────────────────────── */
function yaya_maybe_fix_project_city_categories() {
    if (get_option('yaya_project_city_categories_fixed_v1')) {
        return;
    }

    if (!post_type_exists('project') || !taxonomy_exists('project_category')) {
        return;
    }

    $expected = [
        'brussels'     => 'Brussels',
        'amsterdam'    => 'Amsterdam',
        'guzelbahce-x' => 'Izmir',
        'inkim-suites' => 'Izmir',
        'ee-villa'     => 'Izmir',
        'z-suites'     => 'Izmir',
    ];

    foreach ($expected as $slug => $category) {
        $existing = get_posts([
            'post_type'   => 'project',
            'name'        => $slug,
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);
        foreach ($existing as $post_id) {
            wp_set_object_terms($post_id, $category, 'project_category', false);
        }
    }

    update_option('yaya_project_city_categories_fixed_v1', gmdate('c'));
}
add_action('admin_init', 'yaya_maybe_fix_project_city_categories', 21);

/* ─────────────────────────────────────────
   PROJECT META BOX: PHOTO GALLERY & COMING SOON
───────────────────────────────────────── */
function yaya_add_project_gallery_meta_box() {
    add_meta_box(
        'yaya_project_gallery',
        'Project Gallery',
        'yaya_render_project_gallery_meta_box',
        'project',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'yaya_add_project_gallery_meta_box');

function yaya_render_project_gallery_meta_box($post) {
    wp_enqueue_media();
    wp_nonce_field('yaya_project_gallery_meta_box', 'yaya_project_gallery_nonce');

    $gallery_ids  = get_post_meta($post->ID, '_yaya_project_gallery', true);
    $gallery_ids  = $gallery_ids ? array_filter(array_map('absint', explode(',', $gallery_ids))) : [];
    $coming_soon  = (bool) get_post_meta($post->ID, '_yaya_project_coming_soon', true);
    ?>
    <p>
      <label>
        <input type="checkbox" id="yaya_project_coming_soon" name="yaya_project_coming_soon" value="1" <?php checked($coming_soon); ?> />
        Mark this project as "Coming Soon" (hides photos, shows a coming-soon badge instead)
      </label>
    </p>
    <p>
      <button type="button" class="button" id="yaya-project-gallery-add">Add Images to Gallery</button>
    </p>
    <input type="hidden" id="yaya_project_gallery_ids" name="yaya_project_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>" />
    <ul id="yaya-project-gallery-preview" style="display:flex;flex-wrap:wrap;gap:10px;list-style:none;margin:14px 0 0;padding:0;">
      <?php foreach ($gallery_ids as $attachment_id):
        $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');
        if (!$thumb) continue;
      ?>
      <li data-id="<?php echo esc_attr($attachment_id); ?>" style="position:relative;width:100px;height:100px;">
        <img src="<?php echo esc_url($thumb); ?>" style="width:100%;height:100%;object-fit:cover;display:block;" />
        <button type="button" class="yaya-gallery-remove" style="position:absolute;top:2px;right:2px;background:#c0392b;color:#fff;border:0;border-radius:50%;width:20px;height:20px;line-height:1;cursor:pointer;">&times;</button>
      </li>
      <?php endforeach; ?>
    </ul>
    <script>
    (function($){
      var frame;
      var $ids = $('#yaya_project_gallery_ids');
      var $list = $('#yaya-project-gallery-preview');

      $('#yaya-project-gallery-add').on('click', function(e){
        e.preventDefault();
        if (frame) { frame.open(); return; }
        frame = wp.media({
          title: 'Select Gallery Images',
          button: { text: 'Add to Gallery' },
          multiple: true
        });
        frame.on('select', function(){
          var selection = frame.state().get('selection');
          selection.each(function(attachment){
            attachment = attachment.toJSON();
            var current = $ids.val() ? $ids.val().split(',') : [];
            if (current.indexOf(String(attachment.id)) !== -1) return;
            current.push(attachment.id);
            $ids.val(current.join(','));
            var thumb = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
            $list.append(
              '<li data-id="' + attachment.id + '" style="position:relative;width:100px;height:100px;">' +
                '<img src="' + thumb + '" style="width:100%;height:100%;object-fit:cover;display:block;" />' +
                '<button type="button" class="yaya-gallery-remove" style="position:absolute;top:2px;right:2px;background:#c0392b;color:#fff;border:0;border-radius:50%;width:20px;height:20px;line-height:1;cursor:pointer;">&times;</button>' +
              '</li>'
            );
          });
        });
        frame.open();
      });

      $list.on('click', '.yaya-gallery-remove', function(){
        var $li = $(this).closest('li');
        var id = String($li.data('id'));
        var current = $ids.val() ? $ids.val().split(',') : [];
        current = current.filter(function(v){ return v !== id; });
        $ids.val(current.join(','));
        $li.remove();
      });
    })(jQuery);
    </script>
    <?php
}

function yaya_save_project_gallery_meta_box($post_id) {
    if (!isset($_POST['yaya_project_gallery_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yaya_project_gallery_nonce'])), 'yaya_project_gallery_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['yaya_project_gallery_ids'])) {
        $ids = array_filter(array_map('absint', explode(',', wp_unslash($_POST['yaya_project_gallery_ids']))));
        update_post_meta($post_id, '_yaya_project_gallery', implode(',', $ids));
    }

    update_post_meta($post_id, '_yaya_project_coming_soon', isset($_POST['yaya_project_coming_soon']) ? 1 : 0);
}
add_action('save_post_project', 'yaya_save_project_gallery_meta_box');

/* ─────────────────────────────────────────
   PROJECT SPEC
   Location, year, scope, area and status, in that fixed order, set in mono on
   every project page. It is how the profession presents work, and it gives a
   short entry a spine — three of the five projects are a paragraph or less.

   Location and year keep their original unprefixed meta keys: they were only
   ever editable through the raw Custom Fields panel, and the data that is
   already there has to keep resolving.
───────────────────────────────────────── */
function yaya_project_spec_definition() {
    return [
        [
            'key'         => 'project_location',
            'label'       => 'Location',
            'placeholder' => 'Ilica, Cesme',
            'suggestions' => [],
        ],
        [
            'key'         => 'project_year',
            'label'       => 'Year',
            'placeholder' => '2021',
            'suggestions' => [],
        ],
        [
            'key'         => '_yaya_project_scope',
            'label'       => 'Scope',
            'placeholder' => 'Conversion',
            'suggestions' => ['New build', 'Conversion', 'Renovation', 'Fit-out', 'Interior', 'Extension'],
        ],
        [
            'key'         => '_yaya_project_area',
            'label'       => 'Area',
            'placeholder' => '240 m² per villa',
            'suggestions' => [],
        ],
        [
            'key'         => '_yaya_project_status',
            'label'       => 'Status',
            'placeholder' => 'Completed',
            'suggestions' => ['Completed', 'In progress', 'In design', 'On site', 'Coming soon'],
        ],
    ];
}

/**
 * One spec value.
 *
 * Status falls back to the coming-soon flag so a placeholder project still has
 * something true to say for itself; every other field is only ever what an
 * editor typed.
 */
function yaya_project_spec_value($post_id, $key) {
    $value = trim((string) get_post_meta($post_id, $key, true));

    if ($value === '' && $key === '_yaya_project_status' && get_post_meta($post_id, '_yaya_project_coming_soon', true)) {
        return 'Coming soon';
    }

    return $value;
}

/**
 * The spec rows a template should render: label/value pairs in the fixed
 * order, with the unset fields dropped rather than shown blank.
 */
function yaya_project_spec_rows($post_id) {
    $rows = [];

    foreach (yaya_project_spec_definition() as $field) {
        $value = yaya_project_spec_value($post_id, $field['key']);
        if ($value === '') {
            continue;
        }
        $rows[] = ['label' => $field['label'], 'value' => $value];
    }

    return $rows;
}

/* ─────────────────────────────────────────
   PROJECT META BOX: DETAILS
───────────────────────────────────────── */
function yaya_add_project_details_meta_box() {
    add_meta_box(
        'yaya_project_details',
        'Project Details',
        'yaya_render_project_details_meta_box',
        'project',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'yaya_add_project_details_meta_box');

function yaya_render_project_details_meta_box($post) {
    wp_nonce_field('yaya_project_details_meta_box', 'yaya_project_details_nonce');
    ?>
    <p style="margin-top:0;color:#50575e;">
      Shown as the spec block on the project page and as the detail line in the projects index.
      Leave a field empty to leave it out.
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(15rem,1fr));gap:14px 20px;">
      <?php foreach (yaya_project_spec_definition() as $field):
        $field_id  = 'yaya_field_' . ltrim($field['key'], '_');
        $list_id   = $field['suggestions'] ? $field_id . '_options' : '';
        // The raw stored value, not yaya_project_spec_value() — the coming-soon
        // fallback is a display default and must not look like typed content.
        $value     = trim((string) get_post_meta($post->ID, $field['key'], true));
      ?>
      <p style="margin:0;">
        <label for="<?php echo esc_attr($field_id); ?>" style="display:block;font-weight:600;margin-bottom:4px;">
          <?php echo esc_html($field['label']); ?>
        </label>
        <input
          type="text"
          id="<?php echo esc_attr($field_id); ?>"
          name="<?php echo esc_attr($field_id); ?>"
          value="<?php echo esc_attr($value); ?>"
          placeholder="<?php echo esc_attr($field['placeholder']); ?>"
          class="widefat"
          <?php if ($list_id): ?>list="<?php echo esc_attr($list_id); ?>"<?php endif; ?>
        />
        <?php if ($list_id): ?>
        <datalist id="<?php echo esc_attr($list_id); ?>">
          <?php foreach ($field['suggestions'] as $suggestion): ?>
            <option value="<?php echo esc_attr($suggestion); ?>"></option>
          <?php endforeach; ?>
        </datalist>
        <?php endif; ?>
      </p>
      <?php endforeach; ?>
    </div>
    <?php
}

function yaya_save_project_details_meta_box($post_id) {
    if (!isset($_POST['yaya_project_details_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yaya_project_details_nonce'])), 'yaya_project_details_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    foreach (yaya_project_spec_definition() as $field) {
        $field_id = 'yaya_field_' . ltrim($field['key'], '_');
        if (!isset($_POST[$field_id])) {
            continue;
        }

        $value = sanitize_text_field(wp_unslash($_POST[$field_id]));

        // Cleared fields are deleted rather than stored empty, so the spec
        // block and the index both see one kind of "not set".
        if ($value === '') {
            delete_post_meta($post_id, $field['key']);
            continue;
        }

        update_post_meta($post_id, $field['key'], $value);
    }
}
add_action('save_post_project', 'yaya_save_project_details_meta_box');

/* ─────────────────────────────────────────
   CONTACT FORM
───────────────────────────────────────── */

/**
 * Where contact submissions are delivered.
 *
 * The Customizer's "Contact Email" is the single source of truth; if it is
 * blank or invalid we fall back to info@yayaconstruct.com. The contact page's
 * own email field is display copy only — it used to override this silently,
 * so editing the address shown on the page quietly redirected the mail.
 */
function yaya_contact_recipient() {
    $to = get_theme_mod('yaya_contact_email', 'info@yayaconstruct.com');
    $to = apply_filters('yaya_contact_recipient', $to);

    return is_email($to) ? $to : 'info@yayaconstruct.com';
}

/**
 * Strip anything that could break out of an address header.
 */
function yaya_contact_header_name($name) {
    return trim(preg_replace('/[\r\n<>,;"]+/', ' ', $name));
}

function yaya_contact_form() {
    if (!check_ajax_referer('yaya_contact_nonce', 'nonce', false)) {
        // Usually a cached page serving a nonce that has since expired.
        wp_send_json([
            'success' => false,
            'error'   => 'Your session expired. Please reload the page and try again.',
        ]);
    }

    $name    = sanitize_text_field($_POST['name']    ?? '');
    $email   = sanitize_email($_POST['email']        ?? '');
    $phone   = sanitize_text_field($_POST['phone']   ?? '');
    $type    = sanitize_text_field($_POST['type']    ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if (empty($name) || !is_email($email) || empty($message)) {
        wp_send_json(['success' => false, 'error' => 'Missing or invalid required fields']);
    }

    $to      = yaya_contact_recipient();
    $subject = 'New Message from ' . $name . ' – Yaya Construct';
    $body    = "Name: $name\nEmail: $email\nPhone: $phone\nProject Type: $type\n\nMessage:\n$message";

    // A From address on this domain that is a real mailbox. Without it
    // WordPress sends as wordpress@<domain>, which receiving servers commonly
    // reject or drop silently because that mailbox does not exist — the usual
    // reason a form reports success but nothing ever arrives.
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: Yaya Construct Website <' . $to . '>',
        'Reply-To: ' . yaya_contact_header_name($name) . ' <' . $email . '>',
    ];

    // Capture why PHPMailer failed instead of returning a bare false.
    $mail_error = '';
    $capture = function ($wp_error) use (&$mail_error) {
        $mail_error = $wp_error->get_error_message();
    };
    add_action('wp_mail_failed', $capture);
    try {
        // A throw inside wp_mail (a mail plugin, a PHPMailer misconfiguration)
        // would otherwise abort the request with an HTML error page, and the
        // form — expecting JSON — could only report a generic failure.
        $sent = wp_mail($to, $subject, $body, $headers);
    } catch (\Throwable $e) {
        $sent = false;
        $mail_error = $e->getMessage();
    }
    remove_action('wp_mail_failed', $capture);

    if (!$sent) {
        $mail_error = $mail_error ?: 'unknown error';
        error_log('[yaya contact] delivery to ' . $to . ' failed: ' . $mail_error);

        // Administrators get the real reason; visitors get the plain copy.
        $detail = current_user_can('manage_options')
            ? ' (' . $mail_error . ')'
            : '';

        wp_send_json([
            'success' => false,
            'error'   => 'The message could not be sent. Please email us directly.' . $detail,
        ]);
    }

    wp_send_json(['success' => true]);
}
add_action('wp_ajax_nopriv_yaya_contact', 'yaya_contact_form');
add_action('wp_ajax_yaya_contact',        'yaya_contact_form');

/**
 * Optional authenticated SMTP delivery.
 *
 * PHP's mail() from shared hosting is frequently spam-filed or rejected,
 * especially when the mailbox is hosted elsewhere (Google Workspace, Outlook).
 * Define these in wp-config.php to send over SMTP instead — credentials stay
 * out of the repository:
 *
 *   define('YAYA_SMTP_HOST',   'mail.yayaconstruct.com');
 *   define('YAYA_SMTP_USER',   'info@yayaconstruct.com');
 *   define('YAYA_SMTP_PASS',   '...');
 *   define('YAYA_SMTP_PORT',   587);      // optional, defaults to 587
 *   define('YAYA_SMTP_SECURE', 'tls');    // optional, 'tls' or 'ssl'
 */
function yaya_configure_smtp($phpmailer) {
    if (!defined('YAYA_SMTP_HOST') || !defined('YAYA_SMTP_USER') || !defined('YAYA_SMTP_PASS')) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = YAYA_SMTP_HOST;
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = YAYA_SMTP_USER;
    $phpmailer->Password   = YAYA_SMTP_PASS;
    $phpmailer->Port       = defined('YAYA_SMTP_PORT') ? (int) YAYA_SMTP_PORT : 587;
    $phpmailer->SMTPSecure = defined('YAYA_SMTP_SECURE') ? YAYA_SMTP_SECURE : 'tls';
}
add_action('phpmailer_init', 'yaya_configure_smtp');

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

}
add_action('customize_register', 'yaya_customizer');

/* ─────────────────────────────────────────
   ABOUT PAGE EDITOR FIELDS
───────────────────────────────────────── */
function yaya_about_page_defaults() {
    return [
        'hero' => [
            'label' => 'Our Story',
        ],
        'body' => [
            'label'      => 'Who We Are',
            'heading'    => 'MORE THAN JUST' . "\n" . 'A CONTRACTOR',
            'cta_label'  => 'Work With Us',
            'cta_url'    => home_url('/contact'),
        ],
        'values' => [
            'section_label' => 'Our Values',
            'section_title' => 'WHAT DRIVES US',
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
    ];
}

function yaya_get_about_page_field($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function yaya_is_about_editor_page($post) {
    if (!$post instanceof WP_Post || $post->post_type !== 'page') {
        return false;
    }

    $template = get_page_template_slug($post->ID);
    if ($template === 'page-about.php') {
        return true;
    }

    $aliases = ['about', 'about-us', 'our-story'];
    $slug = sanitize_title($post->post_name);
    $title = sanitize_title($post->post_title);

    return in_array($slug, $aliases, true) || in_array($title, $aliases, true);
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
    if (!yaya_is_about_editor_page($post)) {
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

    echo '<div class="yaya-meta-section"><h3>Hero and Body</h3>';
    $hero_label = yaya_get_about_page_field($post->ID, '_yaya_about_hero_label', $defaults['hero']['label']);
    $body_label = yaya_get_about_page_field($post->ID, '_yaya_about_body_label', $defaults['body']['label']);
    $body_heading = yaya_get_about_page_field($post->ID, '_yaya_about_body_heading', $defaults['body']['heading']);
    $cta_label = yaya_get_about_page_field($post->ID, '_yaya_about_cta_label', $defaults['body']['cta_label']);
    $cta_url = yaya_get_about_page_field($post->ID, '_yaya_about_cta_url', $defaults['body']['cta_url']);
    echo '<div class="yaya-meta-row"><label for="yaya_about_hero_label">Hero Label</label><input type="text" id="yaya_about_hero_label" name="yaya_about_hero_label" value="' . esc_attr($hero_label) . '"></div>';
    echo '<div class="yaya-meta-row"><label for="yaya_about_body_label">Body Section Label</label><input type="text" id="yaya_about_body_label" name="yaya_about_body_label" value="' . esc_attr($body_label) . '"></div>';
    echo '<div class="yaya-meta-row"><label for="yaya_about_body_heading">Body Heading</label><textarea rows="3" id="yaya_about_body_heading" name="yaya_about_body_heading">' . esc_textarea($body_heading) . '</textarea></div>';
    echo '<div class="yaya-meta-row"><label for="yaya_about_cta_label">CTA Button Text</label><input type="text" id="yaya_about_cta_label" name="yaya_about_cta_label" value="' . esc_attr($cta_label) . '"></div>';
    echo '<div class="yaya-meta-row"><label for="yaya_about_cta_url">CTA Button URL</label><input type="url" id="yaya_about_cta_url" name="yaya_about_cta_url" value="' . esc_attr($cta_url) . '"></div>';
    echo '</div>';

    echo '<div class="yaya-meta-section"><h3>Values Section</h3>';
    $values_section_label = yaya_get_about_page_field($post->ID, '_yaya_about_values_section_label', $defaults['values']['section_label']);
    $values_section_title = yaya_get_about_page_field($post->ID, '_yaya_about_values_section_title', $defaults['values']['section_title']);
    echo '<div class="yaya-meta-row"><label for="yaya_about_values_section_label">Section Label</label><input type="text" id="yaya_about_values_section_label" name="yaya_about_values_section_label" value="' . esc_attr($values_section_label) . '"></div>';
    echo '<div class="yaya-meta-row"><label for="yaya_about_values_section_title">Section Title</label><input type="text" id="yaya_about_values_section_title" name="yaya_about_values_section_title" value="' . esc_attr($values_section_title) . '"></div>';
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

    $text_fields = [
        'yaya_about_hero_label' => '_yaya_about_hero_label',
        'yaya_about_body_label' => '_yaya_about_body_label',
        'yaya_about_body_heading' => '_yaya_about_body_heading',
        'yaya_about_cta_label' => '_yaya_about_cta_label',
        'yaya_about_values_section_label' => '_yaya_about_values_section_label',
        'yaya_about_values_section_title' => '_yaya_about_values_section_title',
    ];

    foreach ($text_fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            $value = wp_unslash($_POST[$field]);
            $value = $field === 'yaya_about_body_heading' ? sanitize_textarea_field($value) : sanitize_text_field($value);
            update_post_meta($post_id, $meta_key, $value);
        }
    }

    if (isset($_POST['yaya_about_cta_url'])) {
        update_post_meta($post_id, '_yaya_about_cta_url', esc_url_raw(wp_unslash($_POST['yaya_about_cta_url'])));
    }

    for ($i = 1; $i <= 4; $i++) {
        if (isset($_POST["yaya_about_value_{$i}_title"])) {
            update_post_meta($post_id, "_yaya_about_value_{$i}_title", sanitize_text_field(wp_unslash($_POST["yaya_about_value_{$i}_title"])));
        }
        if (isset($_POST["yaya_about_value_{$i}_text"])) {
            update_post_meta($post_id, "_yaya_about_value_{$i}_text", sanitize_textarea_field(wp_unslash($_POST["yaya_about_value_{$i}_text"])));
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

/* ─────────────────────────────────────────
   CONTACT PAGE EDITOR FIELDS
───────────────────────────────────────── */
function yaya_contact_page_defaults() {
    return [
        'hero' => [
            'label'   => 'Get In Touch',
            'heading' => "LET'S BUILD\nSOMETHING GREAT",
        ],
        'info' => [
            'section_label'    => 'Contact',
            'heading'          => "REACH OUT\nTO US",
            'address_label'    => 'Office Address',
            'address1'         => get_theme_mod('yaya_contact_address1', '123 Construction Ave'),
            'address2'         => get_theme_mod('yaya_contact_address2', 'Building District, City 10001'),
            'phone_label'      => 'Phone',
            'phone'            => get_theme_mod('yaya_contact_phone', '+1 (555) 000-0000'),
            'email_label'      => 'Email',
            'email'            => get_theme_mod('yaya_contact_email', 'info@yayaconstruct.com'),
            'hours_label'      => 'Working Hours',
            'hours1'           => get_theme_mod('yaya_contact_hours1', 'Mon–Fri: 7:00 AM – 6:00 PM'),
            'hours2'           => get_theme_mod('yaya_contact_hours2', 'Sat: 8:00 AM – 2:00 PM'),
            'social_label'     => 'Follow Us',
            'instagram_url'    => 'https://www.instagram.com/yayaconstruct/',
            'linkedin_url'     => '',
            'facebook_url'     => '',
        ],
        'form' => [
            'heading'            => "SEND US\nA MESSAGE",
            'first_name_label'   => 'First Name',
            'first_name_placeholder' => 'John',
            'last_name_label'    => 'Last Name',
            'last_name_placeholder' => 'Smith',
            'email_label'        => 'Email',
            'email_placeholder'  => 'you@email.com',
            'phone_label'        => 'Phone',
            'phone_placeholder'  => '+1 555 000 0000',
            'project_type_label' => 'Project Type',
            'project_type_placeholder' => 'Select a service...',
            'project_type_options' => "General Construction\nCommercial Building\nResidential Project\nRenovation & Refit\nDesign & Build\nProject Management\nOther",
            'message_label'      => 'Message',
            'message_placeholder' => 'Tell us about your project...',
            'submit_label'       => 'Send Message →',
            'submit_loading_label' => 'Sending...',
            'success_message'    => 'Thank you! Your message has been received. We\'ll be in touch within 24 hours.',
            'error_message'      => 'Something went wrong. Please try again or email us directly.',
        ],
    ];
}

function yaya_get_contact_page_field($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function yaya_add_contact_meta_box() {
    add_meta_box(
        'yaya_contact_details_editor',
        'Contact Page Details',
        'yaya_render_contact_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'yaya_add_contact_meta_box');

function yaya_render_contact_meta_box($post) {
    $template = get_page_template_slug($post->ID);
    $slug     = $post->post_name;

    if ($template !== 'page-contact.php' && !in_array($slug, ['contact', 'contact-us', 'get-in-touch'], true)) {
        echo '<p>This panel is used by the Contact page. Assign the Contact template to this page to use these fields.</p>';
        return;
    }

    $defaults = yaya_contact_page_defaults();
    wp_nonce_field('yaya_contact_page_meta_box', 'yaya_contact_page_meta_nonce');

    echo '<p>Use the regular page title, excerpt, and content editor if you want extra Contact page content later. Use the fields below to manage the currently visible Contact page components.</p>';
    echo '<style>
        .yaya-meta-grid{display:grid;gap:16px}
        .yaya-meta-section{border:1px solid #dcdcde;padding:16px;background:#fff}
        .yaya-meta-row{display:grid;gap:12px;grid-template-columns:1fr 2fr;margin-bottom:12px}
        .yaya-meta-row:last-child{margin-bottom:0}
        .yaya-meta-row label{font-weight:600}
        .yaya-meta-row input,.yaya-meta-row textarea{width:100%}
      </style>';

    $sections = [
        'hero' => 'Hero Section',
        'info' => 'Contact Info Section',
        'form' => 'Form Section',
    ];

    echo '<div class="yaya-meta-grid">';
    foreach ($sections as $section_key => $section_title) {
        echo '<div class="yaya-meta-section"><h3>' . esc_html($section_title) . '</h3>';
        foreach ($defaults[$section_key] as $field_key => $fallback) {
            $meta_key = "_yaya_contact_{$section_key}_{$field_key}";
            $value = yaya_get_contact_page_field($post->ID, $meta_key, $fallback);
            $field_id = "yaya_contact_{$section_key}_{$field_key}";
            $label = ucwords(str_replace('_', ' ', $field_key));
            echo '<div class="yaya-meta-row">';
            echo '<label for="' . esc_attr($field_id) . '">' . esc_html($label) . '</label>';
            if (in_array($field_key, ['heading', 'project_type_options', 'success_message', 'error_message'], true)) {
                echo '<textarea rows="3" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '">' . esc_textarea($value) . '</textarea>';
            } else {
                $type = str_ends_with($field_key, '_url') ? 'url' : 'text';
                echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" value="' . esc_attr($value) . '">';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
}

function yaya_save_contact_meta_box($post_id) {
    if (!isset($_POST['yaya_contact_page_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['yaya_contact_page_meta_nonce'])), 'yaya_contact_page_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $defaults = yaya_contact_page_defaults();
    foreach ($defaults as $section_key => $fields) {
        foreach ($fields as $field_key => $fallback) {
            $field = "yaya_contact_{$section_key}_{$field_key}";
            if (!isset($_POST[$field])) {
                continue;
            }

            $value = wp_unslash($_POST[$field]);
            if (str_ends_with($field_key, '_url')) {
                $value = esc_url_raw($value);
            } elseif (in_array($field_key, ['heading', 'project_type_options', 'success_message', 'error_message'], true)) {
                $value = sanitize_textarea_field($value);
            } else {
                $value = sanitize_text_field($value);
            }

            update_post_meta($post_id, "_yaya_contact_{$section_key}_{$field_key}", $value);
        }
    }
}
add_action('save_post_page', 'yaya_save_contact_meta_box');
