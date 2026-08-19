<?php
require_once __DIR__ . '/includes/functions.php';
$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$productId || $productId <= 0) { header('Location: index.php'); exit; }
$product = getProductById($productId);
if (!$product) { header('Location: index.php'); exit; }

$pageTitle = htmlspecialchars($product['name']);
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <div class="breadcrumb" style="margin-bottom:2rem;">
    <a href="/index.php">Home</a> ›
    <a href="/category.php?slug=<?php echo urlencode($product['category_slug']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a> ›
    <span><?php echo htmlspecialchars($product['name']); ?></span>
  </div>

  <section class="product-detail-grid">

    <!-- Image -->
    <div class="product-detail-image">
      <img
        src="/public/images/<?php echo htmlspecialchars($product['image']); ?>"
        alt="<?php echo htmlspecialchars($product['name']); ?>"
        onerror="this.parentElement.style.display='flex';this.parentElement.style.alignItems='center';this.parentElement.style.justifyContent='center';this.parentElement.innerHTML='<span style=\'font-size:5rem;opacity:0.2\'>🛍️</span>';"
      >
    </div>

    <!-- Info -->
    <div class="product-detail-info">
      <span class="badge badge-category"><?php echo htmlspecialchars($product['category_name']); ?></span>

      <h1><?php echo htmlspecialchars($product['name']); ?></h1>

      <div class="product-price-block">
        <div style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-muted);margin-bottom:0.4rem;">Price</div>
        <span class="price">Ksh <?php echo number_format($product['price'], 2); ?></span>
      </div>

      <div class="product-desc-block">
        <h4>Product Description</h4>
        <p><?php echo nl2br(htmlspecialchars($product['description'] ?: $product['short_description'])); ?></p>
      </div>

      <form class="product-add-form" action="/cart.php" method="post">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <button type="submit" class="button button-primary" style="flex:1;justify-content:center;padding:0.9rem 2rem;">
          🛒 Add to Cart
        </button>
        <a href="/category.php?slug=<?php echo urlencode($product['category_slug']); ?>" class="button button-secondary">
          More in <?php echo htmlspecialchars($product['category_name']); ?>
        </a>
      </form>

      <div class="product-guarantees">
        <div class="guarantee-item">
          <span class="guarantee-icon">✓</span>
          100% Genuine &amp; Quality Inspected
        </div>
        <div class="guarantee-item">
          <span class="guarantee-icon">✓</span>
          Official manufacturer warranty on electronics
        </div>
        <div class="guarantee-item">
          <span class="guarantee-icon">✓</span>
          Fast &amp; secure delivery nationwide
        </div>
        <div class="guarantee-item">
          <span class="guarantee-icon">✓</span>
          Easy returns within 7 days
        </div>
      </div>
    </div>

  </section>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>