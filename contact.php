<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Contact Us';
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <div class="page-banner">
    <div class="breadcrumb"><a href="/index.php">Home</a> › Contact</div>
    <h1>Get in Touch</h1>
    <p>We'd love to hear from you. Reach out for inquiries, orders, or support.</p>
  </div>

  <div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;margin-top:0.5rem;align-items:start;">

    <!-- Contact Form -->
    <div class="content-card">
      <h2>Send us a message</h2>
      <form style="margin-top:1.5rem;" onsubmit="handleContactSubmit(event)">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="contact-name">Full Name <span>*</span></label>
            <input type="text" id="contact-name" name="name" class="form-control" placeholder="Your name" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="contact-email">Email Address <span>*</span></label>
            <input type="email" id="contact-email" name="email" class="form-control" placeholder="you@example.com" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="contact-subject">Subject</label>
          <input type="text" id="contact-subject" name="subject" class="form-control" placeholder="e.g. Order inquiry, Product question...">
        </div>
        <div class="form-group">
          <label class="form-label" for="contact-message">Message <span>*</span></label>
          <textarea id="contact-message" name="message" class="form-control" rows="6" placeholder="Tell us how we can help you..." required></textarea>
        </div>
        <button type="submit" class="button button-primary" style="padding:0.85rem 2.5rem;">Send Message</button>
      </form>
    </div>

    <!-- Contact Info -->
    <div style="display:flex;flex-direction:column;gap:1.25rem;">
      <div class="content-card">
        <h2 style="font-size:1.1rem;">📍 Location</h2>
        <p>Nairobi, Kenya</p>
      </div>
      <div class="content-card">
        <h2 style="font-size:1.1rem;">📞 Phone</h2>
        <p><a href="tel:+254700000000" style="color:var(--accent);font-weight:600;">+254 700 000 000</a></p>
      </div>
      <div class="content-card">
        <h2 style="font-size:1.1rem;">✉️ Email</h2>
        <p><a href="mailto:info@ckkollections.co.ke" style="color:var(--accent);font-weight:600;">info@ckkollections.co.ke</a></p>
      </div>
      <div class="content-card">
        <h2 style="font-size:1.1rem;">🕐 Business Hours</h2>
        <p style="margin-bottom:0.4rem;"><strong>Mon – Sat:</strong> 8am – 6pm</p>
        <p><strong>Sunday:</strong> 10am – 4pm</p>
      </div>
    </div>

  </div>

</main>

<script>
function handleContactSubmit(e) {
  e.preventDefault();
  const btn = e.target.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Sending...';
  setTimeout(function() {
    btn.textContent = '✓ Message Sent!';
    btn.style.background = '#16a34a';
    e.target.reset();
    setTimeout(function() {
      btn.disabled = false;
      btn.textContent = 'Send Message';
      btn.style.background = '';
    }, 4000);
  }, 1200);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>