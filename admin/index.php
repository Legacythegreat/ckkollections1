<?php
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$masterCount = countMasterAdmins();
$errors = [];
$successMessage = '';

if (isset($_GET['logged_out'])) {
    $successMessage = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid or expired. Please refresh and try again.';
    } elseif ($masterCount === 0 && !empty($_POST['create_master'])) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        } else {
            $adminId = createAdmin($email, $password, 1);
            $_SESSION['admin_id'] = $adminId;
            $_SESSION['admin_email'] = $email;
            $_SESSION['is_master'] = true;
            setFlash('success', 'Master Admin account created successfully! Welcome to CK Kollections.');
            header('Location: dashboard.php');
            exit;
        }
    } elseif (!empty($_POST['login'])) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $errors[] = 'Please enter both your email and password.';
        } else {
            $admin = verifyAdminCredentials($email, $password);
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['is_master'] = (bool)$admin['is_master'];
                setFlash('success', 'Welcome back, ' . htmlspecialchars($admin['email']) . '!');
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<main class="page-content">
  <section class="page-section">
    <div class="admin-auth-card">
      <div class="auth-header">
        <span class="auth-badge">CK Kollections</span>
        <h1><?php echo ($masterCount === 0) ? 'Master Admin Setup' : 'Admin Portal'; ?></h1>
        <p class="auth-subtitle">
          <?php echo ($masterCount === 0) 
            ? 'Initialize your master administrator account to manage clothes, household appliances, and staff.' 
            : 'Sign in to access catalog management, order inventory, and store settings.'; ?>
        </p>
      </div>

      <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
          <svg class="alert-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          <span><?php echo htmlspecialchars($successMessage); ?></span>
        </div>
      <?php endif; ?>

      <?php foreach ($errors as $error): ?>
        <div class="alert alert-error">
          <svg class="alert-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
          <span><?php echo htmlspecialchars($error); ?></span>
        </div>
      <?php endforeach; ?>

      <?php if ($masterCount === 0): ?>
        <form method="post" class="admin-form" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
          <div class="form-group">
            <label for="email">Master Admin Email</label>
            <input type="email" id="email" name="email" placeholder="admin@ckkollections.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autofocus>
          </div>
          <div class="form-group">
            <label for="password">Password (minimum 6 characters)</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
          </div>
          <button type="submit" name="create_master" value="1" class="button button-primary button-block">
            Create Master Account &amp; Log In
          </button>
        </form>
      <?php else: ?>
        <form method="post" class="admin-form">
          <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="admin@ckkollections.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autofocus>
          </div>
          <div class="form-group">
            <div class="label-row">
              <label for="password">Password</label>
            </div>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
          </div>
          <button type="submit" name="login" value="1" class="button button-primary button-block">
            Sign In to Dashboard
          </button>
        </form>
      <?php endif; ?>

      <div class="auth-footer">
        <a href="/index.php" class="link-store">&larr; Return to Storefront</a>
      </div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
