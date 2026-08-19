<?php
require_once __DIR__ . '/includes/functions.php';
$categories = getCategories();
$products   = getProducts();
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <!-- ── HERO ── -->
  <section class="hero">
    <div class="hero-copy">
      <p class="eyebrow">Designer Apparel &bull; Smart Home Living</p>
      <h1>Style &amp; Home,<br><em>Redefined.</em></h1>
      <p>Discover premium men's &amp; women's fashion alongside cutting-edge kitchen and household appliances — curated for modern Kenyan living.</p>
      <div class="hero-actions">
        <a href="category.php?slug=womens-fashion"     class="button button-primary">Shop Fashion</a>
        <a href="category.php?slug=kitchen-appliances" class="button button-secondary">Explore Appliances</a>
      </div>
      <div class="trust-bar">
        <div class="trust-item">
          <strong>✓ Authentic Quality</strong>
          <span>Premium fabrics &amp; certified appliances</span>
        </div>
        <div class="trust-item">
          <strong>✓ Warranty Included</strong>
          <span>On all electronic products</span>
        </div>
        <div class="trust-item">
          <strong>✓ Nationwide Delivery</strong>
          <span>Fast &amp; secure shipping</span>
        </div>
      </div>
    </div>

    <div class="hero-image">
      <div class="hero-visual-grid">
        <div class="hero-card hero-card-tall">
          <span class="hero-badge">New Season</span>
          <div>
            <div class="hero-card-label">Fashion</div>
            <div class="hero-card-title">Tailored for<br>Modern Living</div>
          </div>
        </div>
        <div class="hero-card hero-card-short">
          <div class="hero-card-label">Kitchen</div>
          <div class="hero-card-title">Smart Appliances</div>
        </div>
        <div class="hero-card hero-card-short teal-card">
          <div class="hero-card-label">Home</div>
          <div class="hero-card-title">Everyday Essentials</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── DEPARTMENT PILLS ── -->
  <section class="category-pills">
    <p class="section-label">Shop by Department</p>
    <div class="pill-list">
      <?php foreach ($categories as $cat): ?>
        <a class="pill" href="category.php?slug=<?php echo urlencode($cat['slug']); ?>">
          <?php echo htmlspecialchars($cat['name']); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ── FEATURED PRODUCTS ── -->
  <section class="featured-products">
    <div class="section-header">
      <div>
        <h2>Featured Collections</h2>
        <p>Our most popular fashion pieces and top-rated home appliances.</p>
      </div>
      <a href="search.php?q=" class="link-more">Browse all &rarr;</a>
    </div>

    <div class="product-grid">
      <?php foreach (array_slice($products, 0, 6) as $product): ?>
        <article class="product-card">
          <div class="product-card-img-wrap">
            <img
              src="/public/images/<?php echo htmlspecialchars($product['image']); ?>"
              alt="<?php echo htmlspecialchars($product['name']); ?>"
              loading="lazy"
              onerror="this.parentElement.innerHTML='<div class=\'product-card-img-placeholder\'>🛍️</div>'"
            >
          </div>
          <div class="product-card-body">
            <span class="badge badge-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Collection'); ?></span>
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><?php echo htmlspecialchars($product['short_description']); ?></p>
            <div class="product-meta">
              <span class="price">Ksh <?php echo number_format($product['price'], 2); ?></span>
              <a class="button button-secondary" style="padding:0.5rem 1rem;font-size:0.82rem;" href="product.php?id=<?php echo $product['id']; ?>">View</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>