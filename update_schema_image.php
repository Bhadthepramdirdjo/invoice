<?php
require_once 'config/database.php';
$db = getDB();

try {
    echo "Updating database schema...<br>";
    
    // Add image column if not exists
    $sql = "ALTER TABLE products ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER description";
    
    // Check if column exists first to avoid error
    $check = $db->query("SHOW COLUMNS FROM products LIKE 'image'");
    if ($check->rowCount() == 0) {
        $db->exec($sql);
        echo "✅ Berhasil menambahkan kolom 'image' ke tabel products.<br>";
    } else {
        echo "ℹ️ Kolom 'image' sudah ada.<br>";
    }
    
    echo "Done.";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
