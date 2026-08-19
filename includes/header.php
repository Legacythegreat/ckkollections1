<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : ''; ?>CK Kollections · Clothes &amp; Household Appliances</title>
  <meta name="description" content="CK Kollections — Premium men's &amp; women's fashion and top-rated household appliances. Shop online in Kenya.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/styles.css">
</head>
<?php
$currentUri  = $_SERVER['REQUEST_URI'] ?? '';
$isAdminPath = strpos($currentUri, '/admin') !== false;
$bodyClass   = $isAdminPath ? ' class="admin-body"' : '';
$currentPage = basename(parse_url($currentUri, PHP_URL_PATH) ?? '');
?>
<body<?php echo $bodyClass; ?>>
  <header class="site-header">
    <div class="header-inner">

      <!-- Brand -->
      <div class="site-branding">
        <a href="/index.php">
          <span class="brand-text">CK <span>Kollections</span></span>
          <?php if ($isAdminPath): ?>
            <span class="brand-badge">Admin</span>
          <?php endif; ?>
        </a>
      </div>

      <?php if ($isAdminPath): ?>
        <!-- Admin Navigation -->
        <?php if (!empty($_SESSION['admin_id'])): ?>
          <nav class="admin-nav" aria-label="Admin navigation">
            <a href="/admin/dashboard.php"   class="<?php echo ($currentPage === 'dashboard.php')  ? 'active' : ''; ?>">Dashboard</a>
            <a href="/admin/products.php"    class="<?php echo ($currentPage === 'products.php')   ? 'active' : ''; ?>">Products</a>
            <a href="/admin/categories.php"  class="<?php echo ($currentPage === 'categories.php') ? 'active' : ''; ?>">Categories</a>
            <?php if (!empty($_SESSION['is_master'])): ?>
              <a href="/admin/users.php"     class="<?php echo ($currentPage === 'users.php')      ? 'active' : ''; ?>">Admins</a>
            <?php endif; ?>
            <a href="/index.php" target="_blank" class="nav-store-link">Store ↗</a>
            <div class="nav-user-pill">
              <span class="user-email"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'Admin'); ?></span>
              <a href="/admin/logout.php" class="button-logout">Logout</a>
            </div>
          </nav>
        <?php endif; ?>

      <?php else: ?>
        <!-- Storefront Search -->
        <form class="site-search" action="/search.php" method="get" role="search">
          <label class="search-field">
            <svg class="icon icon-search" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 18a8 8 0 1 1 5.293-2.707l4.707 4.707-1.414 1.414-4.707-4.707A7.963 7.963 0 0 1 10 18zm0-14a6 6 0 1 0 0 12 6 6 0 0 0 0-12z"/></svg>
            <input type="search" name="q" placeholder="Search clothes, appliances..." aria-label="Search products" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
          </label>
          <button type="submit" class="button button-primary" style="border-radius:999px;padding:0.5rem 1.25rem;margin:0.25rem;" aria-label="Search">Search</button>
        </form>

        <!-- Mobile Toggle -->
        <button class="nav-toggle" id="nav-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Toggle navigation">
          <svg class="icon icon-menu" viewBox="0 0 24 24" aria-hidden="true" style="width:1.4rem;height:1.4rem;"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
        </button>

        <!-- Storefront Nav -->
        <nav id="site-nav" class="site-nav" aria-label="Main navigation">
          <a href="/index.php">Home</a>
          <a href="/about.php">About</a>
          <a href="/contact.php">Contact</a>
          <a href="/cart.php">
            🛒 Cart<?php
              $cartCount = array_sum($_SESSION['cart'] ?? []);
              if ($cartCount > 0) echo ' <span style="background:var(--accent);color:#fff;font-size:0.7rem;padding:0.1rem 0.45rem;border-radius:999px;margin-left:0.2rem;">' . $cartCount . '</span>';
            ?>
          </a>
          <a href="/admin/index.php" class="admin-login-link">Admin</a>
        </nav>

      <?php endif; ?>
    </div>
  </header>
