<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database config only if not already loaded
if (!function_exists('getDB')) {
    require_once __DIR__ . '/../config/database.php';
}

// Get company settings
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
    <title><?php echo $pageTitle ?? 'Invoice App'; ?> - <?php echo $company['company_name']; ?></title>
    
    <!-- TailwindCSS -->
    <link href="<?php echo $baseUrl ?? ''; ?>css/output.css" rel="stylesheet">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-invoice-primary to-invoice-secondary rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Invoice App</h1>
                        <p class="text-xs text-gray-500"><?php echo $company['company_name']; ?></p>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <div class="hidden md:flex items-center gap-2">
                    <a href="<?php echo $baseUrl ?? ''; ?>index.php" class="nav-link <?php echo ($currentPage ?? '') == 'dashboard' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="<?php echo $baseUrl ?? ''; ?>page/products/list.php" class="nav-link <?php echo ($currentPage ?? '') == 'products' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Produk
                    </a>
                    <a href="<?php echo $baseUrl ?? ''; ?>page/customers/list.php" class="nav-link <?php echo ($currentPage ?? '') == 'customers' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Customer
                    </a>
                    <a href="<?php echo $baseUrl ?? ''; ?>page/invoices/list.php" class="nav-link <?php echo ($currentPage ?? '') == 'invoices' ? 'active' : ''; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Invoice
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-2">
                <a href="<?php echo $baseUrl ?? ''; ?>index.php" class="block py-2 px-4 rounded-lg hover:bg-gray-100">
                    Dashboard
                </a>
                <a href="<?php echo $baseUrl ?? ''; ?>page/products/list.php" class="block py-2 px-4 rounded-lg hover:bg-gray-100">
                    Produk
                </a>
                <a href="<?php echo $baseUrl ?? ''; ?>page/customers/list.php" class="block py-2 px-4 rounded-lg hover:bg-gray-100">
                    Customer
                </a>
                <a href="<?php echo $baseUrl ?? ''; ?>page/invoices/list.php" class="block py-2 px-4 rounded-lg hover:bg-gray-100">
                    Invoice
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Flash Message -->
    <?php
    $flash = getFlash();
    if ($flash):
    ?>
    <div class="container mx-auto px-4 mt-4">
        <div class="alert alert-<?php echo $flash['type']; ?> animate-fade-in">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <?php if ($flash['type'] == 'success'): ?>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                <?php else: ?>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                <?php endif; ?>
            </svg>
            <span><?php echo htmlspecialchars($flash['message']); ?></span>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
