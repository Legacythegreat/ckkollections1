<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get resilient PDO database connection.
 * Automatically connects to configured MySQL database.
 * If remote connection fails locally (common on localhost), seamlessly falls back to local MariaDB/MySQL.
 * Automatically ensures all required tables exist.
 */
function getDbConnection() {
    static $pdo;
    if ($pdo !== null) {
        return $pdo;
    }

    $config = getConfig();
    $host = $config['MYSQL_HOST'] ?? '127.0.0.1';
    $db = $config['MYSQL_DATABASE'] ?? 'alcy_42591217_ckkollection';
    $user = $config['MYSQL_USER'] ?? 'root';
    $pass = $config['MYSQL_PASSWORD'] ?? '';

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $db);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 3,
        ]);
    } catch (PDOException $e) {
        // If connecting to remote host fails on localhost/development, fallback to local MySQL
        if ($host !== '127.0.0.1' && $host !== 'localhost') {
            try {
                $localDsn = sprintf('mysql:host=127.0.0.1;dbname=%s;charset=utf8mb4', $db);
                $pdo = new PDO($localDsn, 'root', '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $fallbackError) {
                try {
                    $serverPdo = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', 'root', '', [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]);
                    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $pdo = new PDO($localDsn, 'root', '', [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                } catch (PDOException $createError) {
                    renderDbErrorScreen($e->getMessage(), $host);
                    exit;
                }
            }
        } else {
            try {
                $serverPdo = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $finalErr) {
                renderDbErrorScreen($e->getMessage(), $host);
                exit;
            }
        }
    }

    ensureSchemaExists($pdo);
    return $pdo;
}

/**
 * Display a user-friendly database connection notice screen.
 */
function renderDbErrorScreen($errorMsg, $attemptedHost) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Database Notice - CK Kollections</title>
      <style>
        body { font-family: system-ui, sans-serif; background: #0d0a08; color: #f7f1e6; padding: 3rem 1rem; line-height: 1.6; }
        .card { max-width: 680px; margin: 0 auto; background: rgba(22,18,14,0.9); border: 1px solid rgba(255,223,148,0.2); border-radius: 16px; padding: 2.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.6); }
        h1 { color: #f4dfb1; margin-top: 0; }
        .error-box { background: rgba(255, 92, 92, 0.12); border-left: 4px solid #ff5c5c; padding: 1rem; border-radius: 6px; margin: 1.5rem 0; font-family: monospace; font-size: 0.9rem; color: #ff9999; word-break: break-word; }
        .tip { background: rgba(255,223,148,0.08); border-left: 4px solid #f4dfb1; padding: 1rem; border-radius: 6px; margin: 1.5rem 0; }
        code { background: rgba(255,255,255,0.1); padding: 0.2rem 0.4rem; border-radius: 4px; color: #ffd77d; }
        .btn { display: inline-block; background: linear-gradient(135deg, #d7b76a, #f4d497); color: #08110e; padding: 0.8rem 1.6rem; border-radius: 999px; font-weight: 700; text-decoration: none; margin-top: 1rem; }
      </style>
    </head>
    <body>
      <div class="card">
        <h1>Database Connection Notice</h1>
        <p>Could not connect to MySQL server at <code>' . htmlspecialchars($attemptedHost) . '</code>.</p>
        <div class="error-box">' . htmlspecialchars($errorMsg) . '</div>
        <div class="tip">
          <strong>Troubleshooting:</strong><br>
          &bull; When running on your local computer, make sure MySQL is started in XAMPP.<br>
          &bull; When uploaded to AlcHosting, the server will connect to <code>sql201.alchosting.xyz</code> automatically.
        </div>
        <a href="index.php" class="btn">Retry Connection</a>
      </div>
    </body>
    </html>';
}

/**
 * Automatically create tables and seed default clothing & appliances data if missing.
 */
function ensureSchemaExists(PDO $pdo) {
    static $checked = false;
    if ($checked) return;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            display_order INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            short_description TEXT NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            is_master TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Seed default categories if table is empty
    $countCat = (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($countCat === 0) {
        $pdo->exec("
            INSERT INTO categories (id, name, slug, display_order) VALUES
            (1, 'Women\'s Fashion', 'womens-fashion', 1),
            (2, 'Men\'s Collection', 'mens-collection', 2),
            (3, 'Kitchen Appliances', 'kitchen-appliances', 3),
            (4, 'Home Appliances', 'home-appliances', 4);
        ");
    }

    // Seed default products if table is empty
    $countProd = (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($countProd === 0) {
        $pdo->exec("
            INSERT INTO products (category_id, name, short_description, description, price, image) VALUES
            (2, 'Italian Tailored Slim Suit', 'Premium wool-blend tailored suit with modern slim silhouette.', 'Crafted from fine wool blend fabrics, this tailored suit offers a modern silhouette, silk lining, and peak lapels for weddings and formal events.', 14500.00, 'product-1.jpg'),
            (1, 'Emerald Silk Evening Dress', 'Elegant flowing emerald green silk evening gown.', 'Exquisite floor-length silk gown with subtle side drape, delicate shoulder straps, and premium satin finish.', 8900.00, 'product-2.jpg'),
            (3, 'Digital Smart Air Fryer 6.5L', '1800W rapid-air technology with touch presets and glass view.', 'High capacity 6.5-liter digital air fryer with 10 one-touch cooking presets, dual heating elements, and easy-clean non-stick basket.', 12500.00, 'product-3.jpg'),
            (3, 'Pro Barista Espresso Machine', '15-bar Italian pump with stainless steel milk steam wand.', 'Commercial grade compact espresso maker with integrated pressure gauge, precision thermal PID control, and powerful micro-foam steaming.', 32000.00, 'product-4.jpg'),
            (4, 'Cordless Smart Vacuum Cleaner', '25000Pa suction power with HEPA filtration and LED head.', 'Ultra-lightweight cordless stick vacuum with intelligent surface sensing, 60-minute runtime battery, and 5-stage allergen HEPA filtration.', 18500.00, 'product-5.jpg'),
            (4, 'Smart Garment Care Steam Station', 'Continuous high-pressure steam for fast wrinkle removal.', 'Pro-level upright garment steamer with vertical board, ceramic soleplate, fast 45-second heat-up, and anti-calc system.', 9800.00, 'product-6.jpg');
        ");
    }

    $checked = true;
}

// ---- Storefront Helpers ----

function getCategories() {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id, name, slug, display_order FROM categories ORDER BY display_order ASC, name ASC');
    return $stmt->fetchAll();
}

function getProducts($categorySlug = null, $search = null) {
    $pdo = getDbConnection();
    $sql = 'SELECT p.id, p.name, p.short_description, p.description, p.price, p.image, c.slug AS category_slug, c.name AS category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id';
    $params = [];

    if ($categorySlug !== null && $categorySlug !== '') {
        $sql .= ' WHERE c.slug = :slug';
        $params['slug'] = $categorySlug;
    }

    if ($search !== null && trim($search) !== '') {
        $searchTerm = '%' . trim($search) . '%';
        $sql .= ($categorySlug !== null && $categorySlug !== '') ? ' AND' : ' WHERE';
        $sql .= ' (p.name LIKE :search OR p.short_description LIKE :search OR p.description LIKE :search)';
        $params['search'] = $searchTerm;
    }

    $sql .= ' ORDER BY p.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function getProductById($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT p.id, p.category_id, p.name, p.short_description, p.description, p.price, p.image, p.created_at, c.slug AS category_slug, c.name AS category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = :id');
    $stmt->execute(['id' => (int)$id]);
    return $stmt->fetch();
}

function addToCart($productId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $productId = (int) $productId;
    if ($productId <= 0) {
        return;
    }
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }
    $_SESSION['cart'][$productId]++;
}

function getCartItems() {
    $items = [];
    if (empty($_SESSION['cart'])) {
        return $items;
    }
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProductById($productId);
        if ($product) {
            $product['quantity'] = (int)$quantity;
            $items[] = $product;
        }
    }
    return $items;
}

// ---- Authentication & Route Guards ----

function requireAdmin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: index.php');
        exit;
    }
}

function requireMasterAdmin() {
    requireAdmin();
    if (empty($_SESSION['is_master'])) {
        setFlash('error', 'Unauthorized: Master Admin privileges required.');
        header('Location: dashboard.php');
        exit;
    }
}

function getAdminByEmail($email) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT id, email, password_hash, is_master, created_at FROM admins WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => trim($email)]);
    return $stmt->fetch();
}

function createAdmin($email, $password, $is_master = 0) {
    $pdo = getDbConnection();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (email, password_hash, is_master) VALUES (:email, :hash, :is_master)');
    $stmt->execute([
        'email' => trim($email),
        'hash' => $hash,
        'is_master' => (int)$is_master
    ]);
    return $pdo->lastInsertId();
}

function countMasterAdmins() {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT COUNT(*) FROM admins WHERE is_master = 1');
    return (int) $stmt->fetchColumn();
}

function countTotalAdmins() {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT COUNT(*) FROM admins');
    return (int) $stmt->fetchColumn();
}

function verifyAdminCredentials($email, $password) {
    $admin = getAdminByEmail($email);
    if (!$admin) return false;
    if (!password_verify($password, $admin['password_hash'])) return false;
    return $admin;
}

function getAdmins() {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id, email, is_master, created_at FROM admins ORDER BY is_master DESC, id ASC');
    return $stmt->fetchAll();
}

function updateAdminPassword($adminId, $newPassword) {
    $pdo = getDbConnection();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE admins SET password_hash = :hash WHERE id = :id');
    return $stmt->execute(['hash' => $hash, 'id' => (int)$adminId]);
}

function deleteAdminUser($adminId) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM admins WHERE id = :id AND is_master = 0');
    return $stmt->execute(['id' => (int)$adminId]);
}

// ---- Product CRUD Helpers ----

function getAllProducts() {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT p.*, c.slug AS category_slug, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC');
    return $stmt->fetchAll();
}

function addProduct($category_id, $name, $short_description, $description, $price, $image) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO products (category_id, name, short_description, description, price, image) VALUES (:category_id, :name, :short, :desc, :price, :image)');
    $stmt->execute([
        'category_id' => (int)$category_id,
        'name' => trim($name),
        'short' => trim($short_description),
        'desc' => trim($description),
        'price' => (float)$price,
        'image' => trim($image)
    ]);
    return $pdo->lastInsertId();
}

function updateProduct($id, $category_id, $name, $short_description, $description, $price, $image = null) {
    $pdo = getDbConnection();
    if ($image !== null && $image !== '') {
        $stmt = $pdo->prepare('UPDATE products SET category_id = :category_id, name = :name, short_description = :short, description = :desc, price = :price, image = :image WHERE id = :id');
        $stmt->execute([
            'category_id' => (int)$category_id,
            'name' => trim($name),
            'short' => trim($short_description),
            'desc' => trim($description),
            'price' => (float)$price,
            'image' => trim($image),
            'id' => (int)$id
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE products SET category_id = :category_id, name = :name, short_description = :short, description = :desc, price = :price WHERE id = :id');
        $stmt->execute([
            'category_id' => (int)$category_id,
            'name' => trim($name),
            'short' => trim($short_description),
            'desc' => trim($description),
            'price' => (float)$price,
            'id' => (int)$id
        ]);
    }
}

function deleteProduct($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => (int)$id]);
}

// ---- Category CRUD Helpers ----

function getAllCategories() {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT c.*, COUNT(p.id) AS product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id 
                         GROUP BY c.id 
                         ORDER BY c.display_order ASC, c.name ASC');
    return $stmt->fetchAll();
}

function addCategory($name, $slug, $display_order = 0) {
    $pdo = getDbConnection();
    $slug = sanitizeSlug($slug ?: $name);
    $stmt = $pdo->prepare('INSERT INTO categories (name, slug, display_order) VALUES (:name, :slug, :display_order)');
    $stmt->execute([
        'name' => trim($name),
        'slug' => $slug,
        'display_order' => (int)$display_order
    ]);
    return $pdo->lastInsertId();
}

function updateCategory($id, $name, $slug, $display_order = 0) {
    $pdo = getDbConnection();
    $slug = sanitizeSlug($slug ?: $name);
    $stmt = $pdo->prepare('UPDATE categories SET name = :name, slug = :slug, display_order = :display_order WHERE id = :id');
    $stmt->execute([
        'name' => trim($name),
        'slug' => $slug,
        'display_order' => (int)$display_order,
        'id' => (int)$id
    ]);
}

function deleteCategory($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->execute(['id' => (int)$id]);
}

function sanitizeSlug($string) {
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = preg_replace('~[^-\w]+~', '', $string);
    $string = trim($string, '-');
    $string = preg_replace('~-+~', '-', $string);
    return strtolower($string ?: 'n-a');
}

// ---- Image Upload Handler ----

function handleImageUpload($fileArray, &$error = null) {
    if (!isset($fileArray) || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No file uploaded
    }

    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload error (Code: ' . $fileArray['error'] . ').';
        return false;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $fileInfo = pathinfo($fileArray['name']);
    $ext = strtolower($fileInfo['extension'] ?? '');

    if (!in_array($ext, $allowedExtensions)) {
        $error = 'Invalid image format. Allowed: JPG, PNG, WEBP, GIF.';
        return false;
    }

    // Max 5MB
    if ($fileArray['size'] > 5 * 1024 * 1024) {
        $error = 'Image file too large (Max 5MB).';
        return false;
    }

    $uploadDir = __DIR__ . '/../public/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $baseName = sanitizeSlug($fileInfo['filename']);
    $newFileName = $baseName . '-' . time() . '.' . $ext;
    $destination = $uploadDir . $newFileName;

    if (move_uploaded_file($fileArray['tmp_name'], $destination)) {
        return $newFileName;
    } else {
        $error = 'Failed to save uploaded image.';
        return false;
    }
}

// ---- Dashboard Analytics Helper ----

function getDashboardStats() {
    $pdo = getDbConnection();
    $totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $totalCategories = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    $totalAdmins = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    $inventoryValue = (float) $pdo->query('SELECT COALESCE(SUM(price), 0) FROM products')->fetchColumn();

    $recentProducts = $pdo->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 5')->fetchAll();

    return [
        'total_products' => $totalProducts,
        'total_categories' => $totalCategories,
        'total_admins' => $totalAdmins,
        'inventory_value' => $inventoryValue,
        'recent_products' => $recentProducts,
    ];
}

// ---- Flash Messaging ----

function setFlash($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message,
    ];
}

function getFlashes() {
    if (empty($_SESSION['flash_messages'])) {
        return [];
    }
    $flashes = $_SESSION['flash_messages'];
    unset($_SESSION['flash_messages']);
    return $flashes;
}

// ---- CSRF Protection ----

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

?>