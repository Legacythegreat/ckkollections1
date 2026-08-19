<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$errors = [];
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editCategory = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid. Please refresh the page.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            $id = (int)($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $order = (int)($_POST['display_order'] ?? 0);

            if ($name === '') {
                $errors[] = 'Category name is required.';
            }

            if (empty($errors)) {
                if ($action === 'add') {
                    addCategory($name, $slug, $order);
                    setFlash('success', 'Category "' . htmlspecialchars($name) . '" created successfully.');
                } else {
                    updateCategory($id, $name, $slug, $order);
                    setFlash('success', 'Category "' . htmlspecialchars($name) . '" updated successfully.');
                }
                header('Location: categories.php');
                exit;
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['category_id'] ?? 0);
            if ($id > 0) {
                deleteCategory($id);
                setFlash('success', 'Category and its related products deleted successfully.');
                header('Location: categories.php');
                exit;
            }
        }
    }
}

$categories = getAllCategories();

if ($editId) {
    foreach ($categories as $c) {
        if ($c['id'] === $editId) {
            $editCategory = $c;
            break;
        }
    }
}

$flashes = getFlashes();
include __DIR__ . '/../includes/header.php';
?>
<main class="page-content admin-categories-page">
  <div class="dashboard-header">
    <div>
      <div class="breadcrumb"><a href="dashboard.php" class="link-gold">&larr; Dashboard</a> &bull; Categories</div>
      <h1 class="page-title">Product Categories</h1>
      <p class="page-subtitle">Organize clothes, footwear, kitchen electronics, and smart home appliances into store sections.</p>
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

  <div class="categories-layout">
    <!-- Category List Table -->
    <section class="admin-card categories-table-card">
      <div class="card-header">
        <h2>Active Categories</h2>
        <span class="badge badge-gold"><?php echo count($categories); ?> Categories</span>
      </div>

      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Category Name</th>
              <th>Slug / URL</th>
              <th>Products</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $c): ?>
              <tr>
                <td><span class="order-tag"><?php echo $c['display_order']; ?></span></td>
                <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                <td><code>category.php?slug=<?php echo htmlspecialchars($c['slug']); ?></code></td>
                <td><span class="badge badge-category"><?php echo (int)($c['product_count'] ?? 0); ?> items</span></td>
                <td>
                  <div class="actions-cell">
                    <a href="categories.php?edit=<?php echo $c['id']; ?>" class="button-action">Edit</a>
                    <a href="/category.php?slug=<?php echo urlencode($c['slug']); ?>" target="_blank" class="button-action button-action-view">View &nearr;</a>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Warning: Deleting category \'<?php echo addslashes($c['name']); ?>\' will also remove its associated products. Proceed?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="category_id" value="<?php echo $c['id']; ?>">
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
    </section>

    <!-- Add / Edit Category Form -->
    <section class="admin-card category-form-card">
      <div class="card-header">
        <h2><?php echo $editCategory ? 'Edit Category' : 'Create New Category'; ?></h2>
        <p class="card-subtitle"><?php echo $editCategory ? 'Update name, order, or URL slug.' : 'Add a new clothing line or appliance category.'; ?></p>
      </div>

      <form method="post" class="admin-form">
        <input type="hidden" name="action" value="<?php echo $editCategory ? 'edit' : 'add'; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <?php if ($editCategory): ?>
          <input type="hidden" name="category_id" value="<?php echo $editCategory['id']; ?>">
        <?php endif; ?>

        <div class="form-group">
          <label for="cat_name">Category Name *</label>
          <input type="text" id="cat_name" name="name" placeholder="e.g. Women's Fashion, Kitchen Appliances" value="<?php echo htmlspecialchars($editCategory['name'] ?? ''); ?>" required autofocus>
        </div>

        <div class="form-group">
          <label for="cat_slug">URL Slug (Auto-generated)</label>
          <input type="text" id="cat_slug" name="slug" placeholder="e.g. womens-fashion" value="<?php echo htmlspecialchars($editCategory['slug'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label for="cat_order">Display Sort Order</label>
          <input type="number" id="cat_order" name="display_order" value="<?php echo htmlspecialchars((string)($editCategory['display_order'] ?? 0)); ?>">
          <small class="help-text">Lower numbers appear first in storefront navigation menu.</small>
        </div>

        <div class="form-actions-inline">
          <button type="submit" class="button button-primary">
            <?php echo $editCategory ? 'Update Category' : 'Create Category'; ?>
          </button>
          <?php if ($editCategory): ?>
            <a href="categories.php" class="button button-secondary">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </section>
  </div>
</main>

<script>
// Auto slug generation script
document.addEventListener('DOMContentLoaded', function() {
  const nameInput = document.getElementById('cat_name');
  const slugInput = document.getElementById('cat_slug');
  if (nameInput && slugInput && !slugInput.value) {
    nameInput.addEventListener('input', function() {
      slugInput.value = this.value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    });
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
