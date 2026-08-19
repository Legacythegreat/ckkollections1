  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="brand-text">CK <span>Kollections</span></div>
          <p class="footer-tagline">Your go-to destination for premium clothing and quality household appliances in Kenya.</p>
          <a href="/contact.php" class="button button-primary" style="display:inline-flex;font-size:0.88rem;padding:0.6rem 1.4rem;">Get in Touch</a>
        </div>

        <div class="footer-col">
          <h4>Shop</h4>
          <a href="/category.php?slug=womens-fashion">Women's Fashion</a>
          <a href="/category.php?slug=mens-collection">Men's Collection</a>
          <a href="/category.php?slug=kitchen-appliances">Kitchen Appliances</a>
          <a href="/category.php?slug=home-appliances">Home Appliances</a>
        </div>

        <div class="footer-col">
          <h4>Information</h4>
          <a href="/about.php">About Us</a>
          <a href="/contact.php">Contact</a>
          <a href="/cart.php">My Cart</a>
          <a href="/admin/index.php">Admin Portal</a>
        </div>

        <div class="footer-col">
          <h4>We Accept</h4>
          <a href="#">M-Pesa Payments</a>
          <a href="#">Card Payments</a>
          <a href="#">Bank Transfer</a>
        </div>
      </div>

      <div class="footer-bottom">
        <p class="footer-legal">&copy; <?php echo date('Y'); ?> CK Kollections. All rights reserved.</p>
        <p class="footer-legal">Clothes &amp; Household Appliances — Kenya</p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile nav toggle
    const toggle = document.getElementById('nav-toggle');
    const nav    = document.getElementById('site-nav');
    if (toggle && nav) {
      toggle.addEventListener('click', function () {
        const expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', String(!expanded));
        nav.classList.toggle('nav-open');
      });
    }

    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(function(el) {
      setTimeout(function() {
        el.style.transition = 'opacity 0.5s ease';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 500);
      }, 5000);
    });
  </script>
</body>
</html>