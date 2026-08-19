<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['product_id'])) {
        addToCart((int) $_POST['product_id']);
        setFlash('success', 'Item added to your shopping cart.');
        header('Location: cart.php'); exit;
    } elseif (!empty($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
        setFlash('info', 'Your shopping cart has been cleared.');
        header('Location: cart.php'); exit;
    }
}

$cartItems = getCartItems();
$subtotal  = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));
$flashes   = getFlashes();
$pageTitle = 'Shopping Cart';
include __DIR__ . '/includes/header.php';
?>
<main class="page-content">

  <!-- Page Banner -->
  <div class="page-banner">
    <div class="breadcrumb"><a href="/index.php">Home</a> › Shopping Cart</div>
    <h1>Your Cart</h1>
    <p>Review your selected items before checkout.</p>
  </div>

  <!-- Flash Messages -->
  <?php foreach ($flashes as $flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
      <span><?php echo htmlspecialchars($flash['message']); ?></span>
    </div>
  <?php endforeach; ?>

  <?php if (empty($cartItems)): ?>
    <div class="empty-box">
      <div class="empty-icon">🛒</div>
      <h3>Your cart is empty</h3>
      <p>You haven't added anything yet. Start shopping and add items to your cart.</p>
      <a href="/index.php" class="button button-primary">Explore Products</a>
    </div>

  <?php else: ?>
    <div class="cart-layout">

      <!-- Cart Items Table -->
      <div class="cart-table-card">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): ?>
              <tr>
                <td>
                  <div class="product-cell">
                    <div class="product-thumb-wrapper">
                      <img src="/public/images/<?php echo htmlspecialchars($item['image']); ?>" alt="" class="product-thumb" onerror="this.style.display='none'">
                    </div>
                    <div>
                      <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                      <span class="cell-subtext"><?php echo htmlspecialchars($item['category_name'] ?? 'Collection'); ?></span>
                    </div>
                  </div>
                </td>
                <td><span class="order-tag"><?php echo $item['quantity']; ?></span></td>
                <td>Ksh <?php echo number_format($item['price'], 2); ?></td>
                <td><strong class="price-text">Ksh <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="cart-table-footer">
          <a href="/index.php" class="link-gold">&larr; Continue Shopping</a>
          <form method="post">
            <input type="hidden" name="clear_cart" value="1">
            <button type="submit" class="button-action button-action-delete">🗑 Clear Cart</button>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="cart-summary-card">
        <h2>Order Summary</h2>

        <div class="summary-row">
          <span class="label">Items</span>
          <span class="value"><?php echo count($cartItems); ?> item<?php echo count($cartItems) !== 1 ? 's' : ''; ?></span>
        </div>
        <div class="summary-row">
          <span class="label">Delivery</span>
          <span class="value" style="color:#16a34a;font-weight:700;">Free</span>
        </div>
        <div class="summary-row summary-total" style="margin-top:0.5rem;padding-top:1rem;border-top:2px solid var(--border);">
          <span class="label">Total</span>
          <span class="value">Ksh <?php echo number_format($subtotal, 2); ?></span>
        </div>

        <div style="margin-top:1.5rem;">
          <button type="button" class="button button-primary button-block"
            onclick="alert('Checkout initiated! Connect your payment gateway (e.g. M-Pesa / Card) here.');">
            Proceed to Checkout &rarr;
          </button>
          <p style="font-size:0.78rem;color:var(--ink-muted);text-align:center;margin-top:0.75rem;">
            🔒 Secure &amp; Encrypted Checkout
          </p>
        </div>
      </div>

    </div>
  <?php endif; ?>

</main>
<?php include __DIR__ . '/includes/footer.php'; ?>