<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$errors = [];
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editProduct = null;
$showForm = isset($_GET['action']) && $_GET['action'] === 'new';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid. Please refresh the page.';
    } else {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $id = (int)($_POST['product_id'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $short_desc = trim($_POST['short_description'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $image_name = trim($_POST['image_filename'] ?? '');

            if ($name === '') {
                $errors[] = 'Product name is required.';
            }
            if ($category_id <= 0) {
                $errors[] = 'Please select a valid category.';
            }
            if ($price <= 0) {
                $errors[] = 'Price must be greater than 0.';
            }

            // Handle image upload if a file was selected
            $uploadError = null;
            if (!empty($_FILES['image_file']['name'])) {
                $uploadedFile = handleImageUpload($_FILES['image_file'], $uploadError);
                if ($uploadedFile) {
                    $image_name = $uploadedFile;
                } else {
                    $errors[] = $uploadError ?: 'Image upload failed.';
                }
            }

            if ($action === 'add' && $image_name === '') {
                // Default placeholder image filename
                $image_name = 'product-1.jpg';
            }

            if (empty($errors)) {
                if ($action === 'add') {
                    addProduct($category_id, $name, $short_desc, $desc, $price, $image_name);
                    setFlash('success', 'Product "' . htmlspecialchars($name) . '" created successfully!');
                } else {
                    updateProduct($id, $category_id, $name, $short_desc, $desc, $price, $image_name ?: null);
                    setFlash('success', 'Product "' . htmlspecialchars($name) . '" updated successfully!');
                }
                header('Location: products.php');
                exit;
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['product_id'] ?? 0);
            if ($id > 0) {
                deleteProduct($id);
                setFlash('success', 'Product deleted successfully.');
                header('Location: products.php');
                exit;
            }
        }
    }
}

// Fetch categories
$categories = getAllCategories();

// Fetch filter params
$filterCategory = $_GET['category'] ?? '';
$searchQuery = $_GET['q'] ?? '';

$products = getProducts($filterCategory ?: null, $searchQuery ?: null);

// If editing, find the product
if ($editId) {
    $editProduct = getProductById($editId);
    if ($editProduct) {
        $showForm = true;
    }
}

$flashes = getFlashes();
include __DIR__ . '/../includes/header.php';
?>
<main class="page-content admin-products-page">
  <div class="dashboard-header">
    <div>
      <div class="breadcrumb"><a href="dashboard.php" class="link-gold">&larr; Dashboard</a> &bull; Products</div>
      <h1 class="page-title">Manage Products</h1>
      <p class="page-subtitle">Add, edit, and organize clothing collections and household appliances.</p>
    </div>
    <div class="header-actions">
      <?php if (!$showForm): ?>
        <a href="products.php?action=new" class="button button-primary">+ Add New Product</a>
      <?php else: ?>
        <a href="products.php" class="button button-secondary">&larr; Back to Products List</a>
      <?php endif; ?>
    </div>
  </div>

  <?php foreach ($flashes as $flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
      <svg class="alert-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-4h2V7h-2v6z"/></svg>
      <span><?php echo htmlspecialchars($flash['message']); ?></span>
    </div>
  <?php endforeach; ?>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error">
      <svg class="alert-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
      <span><?php echo htmlspecialchars($err); ?></span>
    </div>
  <?php endforeach; ?>

  <?php if ($showForm): ?>
    <!-- Add / Edit Product Card -->
    <section class="admin-card">
      <div class="card-header">
        <h2><?php echo $editProduct ? 'Edit Product Details' : 'Add New Product to Catalog'; ?></h2>
        <p class="card-subtitle">Fill in the product specification, pricing, and upload high-resolution photos.</p>
      </div>

      <form method="post" enctype="multipart/form-data" class="admin-form-grid">
        <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <?php if ($editProduct): ?>
          <input type="hidden" name="product_id" value="<?php echo (int)$editProduct['id']; ?>">
        <?php endif; ?>

        <div class="form-col-left">
          <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" placeholder="e.g. Italian Wool Suit, Smart Air Fryer 6L" value="<?php echo htmlspecialchars($_POST['name'] ?? ($editProduct['name'] ?? '')); ?>" required>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label for="category_id">Category Department *</label>
              <select id="category_id" name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>" <?php 
                    $selectedCat = $_POST['category_id'] ?? ($editProduct['category_id'] ?? '');
                    echo ($selectedCat == $cat['id']) ? 'selected' : ''; 
                  ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="price">Price in Ksh *</label>
              <input type="number" step="0.01" min="1" id="price" name="price" placeholder="e.g. 14500" value="<?php echo htmlspecialchars($_POST['price'] ?? ($editProduct['price'] ?? '')); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="short_description">Short Summary (Featured on Cards)</label>
            <input type="text" id="short_description" name="short_description" placeholder="Brief 1-line highlight" value="<?php echo htmlspecialchars($_POST['short_description'] ?? ($editProduct['short_description'] ?? '')); ?>">
          </div>

          <div class="form-group">
            <label for="description">Full Description &amp; Specifications</label>
            <textarea id="description" name="description" rows="5" placeholder="Detailed product specifications, materials, features, dimensions..."><?php echo htmlspecialchars($_POST['description'] ?? ($editProduct['description'] ?? '')); ?></textarea>
          </div>
        </div>

        <div class="form-col-right">
          <div class="form-group">
            <label>Product Photography</label>
            <?php if (!empty($editProduct['image'])): ?>
              <div class="current-image-preview">
                <img src="/public/images/<?php echo htmlspecialchars($editProduct['image']); ?>" alt="Current Image" onerror="this.style.display='none'">
                <span class="preview-filename">Current: <?php echo htmlspecialchars($editProduct['image']); ?></span>
              </div>
            <?php endif; ?>

            <div class="file-upload-box">
              <svg class="upload-icon" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/></svg>
              <span class="upload-title">Upload New Photo</span>
              <span class="upload-subtext">JPG, PNG, WEBP up to 5MB</span>
              <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="filename-fallback">
              <label for="image_filename">Or Image Filename in <code>public/images/</code></label>
              <input type="text" id="image_filename" name="image_filename" placeholder="e.g. product-1.jpg" value="<?php echo htmlspecialchars($_POST['image_filename'] ?? ($editProduct['image'] ?? '')); ?>">
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="button button-primary button-block">
              <?php echo $editProduct ? 'Save Product Changes' : 'Publish Product to Store'; ?>
            </button>
            <a href="products.php" class="button button-secondary button-block">Cancel</a>
          </div>
        </div>
      </form>
    </section>

  <?php else: ?>
    <!-- Products Catalog Table -->
    <section class="admin-card">
      <div class="table-filter-bar">
        <form method="get" class="filter-form">
          <div class="filter-group">
            <input type="text" name="q" placeholder="Search clothes or appliances..." value="<?php echo htmlspecialchars($searchQuery); ?>">
          </div>
          <div class="filter-group">
            <select name="category" onchange="this.form.submit()">
              <option value="">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['slug']); ?>" <?php echo ($filterCategory === $cat['slug']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="button button-secondary">Filter</button>
          <?php if ($searchQuery !== '' || $filterCategory !== ''): ?>
            <a href="products.php" class="link-clear">Reset Filters</a>
          <?php endif; ?>
        </form>
        <span class="results-count"><?php echo count($products); ?> Product(s) Found</span>
      </div>

      <?php if (empty($products)): ?>
        <div class="empty-box">
          <p>No products match your filter criteria.</p>
          <a href="products.php?action=new" class="button button-primary">+ Add New Product</a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Department</th>
                <th>Price (Ksh)</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p): ?>
                <tr>
                  <td><span class="cell-id">#<?php echo $p['id']; ?></span></td>
                  <td>
                    <div class="product-cell">
                      <div class="product-thumb-wrapper">
                        <img src="/public/images/<?php echo htmlspecialchars($p['image']); ?>" alt="" class="product-thumb" onerror="this.style.display='none'">
                      </div>
                      <div>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                        <span class="cell-subtext"><?php echo htmlspecialchars(mb_strimwidth($p['short_description'], 0, 50, '...')); ?></span>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge badge-category"><?php echo htmlspecialchars($p['category_name']); ?></span></td>
                  <td><strong class="price-text">Ksh <?php echo number_format($p['price'], 2); ?></strong></td>
                  <td>
                    <div class="actions-cell">
                      <a href="products.php?edit=<?php echo $p['id']; ?>" class="button-action">Edit</a>
                      <a href="/product.php?id=<?php echo $p['id']; ?>" target="_blank" class="button-action button-action-view">View &nearr;</a>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete \'<?php echo addslashes($p['name']); ?>\'?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit" class="button-action button-action-delete">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
