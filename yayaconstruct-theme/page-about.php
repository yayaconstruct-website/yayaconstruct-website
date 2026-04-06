<?php /* Template Name: About */ ?>
<?php get_header(); ?>

<div style="padding-top:70px">

  <div class="about-hero">
    <div class="section-label" style="color:var(--rust)">Our Story</div>
    <h1>BUILT ON<br>TRUST &amp; CRAFT</h1>
    <p>A construction company rooted in excellence, community, and dedication to quality work.</p>
  </div>

  <div class="about-body">
    <div class="about-text">
      <div class="section-label">Who We Are</div>
      <h2>MORE THAN JUST<br>A CONTRACTOR</h2>
      <p>Yaya Construct was founded with a simple mission: to build structures that people are proud to inhabit, work in, and visit. Over the years, we've grown from a small local team into a respected construction company trusted by homeowners, developers, and businesses alike.</p>
      <p>We bring together the best tradespeople, the finest materials, and a management approach built on transparency and communication. Every project we deliver is a long-term investment in quality — and in the communities we serve.</p>
      <p>Whether it's a custom home, a commercial complex, or a renovation project, we approach each build with the same level of dedication and care that has earned us our reputation.</p>
      <a href="<?php echo home_url('/contact'); ?>" class="btn-primary" style="margin-top:1rem;display:inline-block">Work With Us</a>
    </div>
    <div class="about-img">
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
      <div class="value-card">
        <h3>Quality First</h3>
        <p>We never cut corners. Every joint, every pour, every finish is done right — because your structure deserves nothing less.</p>
      </div>
      <div class="value-card">
        <h3>Integrity</h3>
        <p>Honest pricing, transparent timelines, and clear communication from day one to handover. No surprises.</p>
      </div>
      <div class="value-card">
        <h3>Innovation</h3>
        <p>We stay current with modern building techniques and materials to deliver solutions that are both durable and forward-thinking.</p>
      </div>
      <div class="value-card">
        <h3>Community</h3>
        <p>We build in communities we care about. Supporting local suppliers and creating opportunities for local talent is at our core.</p>
      </div>
    </div>
  </div>

  <section class="team-section">
    <div class="section-label">The People Behind the Build</div>
    <div class="section-title">OUR TEAM</div>
    <div class="team-grid">
      <div class="team-card">
        <div class="team-avatar">
          <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80" alt="Founder" />
        </div>
        <div class="team-name">Yaya Diallo</div>
        <div class="team-role">Founder &amp; CEO</div>
      </div>
      <div class="team-card">
        <div class="team-avatar">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80" alt="Project Manager" />
        </div>
        <div class="team-name">Sarah Mensah</div>
        <div class="team-role">Head of Projects</div>
      </div>
      <div class="team-card">
        <div class="team-avatar">
          <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80" alt="Engineer" />
        </div>
        <div class="team-name">Marc Koné</div>
        <div class="team-role">Lead Engineer</div>
      </div>
    </div>
  </section>

</div>

<?php get_footer(); ?>
