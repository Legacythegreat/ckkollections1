<?php
require_once __DIR__ . '/includes/functions.php';
$q        = trim($_GET['q'] ?? '');
$products = $q !== '' ? getProducts(null, $q) : [];
$pageTitle = 'Search Results';
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <!-- Search Banner -->
  <div class="page-banner">
    <h1>Search Products</h1>
    <p>Find exactly what you're looking for across all departments.</p>
  </div>

  <!-- Search Bar -->
  <form action="/search.php" method="get" style="margin-bottom:2rem;">
    <div style="display:flex;gap:0.75rem;max-width:600px;">
      <input
        type="search"
        name="q"
        class="form-control"
        value="<?php echo htmlspecialchars($q); ?>"
        placeholder="Search clothes, appliances, brands..."
        style="flex:1;"
      >
      <button type="submit" class="button button-primary" style="flex-shrink:0;">Search</button>
    </div>
  </form>

  <?php if ($q === ''): ?>
    <div class="empty-box">
      <div class="empty-icon">🔍</div>
      <h3>Start your search</h3>
      <p>Type a product name, brand, or category above to find what you're looking for.</p>
    </div>

  <?php elseif (empty($products)): ?>
    <div class="empty-box">
      <div class="empty-icon">😕</div>
      <h3>No results for "<?php echo htmlspecialchars($q); ?>"</h3>
      <p>Try a different keyword, or browse our departments below.</p>
      <a href="/index.php" class="button button-primary" style="margin-top:1rem;">Browse All Products</a>
    </div>

  <?php else: ?>
    <div class="section-header">
      <div>
        <h2><?php echo count($products); ?> result<?php echo count($products) !== 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($q); ?>"</h2>
      </div>
    </div>

    <div class="product-grid">
      <?php foreach ($products as $product): ?>
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
  <?php endif; ?>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>