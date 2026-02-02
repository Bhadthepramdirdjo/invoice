<?php
$pageTitle = 'Daftar Produk';
$currentPage = 'products';
$baseUrl = '../../';

require_once '../../config/database.php';
$db = getDB();
$company = getCompanySettings($db);

// Handle Search
$search = $_GET['search'] ?? '';
$searchQuery = "";
$params = [];

if ($search) {
    $searchQuery = " AND (name LIKE ? OR code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Fetch Products
$query = "SELECT * FROM products WHERE is_active = 1 $searchQuery ORDER BY name ASC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo $company['company_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
    </style>
</head>
<body class="text-gray-800">

    <!-- Navbar -->
    <nav class="glass-header fixed w-full z-10 top-0">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-center relative">
                <!-- Logo Removed -->
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="../../index.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Dashboard</a>
                    <a href="list.php" class="text-blue-600 font-semibold transition duration-200">Produk</a>
                    <a href="../customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Customer</a>
                    <a href="../invoices/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Invoice</a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center absolute right-0">
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
                    <a href="../../index.php" class="text-gray-500 hover:text-blue-600 font-medium">Dashboard</a>
                    <a href="list.php" class="text-blue-600 font-semibold">Produk</a>
                    <a href="../customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Customer</a>
                    <a href="../invoices/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Invoice</a>
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
    <main class="container mx-auto px-6 pt-24 pb-12">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Produk</h1>
                <p class="text-gray-500 mt-1">Daftar semua produk dan layanan Anda.</p>
            </div>
            <a href="form.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-200 transition duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Produk
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <form action="" method="GET" class="flex gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" 
                           placeholder="Cari nama produk atau kode...">
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium transition duration-200">
                    Cari
                </button>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden border border-gray-200">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="../../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    <?php echo htmlspecialchars($product['code']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                        $unit = $product['unit'] ?? 'pcs';
                                        
                                        // Tampilan Khusus untuk BOX yang ada isinya
                                        if (strtolower($unit) === 'box' && !empty($product['items_per_unit']) && $product['items_per_unit'] > 1) {
                                            echo '<div class="flex flex-col">';
                                            echo '<span class="px-2.5 py-1 w-max inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100 uppercase">BOX</span>';
                                            echo '<span class="text-xs text-gray-500 mt-1 font-medium">Isi: ' . $product['items_per_unit'] . ' pcs</span>';
                                            echo '</div>';
                                        } 
                                        // Style badge standart untuk satuan pendek lainnya
                                        elseif (strlen($unit) <= 5) {
                                            echo '<span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100 uppercase">' . htmlspecialchars($unit) . '</span>';
                                        } 
                                        // Text biasa untuk custom (panjang)
                                        else {
                                            echo '<span class="text-sm text-gray-700 font-medium bg-gray-100 px-2 py-1 rounded">'.htmlspecialchars($unit).'</span>';
                                        }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php 
                                        $stockClass = 'bg-green-100 text-green-800';
                                        if ($product['stock'] <= 5) $stockClass = 'bg-red-100 text-red-800';
                                        elseif ($product['stock'] <= 20) $stockClass = 'bg-yellow-100 text-yellow-800';
                                    ?>
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $stockClass; ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="form.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition">
                                            Edit
                                        </a>
                                        <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p class="mb-2">Belum ada produk.</p>
                                    <a href="form.php" class="text-blue-600 font-medium hover:underline">Tambah produk baru</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination (Simple) -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">Menampilkan <?php echo count($products); ?> produk</span>
            </div>
        </div>
        
    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Heroicon name: outline/exclamation -->
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Produk</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeSuccessModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Berhasil</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Produk berhasil dihapus dari sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeSuccessModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let productToDelete = null;

        function deleteProduct(id) {
            productToDelete = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            productToDelete = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (productToDelete) {
                window.location.href = 'delete.php?id=' + productToDelete;
            }
        });

        // Check URL for deleted param
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('deleted')) {
            document.getElementById('successModal').classList.remove('hidden');
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }
    </script>


</body>
</html>
