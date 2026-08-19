<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$stats = getDashboardStats();
$flashes = getFlashes();
$config = getConfig();

include __DIR__ . '/../includes/header.php';
?>
<main class="page-content admin-dashboard-page">
  <div class="dashboard-header">
    <div>
      <div class="breadcrumb">Admin &bull; Overview</div>
      <h1 class="page-title">Store Operations Dashboard</h1>
      <p class="page-subtitle">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong> &mdash; <?php echo (!empty($_SESSION['is_master'])) ? '<span class="badge badge-gold">Master Admin</span>' : '<span class="badge">Staff Admin</span>'; ?></p>
    </div>
    <div class="header-actions">
      <a href="products.php?action=new" class="button button-primary">+ Add New Product</a>
      <a href="/index.php" target="_blank" class="button button-secondary">View Storefront &nearr;</a>
    </div>
  </div>

  <?php foreach ($flashes as $flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
      <svg class="alert-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-4h2V7h-2v6z"/></svg>
      <span><?php echo htmlspecialchars($flash['message']); ?></span>
    </div>
  <?php endforeach; ?>

  <!-- Analytics Metrics -->
  <section class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon-wrapper icon-gold">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-label">Total Products</span>
        <span class="stat-value"><?php echo number_format($stats['total_products']); ?></span>
        <span class="stat-subtext">Clothes &amp; Appliances</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-wrapper icon-champagne">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-label">Categories</span>
        <span class="stat-value"><?php echo number_format($stats['total_categories']); ?></span>
        <span class="stat-subtext">Active departments</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-wrapper icon-bronze">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-label">Admin Users</span>
        <span class="stat-value"><?php echo number_format($stats['total_admins']); ?></span>
        <span class="stat-subtext">Authorized accounts</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-wrapper icon-gold">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-label">Catalog Value</span>
        <span class="stat-value">Ksh <?php echo number_format($stats['inventory_value'], 2); ?></span>
        <span class="stat-subtext">Total active inventory</span>
      </div>
    </div>
  </section>

  <!-- Quick Navigation Grid -->
  <section class="admin-shortcuts">
    <h2>Management Hub</h2>
    <div class="shortcut-grid">
      <a href="products.php" class="shortcut-card">
        <div class="shortcut-icon"><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></div>
        <div class="shortcut-text">
          <h3>Manage Products</h3>
          <p>Add clothes, appliances, update prices, and upload product photos.</p>
        </div>
        <span class="shortcut-arrow">&rarr;</span>
      </a>

      <a href="categories.php" class="shortcut-card">
        <div class="shortcut-icon"><svg viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg></div>
        <div class="shortcut-text">
          <h3>Manage Categories</h3>
          <p>Organize collections into Men, Women, Kitchen &amp; Home Appliances.</p>
        </div>
        <span class="shortcut-arrow">&rarr;</span>
      </a>

      <a href="users.php" class="shortcut-card">
        <div class="shortcut-icon"><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 3s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
        <div class="shortcut-text">
          <h3>Admin Staff</h3>
          <p>Manage system administrators, master privileges, and credentials.</p>
        </div>
        <span class="shortcut-arrow">&rarr;</span>
      </a>
    </div>
  </section>

  <!-- Recent Products & System Diagnostics -->
  <div class="dashboard-columns">
    <section class="dashboard-panel panel-large">
      <div class="panel-header">
        <h2>Latest Catalog Items</h2>
        <a href="products.php" class="link-gold">View all &rarr;</a>
      </div>
      <?php if (empty($stats['recent_products'])): ?>
        <p class="empty-state">No products registered yet. <a href="products.php?action=new">Add your first product &rarr;</a></p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($stats['recent_products'] as $item): ?>
                <tr>
                  <td>
                    <div class="product-cell">
                      <div class="product-thumb-wrapper">
                        <img src="/public/images/<?php echo htmlspecialchars($item['image']); ?>" alt="" class="product-thumb" onerror="this.src='/public/styles.css'; this.className='product-thumb thumb-fallback';">
                      </div>
                      <div>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        <span class="cell-subtext"><?php echo htmlspecialchars(mb_strimwidth($item['short_description'], 0, 45, '...')); ?></span>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge badge-category"><?php echo htmlspecialchars($item['category_name']); ?></span></td>
                  <td><strong class="price-text">Ksh <?php echo number_format($item['price'], 2); ?></strong></td>
                  <td>
                    <a href="products.php?edit=<?php echo $item['id']; ?>" class="button-action">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <section class="dashboard-panel panel-small">
      <div class="panel-header">
        <h2>System Status</h2>
      </div>
      <div class="system-status-list">
        <div class="status-row">
          <span class="status-label">Database Connection</span>
          <span class="status-badge status-online">Connected</span>
        </div>
        <div class="status-row">
          <span class="status-label">Database Name</span>
          <span class="status-val"><?php echo htmlspecialchars($config['MYSQL_DATABASE'] ?? 'alcy_42591217_ckkollection'); ?></span>
        </div>
        <div class="status-row">
          <span class="status-label">Active Host</span>
          <span class="status-val"><?php echo htmlspecialchars($config['MYSQL_HOST'] ?? '127.0.0.1'); ?></span>
        </div>
        <div class="status-row">
          <span class="status-label">PHP Version</span>
          <span class="status-val"><?php echo phpversion(); ?></span>
        </div>
        <div class="status-row">
          <span class="status-label">Product Images Storage</span>
          <span class="status-val">public/images/</span>
        </div>
        <div class="status-row">
          <span class="status-label">Session ID</span>
          <span class="status-val"><?php echo substr(session_id(), 0, 8); ?>...</span>
        </div>
      </div>
    </section>
  </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
