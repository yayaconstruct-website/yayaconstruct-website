<script>
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
    &copy; <?php echo date('Y'); ?> Yaya Construct. All rights reserved.<br>
    <span style="color:var(--rust)">yayaconstruct.com</span>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
