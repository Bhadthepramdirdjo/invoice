<?php
$pageTitle = 'Daftar Customer';
$currentPage = 'customers';
$baseUrl = '../../';

require_once '../../config/database.php';
$db = getDB();
$company = getCompanySettings($db);

// Handle Search
$search = $_GET['search'] ?? '';
$searchQuery = "";
$params = [];

if ($search) {
    $searchQuery = " AND (name LIKE ? OR company LIKE ? OR email LIKE ? OR code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Fetch Customers
$query = "SELECT * FROM customers WHERE is_active = 1 $searchQuery ORDER BY name ASC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Customer - <?php echo $company['company_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
        .avatar-initial {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
                    <a href="../products/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Produk</a>
                    <a href="list.php" class="text-blue-600 font-semibold transition duration-200">Customer</a>
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
                    <a href="../products/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Produk</a>
                    <a href="list.php" class="text-blue-600 font-semibold">Customer</a>
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
                <h1 class="text-2xl font-bold text-gray-900">Kelola Customer</h1>
                <p class="text-gray-500 mt-1">Daftar pelanggan dan klien bisnis Anda.</p>
            </div>
            <a href="form.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-200 transition duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Customer
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
                           placeholder="Cari nama, perusahaan, atau email...">
                </div>
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium transition duration-200">
                    Cari
                </button>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer Info</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($customers) > 0): ?>
                            <?php foreach ($customers as $cust): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 avatar-initial text-white rounded-full flex items-center justify-center font-bold text-sm shadow-sm">
                                            <?php echo strtoupper(substr($cust['name'], 0, 2)); ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cust['name']); ?></div>
                                            <?php if (!empty($cust['company'])): ?>
                                                <div class="text-xs text-blue-600 font-medium"><?php echo htmlspecialchars($cust['company']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($cust['email'] ?? '-'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($cust['phone'] ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($cust['city'] ?? '-'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($cust['province'] ?? ''); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                        <?php echo htmlspecialchars($cust['code']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="form.php?id=<?php echo $cust['id']; ?>" class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition">
                                            Edit
                                        </a>
                                        <button onclick="deleteCustomer(<?php echo $cust['id']; ?>)" class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p class="mb-2">Belum ada customer.</p>
                                    <a href="form.php" class="text-blue-600 font-medium hover:underline">Tambah customer baru</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">Menampilkan <?php echo count($customers); ?> customer</span>
            </div>
        </div>
        
    </main>

    <!-- Delete Modal (Reusing the consistent design) -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Customer</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus data customer ini?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
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
                            <div class="mt-2"><p class="text-sm text-gray-500">Data customer berhasil diperbarui.</p></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeSuccessModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:text-sm">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;
        function deleteCustomer(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteId) window.location.href = 'delete.php?id=' + deleteId;
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('deleted') || urlParams.has('status')) {
            document.getElementById('successModal').classList.remove('hidden');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }
    </script>
</body>
</html>
