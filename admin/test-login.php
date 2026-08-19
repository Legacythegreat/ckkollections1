<?php
require_once __DIR__ . '/../includes/functions.php';

echo "<h1>Test Login Form</h1>";

// Check if session is active
echo "<h2>Session Status:</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Data: " . print_r($_SESSION, true) . "</p>";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Form Submitted:</h2>";
    echo "<p>Email: " . htmlspecialchars($_POST['email'] ?? '') . "</p>";
    echo "<p>Password: " . (isset($_POST['password']) ? '(provided)' : '(not provided)') . "</p>";
    echo "<p>CSRF Token: " . (isset($_POST['csrf_token']) ? 'Present' : 'Missing') . "</p>";
    
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        echo "<p style='color:red;'>✗ CSRF token invalid</p>";
    } else {
        echo "<p style='color:green;'>✓ CSRF token valid</p>";
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $admin = verifyAdminCredentials($email, $password);
        
        if ($admin) {
            echo "<p style='color:green;'>✓ Credentials verified</p>";
            echo "<p>Setting session variables...</p>";
            
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['is_master'] = (bool)$admin['is_master'];
            
            echo "<p>Session updated:</p>";
            echo "<ul>";
            echo "<li>admin_id: " . htmlspecialchars($_SESSION['admin_id']) . "</li>";
            echo "<li>admin_email: " . htmlspecialchars($_SESSION['admin_email']) . "</li>";
            echo "<li>is_master: " . ($_SESSION['is_master'] ? 'true' : 'false') . "</li>";
            echo "</ul>";
            
            echo "<p style='color:green;'>✓ Login successful! Redirecting...</p>";
            echo "<p><a href='dashboard.php'>Click here if not redirected</a></p>";
            
            // Redirect
            header('Location: dashboard.php', true, 302);
            exit;
        } else {
            echo "<p style='color:red;'>✗ Credentials invalid</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Login</title>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #1a1a1a;
            color: #f7f1e6;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        h1 { color: #f4dfb1; }
        h2 { color: #ffd77d; margin-top: 2rem; }
        form {
            background: rgba(255,255,255,0.02);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid rgba(255,223,148,0.1);
            margin-top: 2rem;
        }
        label {
            display: block;
            margin: 1rem 0 0.5rem;
            color: #e7d9b6;
        }
        input {
            width: 100%;
            padding: 0.6rem;
            border-radius: 8px;
            border: 1px solid rgba(255,223,148,0.1);
            background: rgba(255,255,255,0.02);
            color: #f7f1e6;
            box-sizing: border-box;
        }
        button {
            background: linear-gradient(135deg, #d7b76a, #f4d497);
            color: #08110e;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.5rem;
        }
        button:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
        <h2>Login Form</h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <label>Email:
                <input type="email" name="email" value="enochmwenda@gmail.com" required>
            </label>
            <label>Password:
                <input type="password" name="password" value="tarsh6304" required>
            </label>
            <button type="submit">Login</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
