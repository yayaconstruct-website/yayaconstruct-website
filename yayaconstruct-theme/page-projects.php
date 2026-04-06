<?php /* Template Name: Projects */ ?>
<?php get_header(); ?>

<?php
$projects_defaults = function_exists('yaya_projects_page_defaults') ? yaya_projects_page_defaults() : [];
$projects_title = nl2br(esc_html(get_the_title()));
$projects_intro = has_excerpt() ? get_the_excerpt() : '';
$projects_content = get_post_field('post_content', get_the_ID());
$projects_hero_label_default = $projects_defaults['hero_label'] ?? 'Portfolio';
$projects_filter_label_default = $projects_defaults['filter_label'] ?? 'All Projects';
$projects_empty_state_default = $projects_defaults['empty_state'] ?? 'Our portfolio is being updated. Check back soon.';
$projects_hero_label = function_exists('yaya_get_projects_page_field')
  ? yaya_get_projects_page_field(get_the_ID(), '_yaya_projects_hero_label', $projects_hero_label_default)
  : $projects_hero_label_default;
$projects_filter_label = function_exists('yaya_get_projects_page_field')
  ? yaya_get_projects_page_field(get_the_ID(), '_yaya_projects_filter_label', $projects_filter_label_default)
  : $projects_filter_label_default;
$projects_empty_state = function_exists('yaya_get_projects_page_field')
  ? yaya_get_projects_page_field(get_the_ID(), '_yaya_projects_empty_state', $projects_empty_state_default)
  : $projects_empty_state_default;
?>

<div class="page-wrap">

  <div class="projects-hero">
    <div class="section-label reveal" style="color:var(--rust)"><?php echo esc_html($projects_hero_label); ?></div>
    <h1 class="reveal" style="transition-delay:0.15s"><?php echo wp_kses($projects_title, ['br' => []]); ?></h1>
    <?php if ($projects_intro) : ?>
      <p class="reveal" style="transition-delay:0.25s"><?php echo esc_html($projects_intro); ?></p>
    <?php endif; ?>
  </div>

  <?php
  $cats = get_terms(['taxonomy' => 'project_category', 'hide_empty' => true]);
  $projects = new WP_Query(['post_type' => 'project', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC']);
  ?>

  <?php if (trim((string) $projects_content) !== '') : ?>
  <div class="default-page-content" style="padding-top:0;padding-bottom:2rem;">
    <div class="default-page-body">
      <?php the_content(); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($cats && !is_wp_error($cats)): ?>
  <div class="filter-bar reveal" style="transition-delay:0.3s">
    <button class="filter-btn active" onclick="filterProjects('all', this)"><?php echo esc_html($projects_filter_label); ?></button>
    <?php foreach ($cats as $cat): ?>
      <button class="filter-btn" onclick="filterProjects('<?php echo esc_js($cat->slug); ?>', this)"><?php echo esc_html($cat->name); ?></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="projects-grid" id="projects-grid">
    <?php if (!$projects->have_posts()): ?>
      <div class="projects-empty">
        <p><?php echo esc_html($projects_empty_state); ?></p>
      </div>
    <?php else: ?>
      <?php $i = 0; while ($projects->have_posts()): $projects->the_post();
        $p_cats   = get_the_terms(get_the_ID(), 'project_category');
        $cat_slug = $p_cats ? $p_cats[0]->slug : 'other';
        $cat_name = $p_cats ? $p_cats[0]->name : '';
        $location = get_post_meta(get_the_ID(), 'project_location', true);
        $year     = get_post_meta(get_the_ID(), 'project_year', true);
        $img      = get_the_post_thumbnail_url(get_the_ID(), 'large');
        $card_classes = 'project-card';
        if ($i === 4) {
          $card_classes .= ' project-card--wide';
        }
        if (!$img) {
          $card_classes .= ' project-card--no-image';
        }
        $i++;
      ?>
      <a href="<?php the_permalink(); ?>"
         class="<?php echo esc_attr($card_classes); ?>"
         data-cat="<?php echo esc_attr($cat_slug); ?>">
        <?php if ($img): ?>
          <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
        <?php else: ?>
          <div class="project-card-placeholder">
            <span>Project image coming soon</span>
          </div>
        <?php endif; ?>
        <div class="project-overlay">
          <?php if ($cat_name): ?><div class="project-cat"><?php echo esc_html($cat_name); ?></div><?php endif; ?>
          <div class="project-name"><?php the_title(); ?></div>
          <?php if ($location || $year): ?>
            <div class="project-loc">
              <?php if ($location): ?>&#x1F4CD; <?php echo esc_html($location); ?><?php endif; ?>
              <?php if ($location && $year): ?>, <?php endif; ?>
              <?php echo esc_html($year); ?>
            </div>
          <?php endif; ?>
        </div>
      </a>
      <?php endwhile; wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>

</div>

<script>
  function filterProjects(cat, btn) {
    document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var cards = document.querySelectorAll('.project-card');

    cards.forEach(function(card) {
      if (card.style.display !== 'none') {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
      }
    });

    setTimeout(function() {
      cards.forEach(function(card) {
        if (cat === 'all' || card.dataset.cat === cat) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
          card.style.opacity = '';
          card.style.transform = '';
        }
      });
      requestAnimationFrame(function() {
        requestAnimationFrame(function() {
          cards.forEach(function(card) {
            if (card.style.display !== 'none') {
              card.style.opacity = '1';
              card.style.transform = '';
            }
          });
        });
      });
    }, 350);
  }
</script>

<?php get_footer(); ?>
