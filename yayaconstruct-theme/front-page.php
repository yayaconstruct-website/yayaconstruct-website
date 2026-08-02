<?php get_header(); ?>

<?php
$home_page_id = get_queried_object_id();
if (!$home_page_id) {
  $home_page_id = (int) get_option('page_on_front');
}

$home_defaults = function_exists('yaya_home_page_defaults') ? yaya_home_page_defaults() : [];

// Hero values
$hero_tag   = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_tag', get_theme_mod('yaya_hero_tag',   $home_defaults['hero']['tag'] ?? 'Est. in Excellence')) : get_theme_mod('yaya_hero_tag',   'Est. in Excellence');
$hero_line1 = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_line1', get_theme_mod('yaya_hero_line1', $home_defaults['hero']['line1'] ?? 'WE')) : get_theme_mod('yaya_hero_line1', 'WE');
$hero_line2 = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_line2', get_theme_mod('yaya_hero_line2', $home_defaults['hero']['line2'] ?? 'BUILD')) : get_theme_mod('yaya_hero_line2', 'BUILD');
$hero_line3 = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_line3', get_theme_mod('yaya_hero_line3', $home_defaults['hero']['line3'] ?? 'YOUR VISION')) : get_theme_mod('yaya_hero_line3', 'YOUR VISION');
$hero_sub   = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_sub',   get_theme_mod('yaya_hero_sub',   $home_defaults['hero']['sub'] ?? '')) : get_theme_mod('yaya_hero_sub', '');
$hero_cta1  = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_cta1',  get_theme_mod('yaya_hero_cta1',  $home_defaults['hero']['cta1'] ?? 'View Our Work')) : get_theme_mod('yaya_hero_cta1', 'View Our Work');
$hero_cta2  = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_cta2',  get_theme_mod('yaya_hero_cta2',  $home_defaults['hero']['cta2'] ?? 'Get a Quote')) : get_theme_mod('yaya_hero_cta2', 'Get a Quote');
$hero_cta1_url = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_cta1_url', $home_defaults['hero']['cta1_url'] ?? home_url('/projects')) : home_url('/projects');
$hero_cta2_url = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_hero_cta2_url', $home_defaults['hero']['cta2_url'] ?? home_url('/contact')) : home_url('/contact');
// Default is a real project photo (Inkim Suites, on the Aegean) rather than a
// generic stock stand-in. Still overridable via the Customizer.
$hero_img   = get_theme_mod('yaya_hero_image', 'https://www.yayaconstruct.com/wp-content/uploads/2026/04/IMG_0103.png');

$featured_label = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_label', $home_defaults['featured']['label'] ?? 'Featured Work') : 'Featured Work';
$featured_button_text = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_button_text', $home_defaults['featured']['button_text'] ?? 'Explore All Projects') : 'Explore All Projects';
$featured_button_url = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_button_url', $home_defaults['featured']['button_url'] ?? home_url('/projects')) : home_url('/projects');
$featured_empty_title = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_empty_title', $home_defaults['featured']['empty_title'] ?? 'BUILT WITH PURPOSE, CRAFTED WITH PRIDE') : 'BUILT WITH PURPOSE, CRAFTED WITH PRIDE';
$featured_empty_text = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_empty_text', $home_defaults['featured']['empty_text'] ?? '') : '';
?>

<!-- Hero -->
<section class="hero" style="--hero-bg: url('<?php echo esc_url($hero_img); ?>')">
  <div class="hero-bg"></div>
  <p class="hero-tag"><?php echo esc_html($hero_tag); ?></p>
  <h1>
    <?php echo esc_html($hero_line1); ?><br>
    <?php // <em> means emphasis; this is purely the brand colour accent. ?>
    <span class="hero-accent"><?php echo esc_html($hero_line2); ?></span><br>
    <?php echo esc_html($hero_line3); ?>
  </h1>
  <p class="hero-sub"><?php echo esc_html($hero_sub); ?></p>
  <div class="hero-cta">
    <a href="<?php echo esc_url($hero_cta1_url); ?>" class="btn-primary"><?php echo esc_html($hero_cta1); ?></a>
    <a href="<?php echo esc_url($hero_cta2_url); ?>"  class="btn-outline"><?php echo esc_html($hero_cta2); ?></a>
  </div>
  <div class="hero-scroll" aria-hidden="true">
    <div class="scroll-line"></div>
    Scroll
  </div>
</section>

<!-- Featured Project -->
<?php
// "Coming soon" placeholders are skipped — the homepage showcase should be
// finished work, not an empty card.
$featured = new WP_Query([
  'post_type'      => 'project',
  'posts_per_page' => 1,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'meta_query'     => [
    'relation' => 'OR',
    ['key' => '_yaya_project_coming_soon', 'compare' => 'NOT EXISTS'],
    ['key' => '_yaya_project_coming_soon', 'value' => '1', 'compare' => '!='],
  ],
]);
$featured_id = $featured->have_posts() ? $featured->posts[0]->ID : null;
wp_reset_postdata();
if ($featured_id):
  // Same resolution as the project cards: featured image, then the project's
  // own gallery, so the homepage stops falling back to a stock photo.
  $feat_img = function_exists('yaya_project_card_image')
    ? yaya_project_card_image($featured_id, 'large')
    : get_the_post_thumbnail_url($featured_id, 'large');
  // Same spec fields as the project page and the index detail line — one
  // definition (functions.php) drives all three, so this stays in sync with
  // whatever fields an editor has filled in.
  $feat_spec = function_exists('yaya_project_spec_rows') ? yaya_project_spec_rows($featured_id) : [];
  $feat_link = get_permalink($featured_id);
  // The whole project body used to be printed here, inside a <p>. Use the
  // excerpt instead — manual if set, auto-generated otherwise — and bound it.
  $feat_text = trim(get_the_excerpt($featured_id));
  if ($feat_text !== '') {
    $feat_text = wp_trim_words($feat_text, 45, '…');
  }
  if ($feat_text === '') {
    $feat_text = $featured_empty_text ?: 'Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.';
  }
?>
<section class="home-project" aria-labelledby="featured-title">
  <div class="home-project-img reveal">
    <?php // Decorative here: the heading link right beside it already names the
          // project, so an alt would make screen readers announce it twice. ?>
    <a href="<?php echo esc_url($feat_link); ?>" tabindex="-1" aria-hidden="true">
      <?php if ($feat_img): ?>
        <img src="<?php echo esc_url($feat_img); ?>" alt="" loading="lazy" decoding="async" />
      <?php else: ?>
        <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="" loading="lazy" decoding="async" />
      <?php endif; ?>
    </a>
  </div>
  <div class="home-project-content reveal">
    <p class="section-label"><?php echo esc_html($featured_label); ?></p>
    <h2 id="featured-title" class="section-title">
      <a class="home-project-link" href="<?php echo esc_url($feat_link); ?>"><?php echo esc_html(get_the_title($featured_id)); ?></a>
    </h2>
    <?php if ($feat_spec): ?>
      <p class="home-project-meta"><?php echo esc_html(implode(' · ', wp_list_pluck($feat_spec, 'value'))); ?></p>
    <?php endif; ?>
    <p><?php echo esc_html($feat_text); ?></p>
    <a href="<?php echo esc_url($featured_button_url); ?>" class="btn-primary"><?php echo esc_html($featured_button_text); ?></a>
  </div>
</section>
<?php else: ?>
<section class="home-project" aria-labelledby="featured-title">
  <div class="home-project-img reveal">
    <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="" loading="lazy" decoding="async" />
  </div>
  <div class="home-project-content reveal">
    <p class="section-label"><?php echo esc_html($featured_label); ?></p>
    <h2 id="featured-title" class="section-title"><?php echo nl2br(esc_html($featured_empty_title)); ?></h2>
    <p><?php echo esc_html($featured_empty_text ?: 'Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.'); ?></p>
    <a href="<?php echo esc_url($featured_button_url); ?>" class="btn-primary"><?php echo esc_html($featured_button_text); ?></a>
  </div>
</section>
<?php endif; ?>

<?php
// Local SEO: a contractor's home page carried no structured data at all, so
// search engines had nothing to build a business listing from.
$yaya_schema = [
  '@context'    => 'https://schema.org',
  '@type'       => 'GeneralContractor',
  'name'        => get_bloginfo('name'),
  'url'         => home_url('/'),
  'description' => wp_strip_all_tags($hero_sub ?: get_bloginfo('description')),
];
if (has_custom_logo()) {
  $yaya_logo_src = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
  if ($yaya_logo_src) {
    $yaya_schema['logo'] = $yaya_logo_src[0];
  }
} else {
  $yaya_schema['logo'] = get_template_directory_uri() . '/images/logo.png';
}
?>
<script type="application/ld+json"><?php echo wp_json_encode($yaya_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

<?php get_footer(); ?>
