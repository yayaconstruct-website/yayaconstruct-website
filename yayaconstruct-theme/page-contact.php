<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

<?php
$address1 = get_theme_mod('yaya_contact_address1', '123 Construction Ave');
$address2 = get_theme_mod('yaya_contact_address2', 'Building District, City 10001');
$phone    = get_theme_mod('yaya_contact_phone',    '+1 (555) 000-0000');
$email    = get_theme_mod('yaya_contact_email',    'info@yayaconstruct.com');
$hours1   = get_theme_mod('yaya_contact_hours1',   'Mon–Fri: 7:00 AM – 6:00 PM');
$hours2   = get_theme_mod('yaya_contact_hours2',   'Sat: 8:00 AM – 2:00 PM');
?>

<div class="page-wrap">

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
          <div class="contact-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
          </div>
          <div>
            <div class="contact-item-label">Office Address</div>
            <div class="contact-item-value"><?php echo esc_html($address1); ?><br><?php echo esc_html($address2); ?></div>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.63 3.38 2 2 0 0 1 3.6 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.78a16 16 0 0 0 6.29 6.29l.97-.97a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
          </div>
          <div>
            <div class="contact-item-label">Phone</div>
            <div class="contact-item-value"><a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone)); ?>" style="color:inherit;text-decoration:none"><?php echo esc_html($phone); ?></a></div>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
            </svg>
          </div>
          <div>
            <div class="contact-item-label">Email</div>
            <div class="contact-item-value"><a href="mailto:<?php echo esc_attr($email); ?>" style="color:inherit;text-decoration:none"><?php echo esc_html($email); ?></a></div>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div>
            <div class="contact-item-label">Working Hours</div>
            <div class="contact-item-value"><?php echo esc_html($hours1); ?><br><?php echo esc_html($hours2); ?></div>
          </div>
        </div>
      </div>

      <div class="contact-item-label" style="margin-bottom:0.8rem">Follow Us</div>
      <div class="social-links">
        <a class="social-link" href="https://www.instagram.com/yayaconstruct/" target="_blank" aria-label="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
          </svg>
        </a>
        <a class="social-link" href="#" target="_blank" aria-label="LinkedIn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>
          </svg>
        </a>
        <a class="social-link" href="#" target="_blank" aria-label="Facebook">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
          </svg>
        </a>
      </div>
    </div>

    <div class="contact-form">
      <h2>SEND US<br>A MESSAGE</h2>
      <?php wp_nonce_field('yaya_contact_nonce', 'yaya_nonce'); ?>
      <div class="form-row">
        <div class="form-group">
          <label for="cf-first">First Name</label>
          <input type="text" id="cf-first" placeholder="John" autocomplete="given-name" />
        </div>
        <div class="form-group">
          <label for="cf-last">Last Name</label>
          <input type="text" id="cf-last" placeholder="Smith" autocomplete="family-name" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="cf-email">Email</label>
          <input type="email" id="cf-email" placeholder="you@email.com" autocomplete="email" />
        </div>
        <div class="form-group">
          <label for="cf-phone">Phone</label>
          <input type="tel" id="cf-phone" placeholder="+1 555 000 0000" autocomplete="tel" />
        </div>
      </div>
      <div class="form-group">
        <label for="cf-type">Project Type</label>
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
        <label for="cf-message">Message</label>
        <textarea id="cf-message" placeholder="Tell us about your project..."></textarea>
      </div>
      <button class="btn-primary" id="cf-submit" onclick="submitContactForm()">Send Message &rarr;</button>
      <div class="form-success" id="form-success">
        &#10003; Thank you! Your message has been received. We'll be in touch within 24 hours.
      </div>
      <div class="form-error" id="form-error">
        Something went wrong. Please try again or email us directly.
      </div>
    </div>

  </div>
</div>

<script>
async function submitContactForm() {
  var fields = ['cf-first', 'cf-last', 'cf-email', 'cf-message'];
  var valid = true;
  fields.forEach(function(id) {
    var el = document.getElementById(id);
    if (!el.value.trim()) { el.style.borderColor = 'var(--rust)'; valid = false; }
    else { el.style.borderColor = ''; }
  });
  if (!valid) return;

  var btn = document.getElementById('cf-submit');
  btn.textContent = 'Sending...';
  btn.disabled = true;
  document.getElementById('form-error').style.display = 'none';

  var formData = new FormData();
  formData.append('action', 'yaya_contact');
  formData.append('nonce',  document.getElementById('yaya_nonce').value);
  formData.append('name',    document.getElementById('cf-first').value + ' ' + document.getElementById('cf-last').value);
  formData.append('email',   document.getElementById('cf-email').value);
  formData.append('phone',   document.getElementById('cf-phone').value);
  formData.append('type',    document.getElementById('cf-type').value);
  formData.append('message', document.getElementById('cf-message').value);

  try {
    var res  = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData });
    var data = await res.json();
    if (data.success) {
      btn.style.display = 'none';
      document.getElementById('form-success').style.display = 'block';
    } else {
      throw new Error('send failed');
    }
  } catch(e) {
    btn.disabled = false;
    btn.textContent = 'Send Message →';
    document.getElementById('form-error').style.display = 'block';
  }
}
</script>

<?php get_footer(); ?>
