<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

<?php
$contact_defaults = function_exists('yaya_contact_page_defaults') ? yaya_contact_page_defaults() : [];
$contact_get = function ($section, $field, $fallback = '') {
  if (function_exists('yaya_get_contact_page_field')) {
    return yaya_get_contact_page_field(get_the_ID(), "_yaya_contact_{$section}_{$field}", $fallback);
  }
  return $fallback;
};

$contact_hero_label = $contact_get('hero', 'label', $contact_defaults['hero']['label'] ?? 'Get In Touch');
$contact_hero_heading = $contact_get('hero', 'heading', $contact_defaults['hero']['heading'] ?? "LET'S BUILD\nSOMETHING GREAT");

$contact_info_label = $contact_get('info', 'section_label', $contact_defaults['info']['section_label'] ?? 'Contact');
$contact_info_heading = $contact_get('info', 'heading', $contact_defaults['info']['heading'] ?? "REACH OUT\nTO US");
$address_label = $contact_get('info', 'address_label', $contact_defaults['info']['address_label'] ?? 'Office Address');
$address1 = $contact_get('info', 'address1', $contact_defaults['info']['address1'] ?? '');
$address2 = $contact_get('info', 'address2', $contact_defaults['info']['address2'] ?? '');
$phone_label = $contact_get('info', 'phone_label', $contact_defaults['info']['phone_label'] ?? 'Phone');
$phone = $contact_get('info', 'phone', $contact_defaults['info']['phone'] ?? '');
$email_label = $contact_get('info', 'email_label', $contact_defaults['info']['email_label'] ?? 'Email');
$email = $contact_get('info', 'email', $contact_defaults['info']['email'] ?? '');
$hours_label = $contact_get('info', 'hours_label', $contact_defaults['info']['hours_label'] ?? 'Working Hours');
$hours1 = $contact_get('info', 'hours1', $contact_defaults['info']['hours1'] ?? '');
$hours2 = $contact_get('info', 'hours2', $contact_defaults['info']['hours2'] ?? '');
$social_label = $contact_get('info', 'social_label', $contact_defaults['info']['social_label'] ?? 'Follow Us');
$instagram_url = $contact_get('info', 'instagram_url', $contact_defaults['info']['instagram_url'] ?? '');
$linkedin_url = $contact_get('info', 'linkedin_url', $contact_defaults['info']['linkedin_url'] ?? '');
$facebook_url = $contact_get('info', 'facebook_url', $contact_defaults['info']['facebook_url'] ?? '');

$form_heading = $contact_get('form', 'heading', $contact_defaults['form']['heading'] ?? "SEND US\nA MESSAGE");
$first_name_label = $contact_get('form', 'first_name_label', $contact_defaults['form']['first_name_label'] ?? 'First Name');
$first_name_placeholder = $contact_get('form', 'first_name_placeholder', $contact_defaults['form']['first_name_placeholder'] ?? 'John');
$last_name_label = $contact_get('form', 'last_name_label', $contact_defaults['form']['last_name_label'] ?? 'Last Name');
$last_name_placeholder = $contact_get('form', 'last_name_placeholder', $contact_defaults['form']['last_name_placeholder'] ?? 'Smith');
$form_email_label = $contact_get('form', 'email_label', $contact_defaults['form']['email_label'] ?? 'Email');
$form_email_placeholder = $contact_get('form', 'email_placeholder', $contact_defaults['form']['email_placeholder'] ?? 'you@email.com');
$form_phone_label = $contact_get('form', 'phone_label', $contact_defaults['form']['phone_label'] ?? 'Phone');
$form_phone_placeholder = $contact_get('form', 'phone_placeholder', $contact_defaults['form']['phone_placeholder'] ?? '+1 555 000 0000');
$project_type_label = $contact_get('form', 'project_type_label', $contact_defaults['form']['project_type_label'] ?? 'Project Type');
$project_type_placeholder = $contact_get('form', 'project_type_placeholder', $contact_defaults['form']['project_type_placeholder'] ?? 'Select a service...');
$project_type_options = $contact_get('form', 'project_type_options', $contact_defaults['form']['project_type_options'] ?? "General Construction\nCommercial Building\nResidential Project\nRenovation & Refit\nDesign & Build\nProject Management\nOther");
$message_label = $contact_get('form', 'message_label', $contact_defaults['form']['message_label'] ?? 'Message');
$message_placeholder = $contact_get('form', 'message_placeholder', $contact_defaults['form']['message_placeholder'] ?? 'Tell us about your project...');
$submit_label = $contact_get('form', 'submit_label', $contact_defaults['form']['submit_label'] ?? 'Send Message →');
$submit_loading_label = $contact_get('form', 'submit_loading_label', $contact_defaults['form']['submit_loading_label'] ?? 'Sending...');
$success_message = $contact_get('form', 'success_message', $contact_defaults['form']['success_message'] ?? 'Thank you! Your message has been received. We\'ll be in touch within 24 hours.');
$error_message = $contact_get('form', 'error_message', $contact_defaults['form']['error_message'] ?? 'Something went wrong. Please try again or email us directly.');
$project_type_options = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $project_type_options))));
?>

<div class="page-wrap">

  <div class="contact-hero">
    <div class="section-label" style="color:var(--rust)"><?php echo esc_html($contact_hero_label); ?></div>
    <h1><?php echo wp_kses(nl2br(esc_html($contact_hero_heading)), ['br' => []]); ?></h1>
  </div>

  <div class="contact-body">

    <div class="contact-info">
      <div class="section-label"><?php echo esc_html($contact_info_label); ?></div>
      <h2><?php echo wp_kses(nl2br(esc_html($contact_info_heading)), ['br' => []]); ?></h2>

      <div class="contact-detail">
        <div class="contact-item">
          <div class="contact-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
          </div>
          <div>
            <div class="contact-item-label"><?php echo esc_html($address_label); ?></div>
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
            <div class="contact-item-label"><?php echo esc_html($phone_label); ?></div>
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
            <div class="contact-item-label"><?php echo esc_html($email_label); ?></div>
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
            <div class="contact-item-label"><?php echo esc_html($hours_label); ?></div>
            <div class="contact-item-value"><?php echo esc_html($hours1); ?><br><?php echo esc_html($hours2); ?></div>
          </div>
        </div>
      </div>

      <div class="contact-item-label" style="margin-bottom:0.8rem"><?php echo esc_html($social_label); ?></div>
      <div class="social-links">
        <?php if ($instagram_url) : ?><a class="social-link" href="<?php echo esc_url($instagram_url); ?>" target="_blank" aria-label="Instagram" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
          </svg>
        </a><?php endif; ?>
        <?php if ($linkedin_url) : ?><a class="social-link" href="<?php echo esc_url($linkedin_url); ?>" target="_blank" aria-label="LinkedIn" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>
          </svg>
        </a><?php endif; ?>
        <?php if ($facebook_url) : ?><a class="social-link" href="<?php echo esc_url($facebook_url); ?>" target="_blank" aria-label="Facebook" rel="noopener noreferrer">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
          </svg>
        </a><?php endif; ?>
      </div>
    </div>

    <div class="contact-form">
      <h2><?php echo wp_kses(nl2br(esc_html($form_heading)), ['br' => []]); ?></h2>
      <?php wp_nonce_field('yaya_contact_nonce', 'yaya_nonce'); ?>
      <div class="form-row">
        <div class="form-group">
          <label for="cf-first"><?php echo esc_html($first_name_label); ?></label>
          <input type="text" id="cf-first" placeholder="<?php echo esc_attr($first_name_placeholder); ?>" autocomplete="given-name" />
        </div>
        <div class="form-group">
          <label for="cf-last"><?php echo esc_html($last_name_label); ?></label>
          <input type="text" id="cf-last" placeholder="<?php echo esc_attr($last_name_placeholder); ?>" autocomplete="family-name" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="cf-email"><?php echo esc_html($form_email_label); ?></label>
          <input type="email" id="cf-email" placeholder="<?php echo esc_attr($form_email_placeholder); ?>" autocomplete="email" />
        </div>
        <div class="form-group">
          <label for="cf-phone"><?php echo esc_html($form_phone_label); ?></label>
          <input type="tel" id="cf-phone" placeholder="<?php echo esc_attr($form_phone_placeholder); ?>" autocomplete="tel" />
        </div>
      </div>
      <div class="form-group">
        <label for="cf-type"><?php echo esc_html($project_type_label); ?></label>
        <select id="cf-type">
          <option value=""><?php echo esc_html($project_type_placeholder); ?></option>
          <?php foreach ($project_type_options as $option) : ?>
            <option><?php echo esc_html($option); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="cf-message"><?php echo esc_html($message_label); ?></label>
        <textarea id="cf-message" placeholder="<?php echo esc_attr($message_placeholder); ?>"></textarea>
      </div>
      <button class="btn-primary" id="cf-submit" onclick="submitContactForm()"><?php echo esc_html($submit_label); ?></button>
      <div class="form-success" id="form-success">
        &#10003; <?php echo esc_html($success_message); ?>
      </div>
      <div class="form-error" id="form-error">
        <?php echo esc_html($error_message); ?>
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
  btn.textContent = <?php echo wp_json_encode($submit_loading_label); ?>;
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
    btn.textContent = <?php echo wp_json_encode($submit_label); ?>;
    document.getElementById('form-error').style.display = 'block';
  }
}
</script>

<?php get_footer(); ?>
