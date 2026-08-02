</main>

<script>
// Keep --nav-h matching the real nav height, so page content and the sticky
// project filter always clear the fixed header even if the logo changes size.
(function () {
  var nav = document.getElementById('main-nav');
  if (!nav) { return; }

  function syncNavHeight() {
    document.documentElement.style.setProperty('--nav-h', nav.offsetHeight + 'px');
  }

  syncNavHeight();
  window.addEventListener('resize', syncNavHeight);
  window.addEventListener('load', syncNavHeight);
})();

(function () {
  if (!('IntersectionObserver' in window)) {
    document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('visible'); });
    return;
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
})();
</script>

<footer>
  <div class="footer-logo">YAYA<span>.</span>CONSTRUCT</div>
  <div class="footer-copy">
    &copy; <?php echo wp_date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. All rights reserved.<br>
    <span style="color:var(--aegean)">yayaconstruct.com</span>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
