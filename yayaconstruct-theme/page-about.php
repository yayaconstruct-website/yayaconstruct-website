<?php /* Template Name: About */ ?>
<?php get_header(); ?>

<?php
$about_intro = has_excerpt() ? get_the_excerpt() : 'A construction company rooted in excellence, community, and dedication to quality work.';
$about_cta_url = get_theme_mod('yaya_about_cta_url', home_url('/contact'));
$about_cta_label = get_theme_mod('yaya_about_cta_label', 'Work With Us');
$about_title = nl2br(esc_html(get_the_title()));
$about_content = get_post_field('post_content', get_the_ID());
$about_defaults = function_exists('yaya_about_page_defaults') ? yaya_about_page_defaults() : ['values' => [], 'team' => []];
?>

<div class="page-wrap">

  <div class="about-hero">
    <div class="section-label" style="color:var(--rust)">Our Story</div>
    <h1><?php echo wp_kses($about_title, ['br' => []]); ?></h1>
    <p><?php echo esc_html($about_intro); ?></p>
  </div>

  <div class="about-body">
    <div class="about-text reveal">
      <div class="section-label">Who We Are</div>
      <h2>MORE THAN JUST<br>A CONTRACTOR</h2>
      <div class="about-editor-content">
        <?php
        if (trim((string) $about_content) !== '') {
          the_content();
        } else {
          ?>
          <p>Yaya Construct was founded with a simple mission: to build structures that people are proud to inhabit, work in, and visit. Over the years, we've grown from a small local team into a respected construction company trusted by homeowners, developers, and businesses alike.</p>
          <p>We bring together the best tradespeople, the finest materials, and a management approach built on transparency and communication. Every project we deliver is a long-term investment in quality and in the communities we serve.</p>
          <p>Whether it's a custom home, a commercial complex, or a renovation project, we approach each build with the same level of dedication and care that has earned us our reputation.</p>
          <?php
        }
        ?>
      </div>
      <a href="<?php echo esc_url($about_cta_url); ?>" class="btn-primary" style="margin-top:1rem;display:inline-block"><?php echo esc_html($about_cta_label); ?></a>
    </div>
    <div class="about-img reveal" style="transition-delay:0.2s">
      <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800&q=80" alt="Our team at work" />
    </div>
  </div>

  <div class="values-section">
    <div class="section-label" style="color:var(--rust);margin-bottom:0.5rem">
      <span style="display:inline-block;width:30px;height:1px;background:var(--rust);vertical-align:middle;margin-right:0.8rem"></span>
      Our Values
    </div>
    <div class="section-title">WHAT DRIVES US</div>
    <div class="values-grid">
      <?php for ($i = 1; $i <= 4; $i++):
        $title = function_exists('yaya_get_about_page_field')
          ? yaya_get_about_page_field(get_the_ID(), "_yaya_about_value_{$i}_title", $about_defaults['values'][$i]['title'])
          : $about_defaults['values'][$i]['title'];
        $text = function_exists('yaya_get_about_page_field')
          ? yaya_get_about_page_field(get_the_ID(), "_yaya_about_value_{$i}_text", $about_defaults['values'][$i]['text'])
          : $about_defaults['values'][$i]['text'];
        $delay = round(($i - 1) * 0.1, 2);
      ?>
      <div class="value-card reveal" style="transition-delay:<?php echo esc_attr($delay); ?>s">
        <h3><?php echo esc_html($title); ?></h3>
        <p><?php echo esc_html($text); ?></p>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <section class="team-section">
    <div class="section-label">The People Behind the Build</div>
    <div class="section-title">OUR TEAM</div>
    <div class="team-grid">
      <?php for ($i = 1; $i <= 3; $i++):
        $team_name_fallback = get_theme_mod("yaya_team{$i}_name", $about_defaults['team'][$i]['name']);
        $team_role_fallback = get_theme_mod("yaya_team{$i}_role", $about_defaults['team'][$i]['role']);
        $team_photo_fallback = get_theme_mod("yaya_team{$i}_photo", $about_defaults['team'][$i]['photo']);
        $name = function_exists('yaya_get_about_page_field')
          ? yaya_get_about_page_field(get_the_ID(), "_yaya_about_team_{$i}_name", $team_name_fallback)
          : $team_name_fallback;
        $role = function_exists('yaya_get_about_page_field')
          ? yaya_get_about_page_field(get_the_ID(), "_yaya_about_team_{$i}_role", $team_role_fallback)
          : $team_role_fallback;
        $photo = function_exists('yaya_get_about_page_field')
          ? yaya_get_about_page_field(get_the_ID(), "_yaya_about_team_{$i}_photo", $team_photo_fallback)
          : $team_photo_fallback;
        $delay = round(($i - 1) * 0.12, 2);
      ?>
      <div class="team-card reveal" style="transition-delay:<?php echo $delay; ?>s">
        <div class="team-avatar">
          <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
        </div>
        <div class="team-name"><?php echo esc_html($name); ?></div>
        <div class="team-role"><?php echo esc_html($role); ?></div>
      </div>
      <?php endfor; ?>
    </div>
  </section>

</div>

<?php get_footer(); ?>
