<?php /* Template Name: Projects */ ?>
<?php get_header(); ?>
<div class="page-wrap">
  <div class="projects-hero">
    <div class="section-label reveal" style="color:var(--rust)">Portfolio</div>
    <h1 class="reveal" style="transition-delay:0.15s">OUR WORK<br>SPEAKS FOR ITSELF</h1>
  </div>

  <div class="filter-bar reveal" style="transition-delay:0.3s">
    <button class="filter-btn active" onclick="filterProjects('all', this)">All Projects</button>
    <?php
    $cats = get_terms(['taxonomy' => 'project_category', 'hide_empty' => true]);
    foreach($cats as $cat): ?>
      <button class="filter-btn" onclick="filterProjects('<?php echo esc_js($cat->slug); ?>', this)"><?php echo esc_html($cat->name); ?></button>
    <?php endforeach; ?>
  </div>

  <div class="projects-grid" id="projects-grid">
    <?php
    $projects = new WP_Query(['post_type' => 'project', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC']);
    $i = 0;
    while($projects->have_posts()): $projects->the_post();
      $cats = get_the_terms(get_the_ID(), 'project_category');
      $cat_slug = $cats ? $cats[0]->slug : 'other';
      $cat_name = $cats ? $cats[0]->name : '';
      $location = get_post_meta(get_the_ID(), 'project_location', true);
      $year = get_post_meta(get_the_ID(), 'project_year', true);
      $img = get_the_post_thumbnail_url(get_the_ID(), 'project-thumb');
      $i++;
    ?>
    <a href="<?php the_permalink(); ?>" class="project-card <?php if($i === 5) echo 'project-card--wide'; ?>" data-cat="<?php echo esc_attr($cat_slug); ?>">
      <?php if($img): ?>
      <img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>" />
      <?php endif; ?>
      <div class="project-overlay">
        <?php if($cat_name): ?><div class="project-cat"><?php echo esc_html($cat_name); ?></div><?php endif; ?>
        <div class="project-name"><?php the_title(); ?></div>
        <?php if($location): ?><div class="project-loc">📍 <?php echo esc_html($location); ?><?php if($year): ?>, <?php echo esc_html($year); ?><?php endif; ?></div><?php endif; ?>
      </div>
    </a>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</div>

<script>
  function filterProjects(cat, btn) {
    document.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var cards = document.querySelectorAll('.project-card');

    // Fade out all currently visible cards
    cards.forEach(function (card) {
      if (card.style.display !== 'none') {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
      }
    });

    // After fade-out, update visibility then fade in matching cards
    setTimeout(function () {
      cards.forEach(function (card) {
        if (cat === 'all' || card.dataset.cat === cat) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
          card.style.opacity = '';
          card.style.transform = '';
        }
      });
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          cards.forEach(function (card) {
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
