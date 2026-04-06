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
$hero_img   = get_theme_mod('yaya_hero_image', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1600&q=80');

// Stats
$stats = [];
for ($i = 1; $i <= 4; $i++) {
  $defaults = [1 => ['150+','Projects Completed'], 2 => ['12+','Years of Experience'], 3 => ['98%','Client Satisfaction'], 4 => ['40+','Skilled Professionals']];
  $stats[] = [
    'num'   => function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, "_yaya_home_stat_{$i}_num", get_theme_mod("yaya_stat{$i}_num",   $defaults[$i][0])) : get_theme_mod("yaya_stat{$i}_num",   $defaults[$i][0]),
    'label' => function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, "_yaya_home_stat_{$i}_label", get_theme_mod("yaya_stat{$i}_label", $defaults[$i][1])) : get_theme_mod("yaya_stat{$i}_label", $defaults[$i][1]),
  ];
}

$services_section_label = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_services_section_label', $home_defaults['services']['section_label'] ?? 'What We Do') : 'What We Do';
$services_section_title = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_services_section_title', $home_defaults['services']['section_title'] ?? 'OUR SERVICES') : 'OUR SERVICES';
$featured_label = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_label', $home_defaults['featured']['label'] ?? 'Featured Work') : 'Featured Work';
$featured_button_text = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_button_text', $home_defaults['featured']['button_text'] ?? 'Explore All Projects') : 'Explore All Projects';
$featured_button_url = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_button_url', $home_defaults['featured']['button_url'] ?? home_url('/projects')) : home_url('/projects');
$featured_empty_title = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_empty_title', $home_defaults['featured']['empty_title'] ?? 'BUILT WITH PURPOSE, CRAFTED WITH PRIDE') : 'BUILT WITH PURPOSE, CRAFTED WITH PRIDE';
$featured_empty_text = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, '_yaya_home_featured_empty_text', $home_defaults['featured']['empty_text'] ?? '') : '';
?>

<!-- Hero -->
<section class="hero" style="--hero-bg: url('<?php echo esc_url($hero_img); ?>')">
  <div class="hero-bg"></div>
  <div class="hero-tag"><?php echo esc_html($hero_tag); ?></div>
  <h1>
    <?php echo esc_html($hero_line1); ?><br>
    <em><?php echo esc_html($hero_line2); ?></em><br>
    <?php echo esc_html($hero_line3); ?>
  </h1>
  <p class="hero-sub"><?php echo esc_html($hero_sub); ?></p>
  <div class="hero-cta">
    <a href="<?php echo esc_url($hero_cta1_url); ?>" class="btn-primary"><?php echo esc_html($hero_cta1); ?></a>
    <a href="<?php echo esc_url($hero_cta2_url); ?>"  class="btn-outline"><?php echo esc_html($hero_cta2); ?></a>
  </div>
  <div class="hero-scroll">
    <div class="scroll-line"></div>
    Scroll
  </div>
</section>

<!-- Stats -->
<div class="stats-bar">
  <?php foreach ($stats as $k => $stat): ?>
  <div class="stat-item reveal" style="transition-delay: <?php echo ($k * 0.1); ?>s">
    <div class="stat-num"><?php echo esc_html($stat['num']); ?></div>
    <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
  </div>
  <?php endforeach; ?>
</div>

<?php
// Service icons (SVG, indexed 1–6)
$service_icons = [
  1 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20"/><path d="M6 20V9"/><path d="M18 20V9"/><path d="M1 9l11-7 11 7"/><path d="M9 20v-6h6v6"/></svg>',
  2 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>',
  3 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V9.5L12 3l7 6.5V21"/><path d="M9 21v-6h6v6"/><path d="M9 12h.01M15 12h.01"/></svg>',
  4 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
  5 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="22"/><line x1="2" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="22" y2="12"/></svg>',
  6 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6M9 16h4"/></svg>',
];
$service_defaults = [
  1 => ['General Construction', 'Full-cycle construction management from planning to handover, delivered on time and within budget.'],
  2 => ['Commercial Buildings', 'Office complexes, retail centers, warehouses, and industrial facilities built to the highest standards.'],
  3 => ['Residential Projects', 'Custom homes, apartment buildings, and residential renovations crafted with care and precision.'],
  4 => ['Renovation & Refit',   'Breathing new life into existing structures with expert renovation, retrofitting, and restoration work.'],
  5 => ['Design & Build',       'Integrated design-build solutions combining architectural vision with construction expertise under one roof.'],
  6 => ['Project Management',   'Professional oversight, scheduling, and coordination for complex multi-phase construction projects.'],
];
?>
<!-- Services -->
<section class="section">
  <div class="section-label reveal"><?php echo esc_html($services_section_label); ?></div>
  <div class="section-title reveal" style="transition-delay:0.1s"><?php echo esc_html($services_section_title); ?></div>
  <div class="services-grid">
    <?php for ($i = 1; $i <= 6; $i++):
      $svc_title = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, "_yaya_home_service_{$i}_title", get_theme_mod("yaya_service{$i}_title", $service_defaults[$i][0])) : get_theme_mod("yaya_service{$i}_title", $service_defaults[$i][0]);
      $svc_text  = function_exists('yaya_get_home_page_field') && $home_page_id ? yaya_get_home_page_field($home_page_id, "_yaya_home_service_{$i}_text",  get_theme_mod("yaya_service{$i}_text",  $service_defaults[$i][1])) : get_theme_mod("yaya_service{$i}_text",  $service_defaults[$i][1]);
      $delay     = round(($i - 1) * 0.08, 2);
    ?>
    <div class="service-card reveal" style="transition-delay:<?php echo $delay; ?>s">
      <div class="service-icon"><?php echo $service_icons[$i]; ?></div>
      <div class="service-title"><?php echo esc_html($svc_title); ?></div>
      <p class="service-text"><?php echo esc_html($svc_text); ?></p>
    </div>
    <?php endfor; ?>
  </div>
</section>

<!-- Featured Project -->
<?php
$featured = new WP_Query(['post_type' => 'project', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC']);
if ($featured->have_posts()):
  $featured->the_post();
  $feat_img = get_the_post_thumbnail_url(get_the_ID(), 'large');
  $feat_loc = get_post_meta(get_the_ID(), 'project_location', true);
  $feat_year = get_post_meta(get_the_ID(), 'project_year', true);
?>
<div class="home-project">
  <div class="home-project-img reveal">
    <?php if ($feat_img): ?>
      <img src="<?php echo esc_url($feat_img); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
    <?php else: ?>
      <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="Featured project" loading="lazy" />
    <?php endif; ?>
  </div>
  <div class="home-project-content reveal" style="transition-delay:0.2s">
    <div class="section-label"><?php echo esc_html($featured_label); ?></div>
    <div class="section-title">
      <?php the_title(); ?>
      <?php if ($feat_loc): ?>
        <span style="display:block;font-size:1rem;color:var(--rust);margin-top:0.5rem;letter-spacing:2px"><?php echo esc_html($feat_loc); ?><?php if ($feat_year): ?>, <?php echo esc_html($feat_year); ?><?php endif; ?></span>
      <?php endif; ?>
    </div>
    <?php if (get_the_content()): ?>
      <p><?php echo wp_kses_post(get_the_content()); ?></p>
    <?php else: ?>
      <p><?php echo esc_html($featured_empty_text ?: 'Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.'); ?></p>
    <?php endif; ?>
    <a href="<?php echo esc_url($featured_button_url); ?>" class="btn-primary"><?php echo esc_html($featured_button_text); ?></a>
  </div>
</div>
<?php wp_reset_postdata(); else: ?>
<div class="home-project">
  <div class="home-project-img reveal">
    <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="Featured project" loading="lazy" />
  </div>
  <div class="home-project-content reveal" style="transition-delay:0.2s">
    <div class="section-label"><?php echo esc_html($featured_label); ?></div>
    <div class="section-title"><?php echo nl2br(esc_html($featured_empty_title)); ?></div>
    <p><?php echo esc_html($featured_empty_text ?: 'Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.'); ?></p>
    <a href="<?php echo esc_url($featured_button_url); ?>" class="btn-primary"><?php echo esc_html($featured_button_text); ?></a>
  </div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
