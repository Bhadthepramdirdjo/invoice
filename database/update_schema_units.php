<?php
require_once '../config/database.php';

try {
    $db = getDB();
    
    // Cek apakah kolom items_per_unit sudah ada
    $check = $db->query("SHOW COLUMNS FROM products LIKE 'items_per_unit'");
    
    if ($check->rowCount() == 0) {
        // Jika belum ada, tambahkan kolom
        $sql = "ALTER TABLE products ADD COLUMN items_per_unit INT DEFAULT 1 COMMENT 'Jumlah pcs dalam satu unit (misal: isi 1 box)' AFTER unit";
        $db->exec($sql);
        echo "<h1>Berhasil!</h1>";
        echo "<p>Kolom 'items_per_unit' berhasil ditambahkan ke tabel products.</p>";
        echo "<p><a href='../page/products/list.php'>Kembali ke Daftar Produk</a></p>";
    } else {
        echo "<h1>Sudah Ada</h1>";
        echo "<p>Kolom 'items_per_unit' sudah ada di database, tidak perlu update.</p>";
        echo "<p><a href='../page/products/list.php'>Kembali ke Daftar Produk</a></p>";
    }

} catch (PDOException $e) {
    echo "<h1>Error</h1>";
    echo "<p>Gagal update database: " . $e->getMessage() . "</p>";
}
?>
