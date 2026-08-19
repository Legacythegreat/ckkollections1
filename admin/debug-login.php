<?php
require_once __DIR__ . '/../includes/functions.php';

echo "<h1>Admin Login Debug</h1>";

// Test 1: Database connection
echo "<h2>1. Database Connection:</h2>";
try {
    $pdo = getDbConnection();
    echo "<p style='color:green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 2: Check if admins table exists and has data
echo "<h2>2. Admins Table:</h2>";
try {
    $stmt = $pdo->query('SELECT id, email, is_master FROM admins');
    $admins = $stmt->fetchAll();
    if (empty($admins)) {
        echo "<p style='color:orange;'>⚠ No admin users found in database</p>";
    } else {
        echo "<p style='color:green;'>✓ Found " . count($admins) . " admin(s):</p>";
        echo "<ul>";
        foreach ($admins as $admin) {
            echo "<li>ID: {$admin['id']}, Email: " . htmlspecialchars($admin['email']) . ", Master: " . ($admin['is_master'] ? 'Yes' : 'No') . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>✗ Error querying admins table: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 3: Test login credentials
echo "<h2>3. Test Login Credentials:</h2>";
$testEmail = 'enochmwenda@gmail.com';
$testPassword = 'tarsh6304';

$admin = getAdminByEmail($testEmail);
if (!$admin) {
    echo "<p style='color:red;'>✗ Admin with email '" . htmlspecialchars($testEmail) . "' not found</p>";
} else {
    echo "<p style='color:green;'>✓ Admin found: " . htmlspecialchars($admin['email']) . "</p>";
    
    // Test password verification
    if (password_verify($testPassword, $admin['password_hash'])) {
        echo "<p style='color:green;'>✓ Password verification successful</p>";
    } else {
        echo "<p style='color:red;'>✗ Password verification failed. Hash in DB: " . htmlspecialchars(substr($admin['password_hash'], 0, 30)) . "...</p>";
    }
}

// Test 4: Full login simulation
echo "<h2>4. Full Login Simulation:</h2>";
$result = verifyAdminCredentials($testEmail, $testPassword);
if ($result) {
    echo "<p style='color:green;'>✓ Login verification successful</p>";
    echo "<p>Would set session:</p>";
    echo "<ul>";
    echo "<li>admin_id: " . htmlspecialchars($result['id']) . "</li>";
    echo "<li>admin_email: " . htmlspecialchars($result['email']) . "</li>";
    echo "<li>is_master: " . ($result['is_master'] ? 'true' : 'false') . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color:red;'>✗ Login verification failed</p>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Debug</title>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #1a1a1a;
            color: #f7f1e6;
            padding: 2rem;
        }
        h1 { color: #f4dfb1; }
        h2 { color: #ffd77d; margin-top: 2rem; }
        p { line-height: 1.6; }
        ul { margin: 1rem 0; }
        li { margin: 0.5rem 0; }
    </style>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Output above -->
    </div>
</body>
</html>
