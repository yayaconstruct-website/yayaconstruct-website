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
  /* ── Categories, ordered by the city order defined in functions.php ── */
  $cats = get_terms(['taxonomy' => 'project_category', 'hide_empty' => true]);
  if (!$cats || is_wp_error($cats)) {
    $cats = [];
  }
  $city_order = function_exists('yaya_project_city_order') ? yaya_project_city_order() : ['Brussels', 'Amsterdam', 'Izmir'];
  usort($cats, function ($a, $b) use ($city_order) {
    $pos_a = array_search($a->name, $city_order, true);
    $pos_b = array_search($b->name, $city_order, true);
    $pos_a = $pos_a === false ? count($city_order) : $pos_a;
    $pos_b = $pos_b === false ? count($city_order) : $pos_b;
    if ($pos_a === $pos_b) {
      return strcasecmp($a->name, $b->name);
    }
    return $pos_a <=> $pos_b;
  });

  /* ── One group per category, projects bucketed by their primary category ── */
  $groups = [];
  foreach ($cats as $cat) {
    $groups[$cat->slug] = ['name' => $cat->name, 'slug' => $cat->slug, 'items' => []];
  }

  $projects = new WP_Query(['post_type' => 'project', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC']);
  $total_projects = 0;

  while ($projects->have_posts()) {
    $projects->the_post();
    $p_cats = get_the_terms(get_the_ID(), 'project_category');
    $slug = ($p_cats && !is_wp_error($p_cats)) ? $p_cats[0]->slug : 'other';
    $name = ($p_cats && !is_wp_error($p_cats)) ? $p_cats[0]->name : 'Other Projects';

    if (!isset($groups[$slug])) {
      $groups[$slug] = ['name' => $name, 'slug' => $slug, 'items' => []];
    }

    $groups[$slug]['items'][] = [
      'title'       => get_the_title(),
      'permalink'   => get_permalink(),
      'image'       => function_exists('yaya_project_card_image')
                        ? yaya_project_card_image(get_the_ID(), 'large')
                        : get_the_post_thumbnail_url(get_the_ID(), 'large'),
      'location'    => get_post_meta(get_the_ID(), 'project_location', true),
      'year'        => get_post_meta(get_the_ID(), 'project_year', true),
      'coming_soon' => (bool) get_post_meta(get_the_ID(), '_yaya_project_coming_soon', true),
    ];
    $total_projects++;
  }
  wp_reset_postdata();

  // Drop categories that ended up with no projects of their own.
  $groups = array_filter($groups, function ($group) {
    return !empty($group['items']);
  });

  // "Coming soon" placeholders (e.g. Z-Suites) always sit at the end of
  // their group instead of wherever their post date happens to sort them.
  foreach ($groups as &$group) {
    $regular = array_values(array_filter($group['items'], function ($item) {
      return empty($item['coming_soon']);
    }));
    $coming_soon = array_values(array_filter($group['items'], function ($item) {
      return !empty($item['coming_soon']);
    }));
    $group['items'] = array_merge($regular, $coming_soon);
  }
  unset($group);
  ?>

  <?php if (trim((string) $projects_content) !== '') : ?>
  <div class="default-page-content" style="padding-top:0;padding-bottom:2rem;">
    <div class="default-page-body">
      <?php the_content(); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($groups): ?>
  <div class="filter-bar" id="projects-filter">
    <button type="button" class="filter-btn active" data-filter="all">
      <?php echo esc_html($projects_filter_label); ?>
      <span class="filter-count"><?php echo esc_html($total_projects); ?></span>
    </button>
    <?php foreach ($groups as $group): ?>
      <button type="button" class="filter-btn" data-filter="<?php echo esc_attr($group['slug']); ?>">
        <?php echo esc_html($group['name']); ?>
        <span class="filter-count"><?php echo esc_html(count($group['items'])); ?></span>
      </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="project-groups" id="project-groups">
    <?php if (!$groups): ?>
      <div class="projects-empty">
        <p><?php echo esc_html($projects_empty_state); ?></p>
      </div>
    <?php else: ?>
      <?php $group_index = 0; foreach ($groups as $group):
        $group_index++;
        $count = count($group['items']);
      ?>
      <section class="project-group" data-cat="<?php echo esc_attr($group['slug']); ?>" id="projects-<?php echo esc_attr($group['slug']); ?>">
        <header class="project-group-head reveal">
          <div class="project-group-heading">
            <span class="project-group-index"><?php echo esc_html(str_pad((string) $group_index, 2, '0', STR_PAD_LEFT)); ?></span>
            <h2 class="project-group-title"><?php echo esc_html($group['name']); ?></h2>
          </div>
          <span class="project-group-count">
            <?php echo esc_html($count); ?> <?php echo esc_html($count === 1 ? 'Project' : 'Projects'); ?>
          </span>
        </header>

        <?php
        // Small groups get their own column count so they never leave holes
        // in a three-column row.
        $grid_classes = 'project-group-grid';
        if ($count === 1) {
          $grid_classes .= ' project-group-grid--single';
        } elseif ($count === 2) {
          $grid_classes .= ' project-group-grid--pair';
        }
        ?>
        <div class="<?php echo esc_attr($grid_classes); ?>">
          <?php foreach (array_values($group['items']) as $index => $item):
            $card_classes = 'project-card';
            // The lead card of a group gets the wide treatment, but only when the
            // group is big enough that the row still fills out.
            if ($index === 0 && $count >= 3) {
              $card_classes .= ' project-card--feature';
            }
            if (!$item['image']) {
              $card_classes .= ' project-card--no-image';
            }
            if ($item['coming_soon']) {
              $card_classes .= ' project-card--coming-soon';
            }
          ?>
          <a href="<?php echo esc_url($item['permalink']); ?>" class="<?php echo esc_attr($card_classes); ?>">
            <div class="project-card-media">
              <?php if ($item['image']): ?>
                <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy" />
              <?php else: ?>
                <div class="project-card-placeholder">
                  <?php // Coming-soon cards already carry a flag in the overlay. ?>
                  <?php if (!$item['coming_soon']): ?>
                    <span>Project image coming soon</span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="project-overlay">
              <?php if ($item['coming_soon']): ?>
                <span class="project-flag">Coming soon</span>
              <?php endif; ?>
              <div class="project-name"><?php echo esc_html($item['title']); ?></div>
              <?php if ($item['location'] || $item['year']): ?>
                <div class="project-loc">
                  <?php if ($item['location']): ?>&#x1F4CD; <?php echo esc_html($item['location']); ?><?php endif; ?>
                  <?php if ($item['location'] && $item['year']): ?><span class="project-loc-sep">&middot;</span><?php endif; ?>
                  <?php echo esc_html($item['year']); ?>
                </div>
              <?php endif; ?>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<script>
(function () {
  var bar = document.getElementById('projects-filter');
  var wrap = document.getElementById('project-groups');
  if (!bar || !wrap) { return; }

  var groups = Array.prototype.slice.call(wrap.querySelectorAll('.project-group'));

  function revealInside(group) {
    group.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('visible'); });
  }

  bar.addEventListener('click', function (e) {
    var btn = e.target.closest('.filter-btn');
    if (!btn || !bar.contains(btn)) { return; }

    var cat = btn.dataset.filter;

    bar.querySelectorAll('.filter-btn').forEach(function (b) {
      b.classList.toggle('active', b === btn);
    });

    groups.forEach(function (group) {
      var show = (cat === 'all' || group.dataset.cat === cat);
      group.classList.toggle('is-hidden', !show);
      if (show) { revealInside(group); }
    });

    // Keep the newly filtered list in view instead of leaving the user
    // stranded further down the page.
    if (cat !== 'all') {
      var target = wrap.querySelector('.project-group:not(.is-hidden)');
      if (target && target.getBoundingClientRect().top < 0) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  });
})();
</script>

<?php get_footer(); ?>
