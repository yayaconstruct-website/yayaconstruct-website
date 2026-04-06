<?php get_header(); ?>

<?php
// Hero values
$hero_tag   = get_theme_mod('yaya_hero_tag',   'Est. in Excellence');
$hero_line1 = get_theme_mod('yaya_hero_line1', 'WE');
$hero_line2 = get_theme_mod('yaya_hero_line2', 'BUILD');
$hero_line3 = get_theme_mod('yaya_hero_line3', 'YOUR VISION');
$hero_sub   = get_theme_mod('yaya_hero_sub',   'From groundbreaking to grand opening — Yaya Construct delivers construction that lasts generations.');
$hero_cta1  = get_theme_mod('yaya_hero_cta1',  'View Our Work');
$hero_cta2  = get_theme_mod('yaya_hero_cta2',  'Get a Quote');
$hero_img   = get_theme_mod('yaya_hero_image', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1600&q=80');

// Stats
$stats = [];
for ($i = 1; $i <= 4; $i++) {
  $defaults = [1 => ['150+','Projects Completed'], 2 => ['12+','Years of Experience'], 3 => ['98%','Client Satisfaction'], 4 => ['40+','Skilled Professionals']];
  $stats[] = [
    'num'   => get_theme_mod("yaya_stat{$i}_num",   $defaults[$i][0]),
    'label' => get_theme_mod("yaya_stat{$i}_label", $defaults[$i][1]),
  ];
}
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
    <a href="<?php echo home_url('/projects'); ?>" class="btn-primary"><?php echo esc_html($hero_cta1); ?></a>
    <a href="<?php echo home_url('/contact'); ?>"  class="btn-outline"><?php echo esc_html($hero_cta2); ?></a>
  </div>
  <div class="hero-scroll">
    <div class="scroll-line"></div>
    Scroll
  </div>
</section>

<!-- Stats -->
<div class="stats-bar">
  <?php foreach ($stats as $stat): ?>
  <div class="stat-item">
    <div class="stat-num"><?php echo esc_html($stat['num']); ?></div>
    <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Services -->
<section class="section">
  <div class="section-label">What We Do</div>
  <div class="section-title">OUR SERVICES</div>
  <div class="services-grid">

    <div class="service-card">
      <div class="service-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2 20h20"/><path d="M6 20V9"/><path d="M18 20V9"/>
          <path d="M1 9l11-7 11 7"/><path d="M9 20v-6h6v6"/>
        </svg>
      </div>
      <div class="service-title">General Construction</div>
      <p class="service-text">Full-cycle construction management from planning to handover, delivered on time and within budget.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>
        </svg>
      </div>
      <div class="service-title">Commercial Buildings</div>
      <p class="service-text">Office complexes, retail centers, warehouses, and industrial facilities built to the highest standards.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 21h18M5 21V9.5L12 3l7 6.5V21"/><path d="M9 21v-6h6v6"/>
          <path d="M9 12h.01M15 12h.01"/>
        </svg>
      </div>
      <div class="service-title">Residential Projects</div>
      <p class="service-text">Custom homes, apartment buildings, and residential renovations crafted with care and precision.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
      </div>
      <div class="service-title">Renovation &amp; Refit</div>
      <p class="service-text">Breathing new life into existing structures with expert renovation, retrofitting, and restoration work.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/>
          <line x1="12" y1="2" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="22"/>
          <line x1="2" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="22" y2="12"/>
        </svg>
      </div>
      <div class="service-title">Design &amp; Build</div>
      <p class="service-text">Integrated design-build solutions combining architectural vision with construction expertise under one roof.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
          <path d="M9 12h6M9 16h4"/>
        </svg>
      </div>
      <div class="service-title">Project Management</div>
      <p class="service-text">Professional oversight, scheduling, and coordination for complex multi-phase construction projects.</p>
    </div>

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
  <div class="home-project-img">
    <?php if ($feat_img): ?>
      <img src="<?php echo esc_url($feat_img); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
    <?php else: ?>
      <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="Featured project" loading="lazy" />
    <?php endif; ?>
  </div>
  <div class="home-project-content">
    <div class="section-label">Featured Work</div>
    <div class="section-title">
      <?php the_title(); ?>
      <?php if ($feat_loc): ?>
        <span style="display:block;font-size:1rem;color:var(--rust);margin-top:0.5rem;letter-spacing:2px"><?php echo esc_html($feat_loc); ?><?php if ($feat_year): ?>, <?php echo esc_html($feat_year); ?><?php endif; ?></span>
      <?php endif; ?>
    </div>
    <?php if (get_the_content()): ?>
      <p><?php echo wp_kses_post(get_the_content()); ?></p>
    <?php else: ?>
      <p>Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.</p>
    <?php endif; ?>
    <a href="<?php echo home_url('/projects'); ?>" class="btn-primary">Explore All Projects</a>
  </div>
</div>
<?php wp_reset_postdata(); else: ?>
<div class="home-project">
  <div class="home-project-img">
    <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="Featured project" loading="lazy" />
  </div>
  <div class="home-project-content">
    <div class="section-label">Featured Work</div>
    <div class="section-title">BUILT WITH PURPOSE, <em style="font-style:normal;color:var(--rust)">CRAFTED WITH PRIDE</em></div>
    <p>Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.</p>
    <a href="<?php echo home_url('/projects'); ?>" class="btn-primary">Explore All Projects</a>
  </div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
