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
    <div class="section-label" style="color:var(--aegean)"><?php echo esc_html($contact_hero_label); ?></div>
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

    <?php
    /* ── A REAL FORM ──
       These fields sat in a bare <div>. Enter in a text field did nothing, the
       button had no submit semantics, there was no constraint validation to
       read state from, and assistive tech was never told a form was here at
       all — it was six labelled inputs and an unrelated button. It is a <form>
       now; the script below intercepts submit and the request still goes over
       fetch, so nothing about delivery changes.

       novalidate suppresses the browser's own validation *UI*, not its parser:
       element.validity is still populated and the script reads typeMismatch
       off the email field. The bubbles have to go because they vanish on the
       next keystroke, are announced once and never again, and cannot be
       associated with the persistent per-field messages below. */
    ?>
    <form class="contact-form" id="contact-form" method="post" novalidate>
      <h2><?php echo wp_kses(nl2br(esc_html($form_heading)), ['br' => []]); ?></h2>
      <?php wp_nonce_field('yaya_contact_nonce', 'yaya_nonce'); ?>
      <div class="form-row">
        <div class="form-group">
          <label for="cf-first"><?php echo esc_html($first_name_label); ?></label>
          <input type="text" id="cf-first" name="cf-first" required aria-required="true" aria-describedby="cf-first-error" placeholder="<?php echo esc_attr($first_name_placeholder); ?>" autocomplete="given-name" />
          <p class="form-field-error" id="cf-first-error" hidden></p>
        </div>
        <div class="form-group">
          <label for="cf-last"><?php echo esc_html($last_name_label); ?></label>
          <input type="text" id="cf-last" name="cf-last" required aria-required="true" aria-describedby="cf-last-error" placeholder="<?php echo esc_attr($last_name_placeholder); ?>" autocomplete="family-name" />
          <p class="form-field-error" id="cf-last-error" hidden></p>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="cf-email"><?php echo esc_html($form_email_label); ?></label>
          <input type="email" id="cf-email" name="cf-email" required aria-required="true" aria-describedby="cf-email-error" placeholder="<?php echo esc_attr($form_email_placeholder); ?>" autocomplete="email" />
          <p class="form-field-error" id="cf-email-error" hidden></p>
        </div>
        <div class="form-group">
          <?php /* Phone and project type are optional by design — no required,
                   no aria-required, and the script does not check them. */ ?>
          <label for="cf-phone"><?php echo esc_html($form_phone_label); ?></label>
          <input type="tel" id="cf-phone" name="cf-phone" placeholder="<?php echo esc_attr($form_phone_placeholder); ?>" autocomplete="tel" />
        </div>
      </div>
      <div class="form-group">
        <label for="cf-type"><?php echo esc_html($project_type_label); ?></label>
        <select id="cf-type" name="cf-type">
          <option value=""><?php echo esc_html($project_type_placeholder); ?></option>
          <?php foreach ($project_type_options as $option) : ?>
            <option><?php echo esc_html($option); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="cf-message"><?php echo esc_html($message_label); ?></label>
        <textarea id="cf-message" name="cf-message" required aria-required="true" aria-describedby="cf-message-error" placeholder="<?php echo esc_attr($message_placeholder); ?>"></textarea>
        <p class="form-field-error" id="cf-message-error" hidden></p>
      </div>
      <?php
      /* Honeypot. Clipped out of the layout by .form-honeypot, out of the tab
         order by tabindex, and out of the accessibility tree by aria-hidden —
         a field no visitor can reach by sight, keyboard or screen reader, so
         anything in it was typed by a script. yaya_contact_form() drops those
         submissions and reports success, because a bot that is told it failed
         comes back having learned something. It is labelled anyway: the label
         is what makes a browser's own autofill leave it alone. */
      ?>
      <div class="form-honeypot" aria-hidden="true">
        <label for="cf-website">Leave this field blank</label>
        <input type="text" id="cf-website" name="website" tabindex="-1" autocomplete="off" />
      </div>
      <?php
      /* Delivery has always been JavaScript-only — there is no non-AJAX
         handler behind this. While the fields sat in a <div>, that failed
         silently; inside a real <form> the browser submits to the page itself
         and empties every field instead. Say so, and give the address. */
      ?>
      <noscript>
        <div class="form-error is-shown">
          This form needs JavaScript to send.
          <?php if ($email) : ?>
            Please email us at <a href="<?php echo esc_url('mailto:' . $email); ?>" style="color:inherit"><?php echo esc_html($email); ?></a> instead.
          <?php endif; ?>
        </div>
      </noscript>
      <button class="btn-primary" id="cf-submit"><?php echo esc_html($submit_label); ?></button>
      <?php
      /* Both boxes render empty and stay in the DOM. A live region has to be
         in the accessibility tree before its text changes or the change is not
         announced, which is why neither is display:none any more and why the
         copy is handed to the script rather than printed here. tabindex on the
         success box is so focus has somewhere to land when the button that
         currently holds it is hidden. */
      ?>
      <div class="form-success" id="form-success" role="status" aria-live="polite" tabindex="-1"><span class="form-feedback-mark" aria-hidden="true">&#10003;</span><span class="form-feedback-text"></span></div>
      <div class="form-error" id="form-error" role="alert" aria-live="assertive"><span class="form-feedback-text"></span></div>
    </form>

  </div>
</div>

<script>
(function () {
  var form = document.getElementById('contact-form');
  if (!form) { return; }

  var btn        = document.getElementById('cf-submit');
  var successBox = document.getElementById('form-success');
  var errorBox   = document.getElementById('form-error');
  var successTxt = successBox.querySelector('.form-feedback-text');
  var errorTxt   = errorBox.querySelector('.form-feedback-text');

  var SUBMIT_LABEL  = <?php echo wp_json_encode($submit_label); ?>;
  var LOADING_LABEL = <?php echo wp_json_encode($submit_loading_label); ?>;
  var SUCCESS_MSG   = <?php echo wp_json_encode($success_message); ?>;
  var ERROR_MSG     = <?php echo wp_json_encode($error_message); ?>;

  // The four fields yaya_contact_form() also insists on. Phone and project
  // type are absent deliberately — they are optional on both sides.
  var REQUIRED = [
    { id: 'cf-first',   empty: 'Enter your first name.' },
    { id: 'cf-last',    empty: 'Enter your last name.' },
    { id: 'cf-email',   empty: 'Enter your email address.',
                        malformed: 'Enter an email address in the form name@example.com.' },
    { id: 'cf-message', empty: 'Tell us what you would like to build.' }
  ];

  var sending = false;

  // aria-invalid is the machine-readable half and the message under the field
  // is the human-readable half; the border colour is the third signal, not the
  // only one. The message element is always present and always referenced by
  // aria-describedby, so the description exists before the text arrives —
  // adding the relationship at the same moment as the text is how screen
  // readers end up reading the field with no explanation attached.
  function setFieldError(el, message) {
    var note = document.getElementById(el.id + '-error');
    if (message) {
      el.setAttribute('aria-invalid', 'true');
      if (note) { note.textContent = message; note.hidden = false; }
    } else {
      el.removeAttribute('aria-invalid');
      if (note) { note.textContent = ''; note.hidden = true; }
    }
  }

  function show(box, textNode, message) {
    textNode.textContent = message;
    box.classList.add('is-shown');
  }

  function hide(box, textNode) {
    textNode.textContent = '';
    box.classList.remove('is-shown');
  }

  // Returns the first field that failed, so focus can be sent there.
  function validate() {
    var firstInvalid = null;
    REQUIRED.forEach(function (field) {
      var el = document.getElementById(field.id);
      if (!el) { return; }
      var message = '';
      if (!el.value.trim()) {
        // trim() rather than validity.valueMissing: a field holding only
        // spaces satisfies required and fails the server.
        message = field.empty;
      } else if (field.malformed && el.validity && el.validity.typeMismatch) {
        message = field.malformed;
      }
      setFieldError(el, message);
      if (message && !firstInvalid) { firstInvalid = el; }
    });
    return firstInvalid;
  }

  function busy(state) {
    sending = state;
    // aria-disabled, not disabled. Disabling the element that currently holds
    // focus drops focus to <body>, and the visitor has just pressed this
    // button. The guard at the top of the handler is what actually stops a
    // second send.
    btn.setAttribute('aria-disabled', state ? 'true' : 'false');
    btn.textContent = state ? LOADING_LABEL : SUBMIT_LABEL;
  }

  // Show what actually went wrong when the server explains itself, rather
  // than the same generic line for an expired nonce and a failed send alike.
  function fail(serverMessage) {
    show(errorBox, errorTxt, serverMessage || ERROR_MSG);
    busy(false);
  }

  // Clear a field's error the moment it stops being wrong, so a message that
  // has been acted on does not sit under a field that now reads fine.
  REQUIRED.forEach(function (field) {
    var el = document.getElementById(field.id);
    if (!el) { return; }
    el.addEventListener('input', function () {
      if (el.getAttribute('aria-invalid') !== 'true') { return; }
      var stillWrong = !el.value.trim() ||
        (field.malformed && el.validity && el.validity.typeMismatch);
      if (!stillWrong) { setFieldError(el, ''); }
    });
  });

  form.addEventListener('submit', async function (e) {
    // First statement in the handler: the form has no action, so anything that
    // throws before this reloads the page and empties every field.
    e.preventDefault();
    if (sending) { return; }

    var firstInvalid = validate();
    if (firstInvalid) {
      hide(errorBox, errorTxt);
      firstInvalid.focus();
      return;
    }

    hide(errorBox, errorTxt);
    busy(true);

    var formData = new FormData();
    formData.append('action', 'yaya_contact');
    formData.append('nonce',  document.getElementById('yaya_nonce').value);
    formData.append('name',    document.getElementById('cf-first').value + ' ' + document.getElementById('cf-last').value);
    formData.append('email',   document.getElementById('cf-email').value);
    formData.append('phone',   document.getElementById('cf-phone').value);
    formData.append('type',    document.getElementById('cf-type').value);
    formData.append('message', document.getElementById('cf-message').value);
    formData.append('website', document.getElementById('cf-website').value);

    try {
      // wp_json_encode, not esc_url: this is a JavaScript string, and the
      // HTML entities esc_url emits are not decoded inside a <script>.
      var res  = await fetch(<?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>, { method: 'POST', body: formData });
      var data = await res.json();
      if (data.success) {
        show(successBox, successTxt, SUCCESS_MSG);
        // Move focus before hiding the button, not after: focus is on the
        // button right now, and hiding the focused element leaves focus on
        // <body> with the confirmation unread and the tab order restarted
        // from the top of the document.
        successBox.focus();
        btn.style.display = 'none';
        return;
      }
      console.error('Contact form:', data.error || 'send failed');
      fail(data.error);
    } catch (err) {
      // Network failure or a non-JSON response: keep the configured copy.
      console.error('Contact form:', err);
      fail('');
    }
  });
})();
</script>

<?php get_footer(); ?>
