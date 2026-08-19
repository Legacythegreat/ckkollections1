CREATE DATABASE IF NOT EXISTS `alcy_42591217_ckkollection` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `alcy_42591217_ckkollection`;

SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';
SET time_zone = '+00:00';

DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  display_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

INSERT INTO categories (id, name, slug, display_order) VALUES
  (1, 'Women\'s Fashion', 'womens-fashion', 1),
  (2, 'Men\'s Collection', 'mens-collection', 2),
  (3, 'Kitchen Appliances', 'kitchen-appliances', 3),
  (4, 'Home Appliances', 'home-appliances', 4)
ON DUPLICATE KEY UPDATE display_order = VALUES(display_order), name = VALUES(name);

INSERT INTO products (category_id, name, short_description, description, price, image) VALUES
  (2, 'Italian Tailored Slim Suit', 'Premium wool-blend tailored suit with modern slim silhouette.', 'Crafted from fine wool blend fabrics, this tailored suit offers a modern silhouette, silk lining, and peak lapels for weddings and formal events.', 14500.00, 'product-1.jpg'),
  (1, 'Emerald Silk Evening Dress', 'Elegant flowing emerald green silk evening gown.', 'Exquisite floor-length silk gown with subtle side drape, delicate shoulder straps, and premium satin finish.', 8900.00, 'product-2.jpg'),
  (3, 'Digital Smart Air Fryer 6.5L', '1800W rapid-air technology with touch presets and glass view.', 'High capacity 6.5-liter digital air fryer with 10 one-touch cooking presets, dual heating elements, and easy-clean non-stick basket.', 12500.00, 'product-3.jpg'),
  (3, 'Pro Barista Espresso Machine', '15-bar Italian pump with stainless steel milk steam wand.', 'Commercial grade compact espresso maker with integrated pressure gauge, precision thermal PID control, and powerful micro-foam steaming.', 32000.00, 'product-4.jpg'),
  (4, 'Cordless Smart Vacuum Cleaner', '25000Pa suction power with HEPA filtration and LED head.', 'Ultra-lightweight cordless stick vacuum with intelligent surface sensing, 60-minute runtime battery, and 5-stage allergen HEPA filtration.', 18500.00, 'product-5.jpg'),
  (4, 'Smart Garment Care Steam Station', 'Continuous high-pressure steam for fast wrinkle removal.', 'Pro-level upright garment steamer with vertical board, ceramic soleplate, fast 45-second heat-up, and anti-calc system.', 9800.00, 'product-6.jpg');

