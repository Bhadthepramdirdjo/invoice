<?php
require_once '../config/database.php';
$db = getDB();

try {
    // 1. Create Packagings Table
    $db->exec("CREATE TABLE IF NOT EXISTS packagings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10, 2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Tabel 'packagings' berhasil dibuat/sudah ada.<br>";

    // Insert default packaging if empty
    $check = $db->query("SELECT COUNT(*) FROM packagings")->fetchColumn();
    if ($check == 0) {
        $db->exec("INSERT INTO packagings (name, price) VALUES 
            ('Standar (Plastik)', 0),
            ('Kardus Kecil', 2000),
            ('Kardus Besar', 5000),
            ('Bubble Wrap', 3000)
        ");
        echo "Data default packaging ditambahkan.<br>";
    }

    // 2. Update Invoices Table
    // Check if columns exist first to avoid errors
    $columns = $db->query("SHOW COLUMNS FROM invoices")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('shipping_fee', $columns)) {
        $db->exec("ALTER TABLE invoices ADD COLUMN shipping_fee DECIMAL(15, 2) DEFAULT 0 AFTER total");
        echo "Kolom 'shipping_fee' ditambahkan ke tabel invoices.<br>";
    }

    if (!in_array('packaging_fee', $columns)) {
        $db->exec("ALTER TABLE invoices ADD COLUMN packaging_fee DECIMAL(15, 2) DEFAULT 0 AFTER shipping_fee");
        $db->exec("ALTER TABLE invoices ADD COLUMN packaging_name VARCHAR(100) NULL AFTER packaging_fee");
        echo "Kolom 'packaging_fee' dan 'packaging_name' ditambahkan ke tabel invoices.<br>";
    }

    echo "<h3>Migrasi Database Selesai! Silakan hapus file ini.</h3>";
    echo "<a href='../index.php'>Kembali ke Dashboard</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
