<?php
/**
 * Test Page - Cek Koneksi Database
 */

echo "<h1>Test Invoice App</h1>";
echo "<hr>";

// Test 1: PHP Version
echo "<h2>1. PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "✅ PHP OK<br><br>";

// Test 2: Database Connection
echo "<h2>2. Database Connection</h2>";
try {
    require_once 'config/database.php';
    echo "✅ Database config loaded<br>";
    
    $db = getDB();
    echo "✅ Database connected<br>";
    
    // Test query
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<br><strong>Tables found (" . count($tables) . "):</strong><br>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        echo "✅ Database OK<br>";
    } else {
        echo "❌ No tables found! Database belum di-import!<br>";
        echo "<br><strong>Solusi:</strong><br>";
        echo "1. Buka phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
        echo "2. Import file: database/invoice.sql<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
    echo "<br><strong>Kemungkinan masalah:</strong><br>";
    echo "1. Database 'invoice_app' belum dibuat<br>";
    echo "2. Username/password MySQL salah<br>";
    echo "3. MySQL tidak running<br>";
    echo "<br><strong>Solusi:</strong><br>";
    echo "1. Buka phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
    echo "2. Import file: database/invoice.sql<br>";
}

echo "<hr>";

// Test 3: CSS File
echo "<h2>3. CSS File</h2>";
if (file_exists('css/output.css')) {
    $size = filesize('css/output.css');
    echo "✅ CSS file exists (" . number_format($size) . " bytes)<br>";
} else {
    echo "❌ CSS file not found!<br>";
    echo "<br><strong>Solusi:</strong><br>";
    echo "1. Jalankan: .\\build.ps1<br>";
    echo "2. Atau: .\\watch.ps1<br>";
}

echo "<hr>";

// Test 4: File Structure
echo "<h2>4. File Structure</h2>";
$files = [
    'index.php' => 'Dashboard',
    'config/database.php' => 'Database Config',
    'includes/header.php' => 'Header Component',
    'includes/footer.php' => 'Footer Component',
    'page/invoices/create.php' => 'Create Invoice',
    'api/invoices.php' => 'Invoice API',
    'js/invoice.js' => 'Invoice JS',
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $desc ($file)<br>";
    } else {
        echo "❌ $desc ($file) - NOT FOUND<br>";
    }
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Jika database belum di-import, import dulu: <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
echo "<li>Jika CSS belum ada, jalankan: <code>.\\build.ps1</code></li>";
echo "<li>Jika semua OK, akses: <a href='index.php'>Dashboard</a></li>";
echo "</ol>";
?>
