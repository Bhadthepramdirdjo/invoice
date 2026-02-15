<?php
require_once '../config/database.php';
$db = getDB();

try {
    echo "<h3>Final Database Update</h3>";
    
    // 1. ADD PACKAGING_FEE COLUMN
    echo "<h4>1. Menambahkan Kolom Packaging Fee...</h4>";
    $stmt = $db->query("SHOW COLUMNS FROM invoices LIKE 'packaging_fee'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $db->exec("ALTER TABLE invoices ADD COLUMN packaging_fee DECIMAL(15, 2) DEFAULT 0 AFTER subtotal");
        echo "✅ Kolom 'packaging_fee' berhasil ditambahkan.<br>";
    } else {
        echo "ℹ️ Kolom 'packaging_fee' sudah ada.<br>";
    }

    // 2. UPDATE COMPANY NAME
    echo "<h4>2. Mengubah Nama Perusahaan (Browser Title)...</h4>";
    // Check if company_settings table exists
    $stmtInfo = $db->query("SHOW TABLES LIKE 'company_settings'");
    if ($stmtInfo->fetch()) {
        $newName = "Invoice HomeBake33";
        $update = $db->prepare("UPDATE company_settings SET company_name = ?");
        $update->execute([$newName]);
        
        if ($update->rowCount() > 0) {
            echo "✅ Nama perusahaan diubah menjadi: <strong>$newName</strong><br>";
        } else {
            echo "ℹ️ Nama perusahaan sudah sesuai atau tidak ada perubahan.<br>";
        }
    } else {
        echo "❌ Tabel company_settings tidak ditemukan.<br>";
    }
    
    // 3. CHECK SHIPPING_FEE (Just in case)
    echo "<h4>3. Cek Kolom Shipping Fee...</h4>";
    $stmtShip = $db->query("SHOW COLUMNS FROM invoices LIKE 'shipping_fee'");
    if (!$stmtShip->fetch()) {
        $db->exec("ALTER TABLE invoices ADD COLUMN shipping_fee DECIMAL(15, 2) DEFAULT 0 AFTER total");
        echo "✅ Kolom 'shipping_fee' berhasil ditambahkan (Fixed).<br>";
    } else {
        echo "ℹ️ Kolom 'shipping_fee' sudah ada.<br>";
    }

    echo "<hr>";
    echo "<h3>🎉 UPDATE SELESAI!</h3>";
    echo "<p>Silakan kembali ke aplikasi.</p>";
    echo "<a href='../page/invoices/create.php' style='padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>Buka Invoice App</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
