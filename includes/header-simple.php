<?php
// Simplified header for testing
if (!function_exists('getDB')) {
    require_once __DIR__ . '/../config/database.php';
}

if (!isset($db)) {
    $db = getDB();
}
if (!isset($company)) {
    $company = getCompanySettings($db);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Invoice App'; ?></title>
    
    <!-- TailwindCSS -->
    <link href="<?php echo $baseUrl ?? ''; ?>css/output.css" rel="stylesheet">
    
    <style>
        body { font-family: Arial, sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Simple Navigation -->
    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto">
            <div class="flex gap-4">
                <a href="<?php echo $baseUrl ?? ''; ?>index.php" class="text-blue-600 hover:underline">Dashboard</a>
                <a href="<?php echo $baseUrl ?? ''; ?>page/products/list.php" class="text-blue-600 hover:underline">Produk</a>
                <a href="<?php echo $baseUrl ?? ''; ?>page/customers/list.php" class="text-blue-600 hover:underline">Customer</a>
                <a href="<?php echo $baseUrl ?? ''; ?>page/invoices/list.php" class="text-blue-600 hover:underline">Invoice</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
