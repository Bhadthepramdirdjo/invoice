<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$baseUrl = '';

require_once 'config/database.php';
$db = getDB();
$company = getCompanySettings($db);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo $company['company_name']; ?></title>
    <!-- Use Tailwind CDN for consistency -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    
    <!-- Navbar -->
    <nav class="glass-header fixed w-full z-10 top-0">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-200">
                        IA
                    </div>
                    <div>
                        <span class="block font-bold text-gray-800 leading-tight">Invoice App</span>
                        <span class="text-xs text-gray-500 font-medium">Manage your business</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="index.php" class="text-blue-600 font-semibold transition duration-200">Dashboard</a>
                    <a href="page/products/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Produk</a>
                    <a href="page/customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Customer</a>
                    <a href="page/invoices/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Invoice</a>
                </div>
                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-100">
                <div class="flex flex-col gap-4 mt-4">
                    <a href="index.php" class="text-blue-600 font-semibold">Dashboard</a>
                    <a href="page/products/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Produk</a>
                    <a href="page/customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Customer</a>
                    <a href="page/invoices/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Invoice</a>
                </div>
            </div>
        </div>
    </nav>
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    
    <!-- Main Content -->
    <main class="container mx-auto px-6 pt-24 pb-6">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Dashboard</h1>
            <p class="text-gray-500">Selamat datang di Invoice App</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            
            <a href="page/invoices/create.php" class="bg-white border border-gray-200 rounded-lg p-4 hover-lift flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Buat Invoice</h3>
                    <p class="text-xs text-gray-600">Buat invoice baru</p>
                </div>
            </a>
            
            <a href="page/invoices/list.php" class="bg-white border border-gray-200 rounded-lg p-4 hover-lift flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Daftar Invoice</h3>
                    <p class="text-xs text-gray-600">Lihat semua invoice</p>
                </div>
            </a>
            
            <a href="page/products/list.php" class="bg-white border border-gray-200 rounded-lg p-4 hover-lift flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-green-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Produk</h3>
                    <p class="text-xs text-gray-600">Kelola produk</p>
                </div>
            </a>
            
            <a href="page/customers/list.php" class="bg-white border border-gray-200 rounded-lg p-4 hover-lift flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Customer</h3>
                    <p class="text-xs text-gray-600">Kelola customer</p>
                </div>
            </a>
            
        </div>
        

        
        <!-- Recent Invoices -->
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Invoice Terbaru</h2>
                    <p class="text-sm text-gray-600">Daftar invoice yang baru dibuat</p>
                </div>
                <a href="page/invoices/list.php" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua →
                </a>
            </div>
            
            <?php
            try {
                $stmt = $db->query("SELECT * FROM view_invoice_summary ORDER BY invoice_date DESC LIMIT 5");
                $invoices = $stmt->fetchAll();
                
                if (empty($invoices)) {
                    echo '<div class="px-6 py-12 text-center">';
                    echo '<div class="text-5xl mb-3">📄</div>';
                    echo '<p class="text-gray-600 mb-4">Belum ada invoice</p>';
                    echo '<a href="page/invoices/create.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">';
                    echo 'Buat Invoice Pertama';
                    echo '</a>';
                    echo '</div>';
                } else {
                    echo '<div class="overflow-x-auto">';
                    echo '<table class="w-full">';
                    echo '<thead>';
                    echo '<tr class="border-b border-gray-200 bg-gray-50">';
                    echo '<th class="text-left px-6 py-3 text-xs font-medium text-gray-600 uppercase">No. Invoice</th>';
                    echo '<th class="text-left px-6 py-3 text-xs font-medium text-gray-600 uppercase">Tanggal</th>';
                    echo '<th class="text-left px-6 py-3 text-xs font-medium text-gray-600 uppercase">Customer</th>';
                    echo '<th class="text-left px-6 py-3 text-xs font-medium text-gray-600 uppercase">Total</th>';
                    echo '<th class="text-left px-6 py-3 text-xs font-medium text-gray-600 uppercase">Status</th>';
                    echo '<th class="text-left px-6 py-3 text-xs font-medium text-gray-600 uppercase">Aksi</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody class="divide-y divide-gray-200">';
                    foreach ($invoices as $inv) {
                        echo '<tr class="hover:bg-gray-50">';
                        echo '<td class="px-6 py-4"><span class="font-medium text-blue-600">' . htmlspecialchars($inv['invoice_number']) . '</span></td>';
                        echo '<td class="px-6 py-4 text-sm text-gray-600">' . date('d/m/Y', strtotime($inv['invoice_date'])) . '</td>';
                        echo '<td class="px-6 py-4 text-sm text-gray-900">' . htmlspecialchars($inv['customer_name']) . '</td>';
                        echo '<td class="px-6 py-4 text-sm font-medium text-gray-900">Rp ' . number_format($inv['total'], 0, ',', '.') . '</td>';
                        echo '<td class="px-6 py-4"><span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded">' . htmlspecialchars($inv['status']) . '</span></td>';
                        echo '<td class="px-6 py-4">';
                        echo '<a href="page/invoices/view.php?id=' . $inv['id'] . '" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="px-6 py-4 bg-red-50 border-t border-red-100">';
                echo '<p class="text-sm text-red-600">Error: ' . $e->getMessage() . '</p>';
                echo '</div>';
            }
            ?>
        </div>
        
    </main>
    
    <!-- Footer -->
    <footer class="border-t border-gray-200 mt-12 py-6">
        <div class="container mx-auto px-6 text-center text-sm text-gray-600">
            <p>&copy; 2026 Bhadriko - HomeBakle33. All rights reserved.</p>
        </div>
    </footer>
    
</body>
</html>
