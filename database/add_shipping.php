<?php
require_once '../config/database.php';
$db = getDB();

try {
    // Check if shipping_fee exists
    $stmt = $db->query("SHOW COLUMNS FROM invoices LIKE 'shipping_fee'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $db->exec("ALTER TABLE invoices ADD COLUMN shipping_fee DECIMAL(15, 2) DEFAULT 0 AFTER total");
        echo "Kolom 'shipping_fee' berhasil ditambahkan.<br>";
    } else {
        echo "Kolom 'shipping_fee' sudah ada.<br>";
    }
    
    echo "<h3>Update Database Selesai!</h3>";
    echo "<p>Sekarang Anda bisa memasukkan Ongkos Kirim pada invoice.</p>";
    echo "<a href='../page/invoices/create.php'>Buat Invoice Baru</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
