<?php /* Template Name: About */ ?>
<?php get_header(); ?>

<div class="page-wrap">

  <div class="about-hero">
    <div class="section-label" style="color:var(--rust)">Our Story</div>
    <h1>BUILT ON<br>TRUST &amp; CRAFT</h1>
    <p>A construction company rooted in excellence, community, and dedication to quality work.</p>
  </div>

  <div class="about-body">
    <div class="about-text reveal">
      <div class="section-label">Who We Are</div>
      <h2>MORE THAN JUST<br>A CONTRACTOR</h2>
      <p>Yaya Construct was founded with a simple mission: to build structures that people are proud to inhabit, work in, and visit. Over the years, we've grown from a small local team into a respected construction company trusted by homeowners, developers, and businesses alike.</p>
      <p>We bring together the best tradespeople, the finest materials, and a management approach built on transparency and communication. Every project we deliver is a long-term investment in quality — and in the communities we serve.</p>
      <p>Whether it's a custom home, a commercial complex, or a renovation project, we approach each build with the same level of dedication and care that has earned us our reputation.</p>
      <a href="<?php echo home_url('/contact'); ?>" class="btn-primary" style="margin-top:1rem;display:inline-block">Work With Us</a>
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
      <div class="value-card reveal">
        <h3>Quality First</h3>
        <p>We never cut corners. Every joint, every pour, every finish is done right — because your structure deserves nothing less.</p>
      </div>
      <div class="value-card reveal" style="transition-delay:0.1s">
        <h3>Integrity</h3>
        <p>Honest pricing, transparent timelines, and clear communication from day one to handover. No surprises.</p>
      </div>
      <div class="value-card reveal" style="transition-delay:0.2s">
        <h3>Innovation</h3>
        <p>We stay current with modern building techniques and materials to deliver solutions that are both durable and forward-thinking.</p>
      </div>
      <div class="value-card reveal" style="transition-delay:0.3s">
        <h3>Community</h3>
        <p>We build in communities we care about. Supporting local suppliers and creating opportunities for local talent is at our core.</p>
      </div>
    </div>
  </div>

  <section class="team-section">
    <div class="section-label">The People Behind the Build</div>
    <div class="section-title">OUR TEAM</div>
    <?php
    $team_defaults = [
      1 => ['Yaya Diallo',  'Founder & CEO',   'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
      2 => ['Sarah Mensah', 'Head of Projects', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80'],
      3 => ['Marc Koné',    'Lead Engineer',    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80'],
    ];
    ?>
    <div class="team-grid">
      <?php for ($i = 1; $i <= 3; $i++):
        $name  = get_theme_mod("yaya_team{$i}_name",  $team_defaults[$i][0]);
        $role  = get_theme_mod("yaya_team{$i}_role",  $team_defaults[$i][1]);
        $photo = get_theme_mod("yaya_team{$i}_photo", $team_defaults[$i][2]);
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
