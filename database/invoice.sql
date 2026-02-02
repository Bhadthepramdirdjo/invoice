-- ============================================
-- Database Schema untuk Invoice App
-- ============================================
-- Dibuat: 2026-02-02
-- Deskripsi: Schema lengkap untuk aplikasi invoice
--            dengan fitur price list dan manajemen invoice
-- ============================================

-- Buat database (jika belum ada)
CREATE DATABASE IF NOT EXISTS `invoice_app` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `invoice_app`;

-- ============================================
-- Tabel: categories
-- Deskripsi: Kategori produk/jasa
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: products
-- Deskripsi: Daftar produk/jasa (Price List)
-- ============================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) DEFAULT 'pcs',
  `stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `category_id` (`category_id`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: customers
-- Deskripsi: Data pelanggan
-- ============================================
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL COMMENT 'NPWP',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `email` (`email`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: invoices
-- Deskripsi: Data invoice utama
-- ============================================
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_company` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_city` varchar(100) DEFAULT NULL,
  `customer_tax_id` varchar(50) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('percentage','fixed') DEFAULT NULL,
  `discount_value` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'PPN dalam persen',
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','sent','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL COMMENT 'Syarat & ketentuan',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `customer_id` (`customer_id`),
  KEY `invoice_date` (`invoice_date`),
  KEY `status` (`status`),
  CONSTRAINT `fk_invoices_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: invoice_items
-- Deskripsi: Detail item per invoice
-- ============================================
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit` varchar(50) DEFAULT 'pcs',
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('percentage','fixed') DEFAULT NULL,
  `discount_value` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invoice_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: payments
-- Deskripsi: Riwayat pembayaran invoice
-- ============================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `payment_number` varchar(50) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL COMMENT 'No. referensi bank/transfer',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `payment_date` (`payment_date`),
  CONSTRAINT `fk_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: users (opsional - untuk multi-user)
-- Deskripsi: Data pengguna sistem
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','staff','viewer') NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: company_settings
-- Deskripsi: Pengaturan informasi perusahaan
-- ============================================
CREATE TABLE IF NOT EXISTS `company_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_address` text DEFAULT NULL,
  `company_city` varchar(100) DEFAULT NULL,
  `company_province` varchar(100) DEFAULT NULL,
  `company_postal_code` varchar(20) DEFAULT NULL,
  `company_phone` varchar(50) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `company_tax_id` varchar(50) DEFAULT NULL COMMENT 'NPWP',
  `company_logo` varchar(255) DEFAULT NULL,
  `invoice_prefix` varchar(20) DEFAULT 'INV',
  `invoice_number_format` varchar(50) DEFAULT '{PREFIX}-{YEAR}-{NUMBER}',
  `invoice_next_number` int(11) DEFAULT 1,
  `tax_rate` decimal(5,2) DEFAULT 11.00 COMMENT 'PPN default',
  `currency` varchar(10) DEFAULT 'IDR',
  `invoice_terms` text DEFAULT NULL,
  `invoice_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: activity_logs (opsional)
-- Deskripsi: Log aktivitas sistem
-- ============================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DATA AWAL
-- ============================================

-- Insert kategori default
INSERT INTO `categories` (`name`, `description`) VALUES
('Elektronik', 'Produk elektronik dan gadget'),
('Makanan & Minuman', 'Produk makanan dan minuman'),
('Pakaian', 'Pakaian dan aksesoris'),
('Jasa', 'Layanan jasa'),
('Lainnya', 'Kategori lainnya');

-- Insert produk contoh
INSERT INTO `products` (`category_id`, `code`, `name`, `description`, `price`, `unit`, `stock`, `min_stock`) VALUES
(1, 'PROD-001', 'Laptop ASUS ROG', 'Laptop gaming high-end dengan spesifikasi terbaik', 15000000.00, 'pcs', 10, 2),
(1, 'PROD-002', 'Mouse Logitech MX Master', 'Mouse wireless ergonomis untuk produktivitas', 1250000.00, 'pcs', 25, 5),
(1, 'PROD-003', 'Keyboard Mechanical RGB', 'Keyboard mechanical dengan RGB lighting', 1500000.00, 'pcs', 15, 3),
(1, 'PROD-004', 'Monitor LG 27 inch 4K', 'Monitor 4K UHD untuk desain dan gaming', 5000000.00, 'pcs', 8, 2),
(2, 'PROD-005', 'Kopi Arabica Premium', 'Kopi arabica pilihan dari pegunungan', 150000.00, 'kg', 100, 20),
(3, 'PROD-006', 'Kaos Polos Cotton', 'Kaos polos berbahan cotton combed 30s', 75000.00, 'pcs', 200, 50),
(4, 'PROD-007', 'Jasa Desain Grafis', 'Jasa desain grafis profesional', 500000.00, 'project', 0, 0),
(4, 'PROD-008', 'Jasa Pembuatan Website', 'Jasa pembuatan website profesional', 5000000.00, 'project', 0, 0);

-- Insert customer contoh
INSERT INTO `customers` (`code`, `name`, `company`, `email`, `phone`, `address`, `city`, `province`, `postal_code`, `tax_id`) VALUES
('CUST-001', 'Budi Santoso', 'PT. Maju Jaya', 'budi@majujaya.com', '081234567890', 'Jl. Sudirman No. 123', 'Jakarta', 'DKI Jakarta', '12190', '01.234.567.8-901.000'),
('CUST-002', 'Siti Nurhaliza', 'CV. Berkah Selalu', 'siti@berkahselalu.com', '082345678901', 'Jl. Gatot Subroto No. 456', 'Bandung', 'Jawa Barat', '40123', '02.345.678.9-012.000'),
('CUST-003', 'Ahmad Fauzi', 'UD. Sejahtera Abadi', 'ahmad@sejahtera.com', '083456789012', 'Jl. Ahmad Yani No. 789', 'Surabaya', 'Jawa Timur', '60234', '03.456.789.0-123.000');

-- Insert company settings default
INSERT INTO `company_settings` (
  `company_name`, 
  `company_address`, 
  `company_city`, 
  `company_province`, 
  `company_postal_code`,
  `company_phone`, 
  `company_email`, 
  `company_website`,
  `company_tax_id`,
  `invoice_prefix`,
  `invoice_number_format`,
  `invoice_next_number`,
  `tax_rate`,
  `currency`,
  `invoice_terms`,
  `invoice_notes`
) VALUES (
  'PT. Invoice App Indonesia',
  'Jl. Contoh Alamat No. 123',
  'Jakarta',
  'DKI Jakarta',
  '12345',
  '(021) 1234-5678',
  'info@invoiceapp.com',
  'www.invoiceapp.com',
  '00.123.456.7-890.000',
  'INV',
  '{PREFIX}-{YEAR}-{NUMBER}',
  1,
  11.00,
  'IDR',
  'Pembayaran harap dilakukan maksimal 30 hari setelah tanggal invoice.\nTransfer ke rekening:\nBank BCA 1234567890 a.n. PT. Invoice App Indonesia',
  'Terima kasih atas kepercayaan Anda menggunakan layanan kami.'
);

-- Insert user default (password: admin123)
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@invoiceapp.com', 'admin');

-- Insert invoice contoh
INSERT INTO `invoices` (
  `invoice_number`,
  `customer_id`,
  `customer_name`,
  `customer_company`,
  `customer_email`,
  `customer_phone`,
  `customer_address`,
  `customer_city`,
  `customer_tax_id`,
  `invoice_date`,
  `due_date`,
  `subtotal`,
  `tax_rate`,
  `tax_amount`,
  `total`,
  `status`
) VALUES (
  'INV-2026-001',
  1,
  'Budi Santoso',
  'PT. Maju Jaya',
  'budi@majujaya.com',
  '081234567890',
  'Jl. Sudirman No. 123',
  'Jakarta',
  '01.234.567.8-901.000',
  '2026-02-01',
  '2026-03-03',
  31250000.00,
  11.00,
  3437500.00,
  34687500.00,
  'sent'
);

-- Insert invoice items untuk invoice contoh
INSERT INTO `invoice_items` (
  `invoice_id`,
  `product_id`,
  `product_code`,
  `product_name`,
  `description`,
  `quantity`,
  `unit`,
  `unit_price`,
  `subtotal`,
  `sort_order`
) VALUES
(1, 1, 'PROD-001', 'Laptop ASUS ROG', 'Laptop gaming high-end dengan spesifikasi terbaik', 2.00, 'pcs', 15000000.00, 30000000.00, 1),
(1, 2, 'PROD-002', 'Mouse Logitech MX Master', 'Mouse wireless ergonomis untuk produktivitas', 5.00, 'pcs', 250000.00, 1250000.00, 2);

-- ============================================
-- VIEWS (Optional - untuk reporting)
-- ============================================

-- View: Invoice Summary
CREATE OR REPLACE VIEW `view_invoice_summary` AS
SELECT 
  i.id,
  i.invoice_number,
  i.invoice_date,
  i.due_date,
  i.customer_name,
  i.customer_company,
  i.subtotal,
  i.tax_amount,
  i.total,
  i.paid_amount,
  (i.total - i.paid_amount) AS balance,
  i.status,
  COUNT(ii.id) AS total_items,
  i.created_at
FROM invoices i
LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
GROUP BY i.id
ORDER BY i.invoice_date DESC;

-- View: Product Stock Status
CREATE OR REPLACE VIEW `view_product_stock` AS
SELECT 
  p.id,
  p.code,
  p.name,
  c.name AS category_name,
  p.price,
  p.unit,
  p.stock,
  p.min_stock,
  CASE 
    WHEN p.stock = 0 THEN 'Habis'
    WHEN p.stock <= p.min_stock THEN 'Stok Menipis'
    ELSE 'Tersedia'
  END AS stock_status,
  p.is_active
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.name;

-- View: Customer Invoice Summary
CREATE OR REPLACE VIEW `view_customer_summary` AS
SELECT 
  c.id,
  c.code,
  c.name,
  c.company,
  c.email,
  c.phone,
  COUNT(i.id) AS total_invoices,
  COALESCE(SUM(i.total), 0) AS total_amount,
  COALESCE(SUM(i.paid_amount), 0) AS total_paid,
  COALESCE(SUM(i.total - i.paid_amount), 0) AS total_balance,
  c.is_active
FROM customers c
LEFT JOIN invoices i ON c.id = i.customer_id
GROUP BY c.id
ORDER BY c.name;

-- ============================================
-- STORED PROCEDURES (Optional)
-- ============================================

-- Procedure: Generate Invoice Number
DELIMITER $$
CREATE PROCEDURE `sp_generate_invoice_number`()
BEGIN
  DECLARE v_prefix VARCHAR(20);
  DECLARE v_format VARCHAR(50);
  DECLARE v_next_number INT;
  DECLARE v_year VARCHAR(4);
  DECLARE v_invoice_number VARCHAR(50);
  
  -- Get settings
  SELECT invoice_prefix, invoice_number_format, invoice_next_number
  INTO v_prefix, v_format, v_next_number
  FROM company_settings
  LIMIT 1;
  
  -- Get current year
  SET v_year = YEAR(CURDATE());
  
  -- Generate invoice number
  SET v_invoice_number = REPLACE(v_format, '{PREFIX}', v_prefix);
  SET v_invoice_number = REPLACE(v_invoice_number, '{YEAR}', v_year);
  SET v_invoice_number = REPLACE(v_invoice_number, '{NUMBER}', LPAD(v_next_number, 4, '0'));
  
  -- Update next number
  UPDATE company_settings SET invoice_next_number = invoice_next_number + 1;
  
  -- Return invoice number
  SELECT v_invoice_number AS invoice_number;
END$$
DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger: Update invoice total saat item berubah
DELIMITER $$
CREATE TRIGGER `tr_update_invoice_total_after_insert` 
AFTER INSERT ON `invoice_items`
FOR EACH ROW
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_tax_rate DECIMAL(5,2);
  DECLARE v_tax_amount DECIMAL(15,2);
  DECLARE v_total DECIMAL(15,2);
  
  -- Calculate subtotal
  SELECT COALESCE(SUM(subtotal), 0) INTO v_subtotal
  FROM invoice_items
  WHERE invoice_id = NEW.invoice_id;
  
  -- Get tax rate
  SELECT tax_rate INTO v_tax_rate
  FROM invoices
  WHERE id = NEW.invoice_id;
  
  -- Calculate tax and total
  SET v_tax_amount = v_subtotal * (v_tax_rate / 100);
  SET v_total = v_subtotal + v_tax_amount;
  
  -- Update invoice
  UPDATE invoices
  SET subtotal = v_subtotal,
      tax_amount = v_tax_amount,
      total = v_total
  WHERE id = NEW.invoice_id;
END$$

CREATE TRIGGER `tr_update_invoice_total_after_update` 
AFTER UPDATE ON `invoice_items`
FOR EACH ROW
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_tax_rate DECIMAL(5,2);
  DECLARE v_tax_amount DECIMAL(15,2);
  DECLARE v_total DECIMAL(15,2);
  
  -- Calculate subtotal
  SELECT COALESCE(SUM(subtotal), 0) INTO v_subtotal
  FROM invoice_items
  WHERE invoice_id = NEW.invoice_id;
  
  -- Get tax rate
  SELECT tax_rate INTO v_tax_rate
  FROM invoices
  WHERE id = NEW.invoice_id;
  
  -- Calculate tax and total
  SET v_tax_amount = v_subtotal * (v_tax_rate / 100);
  SET v_total = v_subtotal + v_tax_amount;
  
  -- Update invoice
  UPDATE invoices
  SET subtotal = v_subtotal,
      tax_amount = v_tax_amount,
      total = v_total
  WHERE id = NEW.invoice_id;
END$$

CREATE TRIGGER `tr_update_invoice_total_after_delete` 
AFTER DELETE ON `invoice_items`
FOR EACH ROW
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_tax_rate DECIMAL(5,2);
  DECLARE v_tax_amount DECIMAL(15,2);
  DECLARE v_total DECIMAL(15,2);
  
  -- Calculate subtotal
  SELECT COALESCE(SUM(subtotal), 0) INTO v_subtotal
  FROM invoice_items
  WHERE invoice_id = OLD.invoice_id;
  
  -- Get tax rate
  SELECT tax_rate INTO v_tax_rate
  FROM invoices
  WHERE id = OLD.invoice_id;
  
  -- Calculate tax and total
  SET v_tax_amount = v_subtotal * (v_tax_rate / 100);
  SET v_total = v_subtotal + v_tax_amount;
  
  -- Update invoice
  UPDATE invoices
  SET subtotal = v_subtotal,
      tax_amount = v_tax_amount,
      total = v_total
  WHERE id = OLD.invoice_id;
END$$
DELIMITER ;

-- ============================================
-- INDEXES untuk Performance
-- ============================================

-- Additional indexes untuk query optimization
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_customers_name ON customers(name);
CREATE INDEX idx_invoices_customer_date ON invoices(customer_id, invoice_date);
CREATE INDEX idx_invoice_items_invoice_product ON invoice_items(invoice_id, product_id);

-- ============================================
-- SELESAI
-- ============================================
-- Database schema berhasil dibuat!
-- 
-- Tabel yang dibuat:
-- 1. categories - Kategori produk
-- 2. products - Daftar produk/price list
-- 3. customers - Data pelanggan
-- 4. invoices - Invoice utama
-- 5. invoice_items - Detail item invoice
-- 6. payments - Riwayat pembayaran
-- 7. users - Pengguna sistem
-- 8. company_settings - Pengaturan perusahaan
-- 9. activity_logs - Log aktivitas
--
-- Views:
-- 1. view_invoice_summary - Ringkasan invoice
-- 2. view_product_stock - Status stok produk
-- 3. view_customer_summary - Ringkasan per customer
--
-- Stored Procedures:
-- 1. sp_generate_invoice_number - Generate nomor invoice otomatis
--
-- Triggers:
-- 1. tr_update_invoice_total_* - Auto update total invoice
--
-- Data contoh sudah diinsert untuk testing
-- ============================================
