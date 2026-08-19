<?php
require_once __DIR__ . '/../includes/functions.php';
requireMasterAdmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token invalid. Please refresh the page.';
    } else {
        $action = $_POST['action'];

        if ($action === 'add') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please provide a valid email address.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long.';
            } elseif ($password !== $confirm) {
                $errors[] = 'Passwords do not match.';
            } else {
                $existing = getAdminByEmail($email);
                if ($existing) {
                    $errors[] = 'An admin account with this email already exists.';
                } else {
                    createAdmin($email, $password, 0);
                    setFlash('success', 'Admin user "' . htmlspecialchars($email) . '" created successfully.');
                    header('Location: users.php');
                    exit;
                }
            }
        } elseif ($action === 'change_password') {
            $adminId = (int)($_POST['admin_id'] ?? 0);
            $newPassword = $_POST['new_password'] ?? '';
            $confirmNew = $_POST['confirm_new_password'] ?? '';

            if (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters long.';
            } elseif ($newPassword !== $confirmNew) {
                $errors[] = 'New passwords do not match.';
            } else {
                updateAdminPassword($adminId, $newPassword);
                setFlash('success', 'Password updated successfully for admin ID #' . $adminId . '.');
                header('Location: users.php');
                exit;
            }
        } elseif ($action === 'delete') {
            $adminId = (int)($_POST['admin_id'] ?? 0);
            if ($adminId === (int)$_SESSION['admin_id']) {
                $errors[] = 'You cannot delete your own active master account.';
            } elseif ($adminId > 0) {
                deleteAdminUser($adminId);
                setFlash('success', 'Admin user account removed.');
                header('Location: users.php');
                exit;
            }
        }
    }
}

$admins = getAdmins();
$flashes = getFlashes();
include __DIR__ . '/../includes/header.php';
?>
<main class="page-content admin-users-page">
  <div class="dashboard-header">
    <div>
      <div class="breadcrumb"><a href="dashboard.php" class="link-gold">&larr; Dashboard</a> &bull; Admin Users</div>
      <h1 class="page-title">Admin Staff &amp; Access Control</h1>
      <p class="page-subtitle">Manage administrative privileges, credentials, and store team members.</p>
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

  <div class="users-layout">
    <!-- Admin List Table -->
    <section class="admin-card users-table-card">
      <div class="card-header">
        <h2>Authorized Administrators</h2>
        <span class="badge badge-gold"><?php echo count($admins); ?> Users</span>
      </div>

      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Admin Email</th>
              <th>Access Role</th>
              <th>Created Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $a): ?>
              <tr>
                <td><span class="cell-id">#<?php echo $a['id']; ?></span></td>
                <td>
                  <strong><?php echo htmlspecialchars($a['email']); ?></strong>
                  <?php if ($a['id'] === (int)$_SESSION['admin_id']): ?>
                    <span class="badge badge-current">You</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($a['is_master']): ?>
                    <span class="badge badge-gold">Master Admin</span>
                  <?php else: ?>
                    <span class="badge">Staff Admin</span>
                  <?php endif; ?>
                </td>
                <td><span class="cell-subtext"><?php echo date('M d, Y', strtotime($a['created_at'])); ?></span></td>
                <td>
                  <div class="actions-cell">
                    <?php if (!$a['is_master'] && $a['id'] !== (int)$_SESSION['admin_id']): ?>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Revoke admin access for \'<?php echo addslashes($a['email']); ?>\'?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="admin_id" value="<?php echo $a['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit" class="button-action button-action-delete">Remove</button>
                      </form>
                    <?php else: ?>
                      <span class="cell-subtext">Protected</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Create Admin User Form -->
    <section class="admin-card user-form-card">
      <div class="card-header">
        <h2>Add Staff Admin</h2>
        <p class="card-subtitle">Create a new login for store employees or inventory managers.</p>
      </div>

      <form method="post" class="admin-form">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
          <label for="admin_email">Staff Email Address *</label>
          <input type="email" id="admin_email" name="email" placeholder="staff@ckkollections.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
          <label for="admin_password">Password (min 6 characters) *</label>
          <input type="password" id="admin_password" name="password" placeholder="••••••••" required>
        </div>

        <div class="form-group">
          <label for="admin_confirm">Confirm Password *</label>
          <input type="password" id="admin_confirm" name="confirm_password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="button button-primary button-block">
          Create Admin Account
        </button>
      </form>
    </section>
  </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
