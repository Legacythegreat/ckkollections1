<?php
require_once __DIR__ . '/includes/functions.php';
$slug = $_GET['slug'] ?? null;
if (!$slug) { header('Location: index.php'); exit; }

$categories = getCategories();
$products   = getProducts($slug);
$category   = null;
foreach ($categories as $cat) {
    if ($cat['slug'] === $slug) { $category = $cat; break; }
}
if (!$category) { header('Location: index.php'); exit; }

$pageTitle = htmlspecialchars($category['name']);
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <!-- Page Banner -->
  <div class="page-banner">
    <div class="breadcrumb">
      <a href="/index.php">Home</a> › <span><?php echo htmlspecialchars($category['name']); ?></span>
    </div>
    <h1><?php echo htmlspecialchars($category['name']); ?></h1>
    <p>Browse our premium selection of <?php echo strtolower(htmlspecialchars($category['name'])); ?> — quality guaranteed.</p>
  </div>

  <!-- Category Tabs -->
  <section class="category-pills" style="margin-bottom:2rem;">
    <div class="pill-list">
      <?php foreach ($categories as $cat): ?>
        <a class="pill <?php echo ($cat['slug'] === $slug) ? 'active-pill' : ''; ?>"
           href="category.php?slug=<?php echo urlencode($cat['slug']); ?>">
          <?php echo htmlspecialchars($cat['name']); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Products -->
  <section>
    <?php if (empty($products)): ?>
      <div class="empty-box">
        <div class="empty-icon">🛍️</div>
        <h3>No products yet</h3>
        <p>This department doesn't have any products listed yet. Check back soon!</p>
        <a href="/index.php" class="button button-primary">Back to Homepage</a>
      </div>
    <?php else: ?>
      <div class="section-header">
        <div>
          <h2><?php echo htmlspecialchars($category['name']); ?></h2>
          <p><?php echo count($products); ?> item<?php echo count($products) !== 1 ? 's' : ''; ?> available</p>
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
              <span class="badge badge-category"><?php echo htmlspecialchars($category['name']); ?></span>
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
  </section>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>