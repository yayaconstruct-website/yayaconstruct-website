<?php get_header(); ?>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-tag">Est. in Excellence</div>
    <h1>WE<br><em>BUILD</em><br>YOUR VISION</h1>
    <p class="hero-sub">From groundbreaking to grand opening — Yaya Construct delivers construction that lasts generations.</p>
    <div class="hero-cta">
      <a href="<?php echo home_url('/projects'); ?>" class="btn-primary">View Our Work</a>
      <a href="<?php echo home_url('/contact'); ?>" class="btn-outline">Get a Quote</a>
    </div>
    <div class="hero-scroll">
      <div class="scroll-line"></div>
      Scroll
    </div>
  </section>

  <!-- Stats -->
  <div class="stats-bar">
    <div class="stat-item">
      <div class="stat-num">150+</div>
      <div class="stat-label">Projects Completed</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">12+</div>
      <div class="stat-label">Years of Experience</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">98%</div>
      <div class="stat-label">Client Satisfaction</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">40+</div>
      <div class="stat-label">Skilled Professionals</div>
    </div>
  </div>

  <!-- Services -->
  <section class="section">
    <div class="section-label">What We Do</div>
    <div class="section-title">OUR SERVICES</div>
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">🏗️</div>
        <div class="service-title">General Construction</div>
        <p class="service-text">Full-cycle construction management from planning to handover, delivered on time and within budget.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">🏢</div>
        <div class="service-title">Commercial Buildings</div>
        <p class="service-text">Office complexes, retail centers, warehouses, and industrial facilities built to the highest standards.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">🏠</div>
        <div class="service-title">Residential Projects</div>
        <p class="service-text">Custom homes, apartment buildings, and residential renovations crafted with care and precision.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">🔧</div>
        <div class="service-title">Renovation &amp; Refit</div>
        <p class="service-text">Breathing new life into existing structures with expert renovation, retrofitting, and restoration work.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">📐</div>
        <div class="service-title">Design &amp; Build</div>
        <p class="service-text">Integrated design-build solutions combining architectural vision with construction expertise under one roof.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">🏛️</div>
        <div class="service-title">Project Management</div>
        <p class="service-text">Professional oversight, scheduling, and coordination for complex multi-phase construction projects.</p>
      </div>
    </div>
  </section>

  <!-- Featured Project -->
  <div class="home-project">
    <div class="home-project-img">
      <img src="https://images.unsplash.com/photo-1590725121839-892b458a74fe?w=800&q=80" alt="Featured project" />
    </div>
    <div class="home-project-content">
      <div class="section-label">Featured Work</div>
      <div class="section-title">BUILT WITH PURPOSE, <em style="font-style:normal;color:var(--rust)">CRAFTED WITH PRIDE</em></div>
      <p>Every project we take on is a testament to our commitment to quality. Our team of experienced builders, engineers, and project managers ensure every detail is executed to perfection.</p>
      <a href="<?php echo home_url('/projects'); ?>" class="btn-primary">Explore All Projects</a>
    </div>
  </div>

<?php get_footer(); ?>
