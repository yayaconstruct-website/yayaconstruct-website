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
    <div class="section-label reveal" style="color:var(--aegean)"><?php echo esc_html($projects_hero_label); ?></div>
    <h1 class="reveal"><?php echo wp_kses($projects_title, ['br' => []]); ?></h1>
    <?php if ($projects_intro) : ?>
      <p class="reveal"><?php echo esc_html($projects_intro); ?></p>
    <?php endif; ?>
  </div>

  <?php
  /* ── Groups, one per region ──
     Five projects in a three-column grid left a visible empty slot, and the
     per-city grouping it sat in announced that Amsterdam and Brussels are one
     project each. The page is now a continuously numbered index grouped by
     the two latitudes the practice builds in. */
  $regions = function_exists('yaya_project_regions') ? yaya_project_regions() : [];

  $groups = [];
  foreach ($regions as $region_slug => $region) {
    $groups[$region_slug] = ['name' => $region['name'], 'slug' => $region_slug, 'items' => []];
  }

  $projects = new WP_Query(['post_type' => 'project', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC']);

  while ($projects->have_posts()) {
    $projects->the_post();
    $p_cats = get_the_terms(get_the_ID(), 'project_category');
    $cat_name = ($p_cats && !is_wp_error($p_cats)) ? $p_cats[0]->name : '';
    $cat_slug = ($p_cats && !is_wp_error($p_cats)) ? $p_cats[0]->slug : 'other';

    // A city that predates the region map, or one added in the admin since,
    // gets a group of its own rather than disappearing off the page.
    $group_slug = function_exists('yaya_project_region_for_category')
      ? yaya_project_region_for_category($cat_name)
      : '';
    if ($group_slug === '') {
      $group_slug = $cat_slug;
      if (!isset($groups[$group_slug])) {
        $groups[$group_slug] = [
          'name'  => $cat_name !== '' ? $cat_name : 'Other Projects',
          'slug'  => $group_slug,
          'items' => [],
        ];
      }
    }

    $groups[$group_slug]['items'][] = [
      'title'       => get_the_title(),
      'permalink'   => get_permalink(),
      'image'       => function_exists('yaya_project_card_image')
                        ? yaya_project_card_image(get_the_ID(), 'yaya-index-thumb')
                        : ['id' => get_post_thumbnail_id(get_the_ID()), 'url' => get_the_post_thumbnail_url(get_the_ID(), 'yaya-index-thumb')],
      'category'    => $cat_name,
      'location'    => function_exists('yaya_project_spec_value') ? yaya_project_spec_value(get_the_ID(), 'project_location') : (string) get_post_meta(get_the_ID(), 'project_location', true),
      'year'        => function_exists('yaya_project_spec_value') ? yaya_project_spec_value(get_the_ID(), 'project_year') : (string) get_post_meta(get_the_ID(), 'project_year', true),
      'scope'       => function_exists('yaya_project_spec_value') ? yaya_project_spec_value(get_the_ID(), '_yaya_project_scope') : '',
      'status'      => function_exists('yaya_project_spec_value') ? yaya_project_spec_value(get_the_ID(), '_yaya_project_status') : '',
      'coming_soon' => (bool) get_post_meta(get_the_ID(), '_yaya_project_coming_soon', true),
    ];
  }
  wp_reset_postdata();

  // Drop regions that ended up with no projects of their own.
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

  <?php if (count($groups) > 1): ?>
  <div class="filter-bar" id="projects-filter">
    <button type="button" class="filter-btn active" data-filter="all">
      <?php echo esc_html($projects_filter_label); ?>
    </button>
    <?php foreach ($groups as $group): ?>
      <button type="button" class="filter-btn" data-filter="<?php echo esc_attr($group['slug']); ?>">
        <?php echo esc_html($group['name']); ?>
      </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="project-index" id="project-index">
    <?php if (!$groups): ?>
      <div class="projects-empty">
        <p><?php echo esc_html($projects_empty_state); ?></p>
      </div>
    <?php else: ?>
      <?php
      // One running number across the whole index, so the list reads as a
      // single catalogue that happens to be sectioned.
      $entry_number = 0;
      foreach ($groups as $group):
      ?>
      <section class="project-region reveal" data-cat="<?php echo esc_attr($group['slug']); ?>" id="projects-<?php echo esc_attr($group['slug']); ?>">
        <h2 class="project-region-title"><?php echo esc_html($group['name']); ?></h2>

        <ol class="project-list">
          <?php foreach (array_values($group['items']) as $item):
            $entry_number++;

            /* The detail line is the top of the project's spec block: the first
               two fields it actually has. The city stands in for a missing
               location, unless the project is named after it. */
            $place = $item['location'];
            if ($place === '' && $item['category'] !== '' && strcasecmp($item['category'], $item['title']) !== 0) {
              $place = $item['category'];
            }
            $detail = array_values(array_filter([$place, $item['year'], $item['scope'], $item['status']], 'strlen'));
            $detail = array_slice($detail, 0, 2);
          ?>
          <li class="project-list-item">
            <a class="project-row" href="<?php echo esc_url($item['permalink']); ?>">
              <span class="project-row-index"><?php echo esc_html(str_pad((string) $entry_number, 2, '0', STR_PAD_LEFT)); ?></span>
              <span class="project-row-name"><?php echo esc_html($item['title']); ?></span>
              <?php if ($detail): ?>
                <span class="project-row-detail">
                  <?php foreach ($detail as $detail_index => $detail_part): ?>
                    <?php if ($detail_index > 0): ?><span class="project-row-sep">&middot;</span><?php endif; ?>
                    <?php echo esc_html($detail_part); ?>
                  <?php endforeach; ?>
                </span>
              <?php endif; ?>
              <?php $has_image = !empty($item['image']['id']) || $item['image']['url'] !== ''; ?>
              <span class="project-row-thumb<?php echo $has_image ? '' : ' project-row-thumb--empty'; ?>" aria-hidden="true">
                <?php if ($has_image): ?>
                  <?php yaya_render_project_image($item['image'], 'yaya-index-thumb', '(max-width: 800px) 88px, 120px'); ?>
                <?php endif; ?>
              </span>
            </a>
          </li>
          <?php endforeach; ?>
        </ol>
      </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<script>
(function () {
  var bar = document.getElementById('projects-filter');
  var wrap = document.getElementById('project-index');
  if (!bar || !wrap) { return; }

  var regions = Array.prototype.slice.call(wrap.querySelectorAll('.project-region'));

  bar.addEventListener('click', function (e) {
    var btn = e.target.closest('.filter-btn');
    if (!btn || !bar.contains(btn)) { return; }

    var cat = btn.dataset.filter;

    bar.querySelectorAll('.filter-btn').forEach(function (b) {
      b.classList.toggle('active', b === btn);
    });

    regions.forEach(function (region) {
      var show = (cat === 'all' || region.dataset.cat === cat);
      region.classList.toggle('is-hidden', !show);
      // A region revealed by the filter must not stay at opacity 0 — its
      // observer already fired, or never will while it is display:none.
      if (show) { region.classList.add('visible'); }
    });

    // Keep the newly filtered list in view instead of leaving the user
    // stranded further down the page.
    if (cat !== 'all') {
      var target = wrap.querySelector('.project-region:not(.is-hidden)');
      if (target && target.getBoundingClientRect().top < 0) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  });
})();
</script>

<?php get_footer(); ?>
