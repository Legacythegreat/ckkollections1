<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us';
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <div class="page-banner">
    <div class="breadcrumb"><a href="/index.php">Home</a> › About</div>
    <h1>About CK Kollections</h1>
    <p>Who we are and what we stand for.</p>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:0.5rem;">

    <div class="content-card">
      <h2>Our Story</h2>
      <p>CK Kollections is a Kenyan lifestyle store dedicated to bringing you the best in premium clothing and household appliances under one roof.</p>
      <p>We carefully select each product to ensure it meets our standards for quality, durability, and value — so you can shop with confidence every time.</p>
      <p>From elegant fashion pieces to smart home appliances, everything at CK Kollections is chosen to elevate your everyday life.</p>
    </div>

    <div class="content-card">
      <h2>Our Promise</h2>
      <div style="display:flex;flex-direction:column;gap:1rem;margin-top:0.5rem;">
        <div class="guarantee-item" style="font-size:0.95rem;">
          <span class="guarantee-icon">✓</span>
          <div><strong>Authentic Products</strong><br><span style="color:var(--ink-3);font-size:0.88rem;">Every item is 100% genuine and quality-inspected before sale.</span></div>
        </div>
        <div class="guarantee-item" style="font-size:0.95rem;">
          <span class="guarantee-icon">✓</span>
          <div><strong>Warranty Coverage</strong><br><span style="color:var(--ink-3);font-size:0.88rem;">Comprehensive warranty on all electronic and home appliances.</span></div>
        </div>
        <div class="guarantee-item" style="font-size:0.95rem;">
          <span class="guarantee-icon">✓</span>
          <div><strong>Nationwide Delivery</strong><br><span style="color:var(--ink-3);font-size:0.88rem;">Fast, secure shipping to all counties across Kenya.</span></div>
        </div>
        <div class="guarantee-item" style="font-size:0.95rem;">
          <span class="guarantee-icon">✓</span>
          <div><strong>Easy Returns</strong><br><span style="color:var(--ink-3);font-size:0.88rem;">7-day hassle-free return policy on all purchases.</span></div>
        </div>
      </div>
    </div>

  </div>

  <div class="content-card" style="margin-top:2rem;text-align:center;padding:3rem 2rem;">
    <h2 style="margin-bottom:1rem;">Ready to Shop?</h2>
    <p style="max-width:480px;margin:0 auto 2rem;">Explore our curated collections of fashion and home appliances — all at competitive prices with guaranteed quality.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="/category.php?slug=womens-fashion"     class="button button-primary">Shop Fashion</a>
      <a href="/category.php?slug=kitchen-appliances" class="button button-secondary">Shop Appliances</a>
    </div>
  </div>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>