<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

<div style="padding-top:70px">

  <div class="contact-hero">
    <div class="section-label" style="color:var(--rust)">Get In Touch</div>
    <h1>LET'S BUILD<br>SOMETHING GREAT</h1>
  </div>

  <div class="contact-body">
    <div class="contact-info">
      <div class="section-label">Contact</div>
      <h2>REACH OUT<br>TO US</h2>
      <div class="contact-detail">
        <div class="contact-item">
          <div class="contact-icon">📍</div>
          <div>
            <div class="contact-item-label">Office Address</div>
            <div class="contact-item-value">123 Construction Ave<br>Building District, City 10001</div>
          </div>
        </div>
        <div class="contact-item">
          <div class="contact-icon">📞</div>
          <div>
            <div class="contact-item-label">Phone</div>
            <div class="contact-item-value">+1 (555) 000-0000</div>
          </div>
        </div>
        <div class="contact-item">
          <div class="contact-icon">✉️</div>
          <div>
            <div class="contact-item-label">Email</div>
            <div class="contact-item-value">info@yayaconstruct.com</div>
          </div>
        </div>
        <div class="contact-item">
          <div class="contact-icon">🕐</div>
          <div>
            <div class="contact-item-label">Working Hours</div>
            <div class="contact-item-value">Mon–Fri: 7:00 AM – 6:00 PM<br>Sat: 8:00 AM – 2:00 PM</div>
          </div>
        </div>
      </div>
      <div class="contact-item-label" style="margin-bottom:0.8rem">Follow Us</div>
      <div class="social-links">
        <a class="social-link" href="https://www.instagram.com/yayaconstruct/" target="_blank">📷</a>
        <a class="social-link" href="#" target="_blank">in</a>
        <a class="social-link" href="#" target="_blank">f</a>
      </div>
    </div>

    <div class="contact-form">
      <h2>SEND US<br>A MESSAGE</h2>
      <div class="form-row">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" placeholder="John" id="cf-first" />
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" placeholder="Smith" id="cf-last" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Email</label>
          <input type="email" placeholder="you@email.com" id="cf-email" />
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="tel" placeholder="+1 555 000 0000" id="cf-phone" />
        </div>
      </div>
      <div class="form-group">
        <label>Project Type</label>
        <select id="cf-type">
          <option value="">Select a service...</option>
          <option>General Construction</option>
          <option>Commercial Building</option>
          <option>Residential Project</option>
          <option>Renovation &amp; Refit</option>
          <option>Design &amp; Build</option>
          <option>Project Management</option>
          <option>Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Message</label>
        <textarea placeholder="Tell us about your project..." id="cf-message"></textarea>
      </div>
      <button class="btn-primary" onclick="submitForm()">Send Message &rarr;</button>
      <div class="form-success" id="form-success">
        &#10003; Thank you! Your message has been received. We'll be in touch within 24 hours.
      </div>
    </div>
  </div>

</div>

<script>
async function submitForm() {
  const fields = ['cf-first','cf-last','cf-email','cf-message'];
  let valid = true;
  fields.forEach(id => {
    const el = document.getElementById(id);
    if (!el.value.trim()) { el.style.borderColor = 'var(--rust)'; valid = false; }
    else { el.style.borderColor = ''; }
  });
  if (!valid) return;
  const btn = document.querySelector('.contact-form .btn-primary');
  btn.textContent = 'Sending...';
  btn.disabled = true;
  const formData = new FormData();
  formData.append('action', 'yaya_contact');
  formData.append('name', document.getElementById('cf-first').value + ' ' + document.getElementById('cf-last').value);
  formData.append('email', document.getElementById('cf-email').value);
  formData.append('message', document.getElementById('cf-message').value);
  try {
    const res = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData });
    const data = await res.json();
    btn.style.display = 'none';
    document.getElementById('form-success').style.display = 'block';
  } catch(e) {
    btn.disabled = false;
    btn.textContent = 'Send Message \u2192';
  }
}
</script>

<?php get_footer(); ?>
