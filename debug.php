<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug</title></head><body>";
echo "<h1>Debug Index.php</h1>";
echo "<hr>";

echo "<h2>Step 1: PHP OK</h2>";
echo "PHP Version: " . phpversion() . "<br><br>";

echo "<h2>Step 2: Loading config...</h2>";
try {
    require_once 'config/database.php';
    echo "✅ Config loaded<br><br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
    die();
}

echo "<h2>Step 3: Connecting to database...</h2>";
try {
    $db = getDB();
    echo "✅ Database connected<br><br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
    die();
}

echo "<h2>Step 4: Getting company settings...</h2>";
try {
    $company = getCompanySettings($db);
    echo "✅ Company: " . $company['company_name'] . "<br><br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
}

echo "<h2>Step 5: Checking CSS file...</h2>";
if (file_exists('css/output.css')) {
    echo "✅ CSS file exists<br><br>";
} else {
    echo "❌ CSS file NOT FOUND!<br>";
    echo "Run: .\\build.ps1<br><br>";
}

echo "<h2>Step 6: Loading header...</h2>";
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$baseUrl = '';

try {
    require_once 'includes/header.php';
    echo "✅ Header loaded<br><br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>
