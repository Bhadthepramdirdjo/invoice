<?php
require_once '../config/database.php';
$db = getDB();

try {
    echo "<h3>Update Database - Manual Packaging</h3>";
    
    // 1. Check packaging_fee
    $stmt = $db->query("SHOW COLUMNS FROM invoices LIKE 'packaging_fee'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $db->exec("ALTER TABLE invoices ADD COLUMN packaging_fee DECIMAL(15, 2) DEFAULT 0 AFTER subtotal");
        echo "Kolom 'packaging_fee' berhasil ditambahkan.<br>";
    } else {
        echo "Kolom 'packaging_fee' sudah ada.<br>";
    }

    echo "<p>Selesai! Sekarang kolom packaging_fee sudah siap.</p>";
    echo "<a href='../page/invoices/create.php'>Kembali ke Buat Invoice</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
