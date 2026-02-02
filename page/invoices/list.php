<?php
$pageTitle = 'Daftar Invoice';
$currentPage = 'invoices';
$baseUrl = '../../';

require_once '../../config/database.php';
$db = getDB();
$company = getCompanySettings($db);

// Handle Search & Sort
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'newest'; // Default: Created Time (Newest First)

$searchQuery = "";
$params = [];

if ($search) {
    $searchQuery .= " AND (invoice_number LIKE ? OR customer_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($statusFilter) {
    $searchQuery .= " AND status = ?";
    $params[] = $statusFilter;
}

// Determine Order Clause
switch ($sort) {
    case 'alphabet_asc':
        $orderClause = "customer_name ASC, invoice_number ASC";
        break;
    case 'alphabet_desc':
        $orderClause = "customer_name DESC, invoice_number DESC";
        break;
    case 'oldest':
        $orderClause = "id ASC";
        break;
    case 'newest':
    default:
        $orderClause = "id DESC"; 
        break;
}

// Fetch Invoices
$query = "SELECT * FROM invoices WHERE 1=1 $searchQuery ORDER BY $orderClause";
$stmt = $db->prepare($query);
$stmt->execute($params);
$invoices = $stmt->fetchAll();

// Helper status colors
function getStatusColor($status) {
    switch ($status) {
        case 'paid': return 'bg-green-100 text-green-800 border-green-200';
        case 'unpaid': // Assuming unpaid maps to sent/draft for simpler logic if needed, or stick to DB enums
        case 'sent': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'draft': return 'bg-gray-100 text-gray-800 border-gray-200';
        case 'overdue': return 'bg-red-100 text-red-800 border-red-200';
        case 'cancelled': return 'bg-red-50 text-red-500 border-red-100';
        case 'partial': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        default: return 'bg-gray-100 text-gray-800';
    }
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ... (head content remains same, ensure to keep it) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Invoice - <?php echo $company['company_name']; ?></title>
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
                    <a href="../../index.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Dashboard</a>
                    <a href="../products/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Produk</a>
                    <a href="../customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium transition duration-200">Customer</a>
                    <a href="list.php" class="text-blue-600 font-semibold transition duration-200">Invoice</a>
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
                    <a href="../../index.php" class="text-gray-500 hover:text-blue-600 font-medium">Dashboard</a>
                    <a href="../products/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Produk</a>
                    <a href="../customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Customer</a>
                    <a href="list.php" class="text-blue-600 font-semibold">Invoice</a>
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
                <h1 class="text-2xl font-bold text-gray-900">Daftar Invoice</h1>
                <p class="text-gray-500 mt-1">Kelola semua tagihan dan pembayaran customer.</p>
            </div>
            <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-200 transition duration-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Invoice Baru
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" 
                           placeholder="Cari nomor invoice atau nama customer...">
                </div>
                
                <div class="w-full md:w-48">
                    <select name="sort" class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-gray-50">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Terbaru (Waktu Buat)</option>
                        <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Terlama</option>
                        <option value="alphabet_asc" <?php echo $sort == 'alphabet_asc' ? 'selected' : ''; ?>>Abjad (A-Z)</option>
                        <option value="alphabet_desc" <?php echo $sort == 'alphabet_desc' ? 'selected' : ''; ?>>Abjad (Z-A)</option>
                    </select>
                </div>

                <div class="w-full md:w-48">
                    <select name="status" class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-gray-50">
                        <option value="">Semua Status</option>
                        <option value="draft" <?php echo $statusFilter == 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="sent" <?php echo $statusFilter == 'sent' ? 'selected' : ''; ?>>Terkirim</option>
                        <option value="paid" <?php echo $statusFilter == 'paid' ? 'selected' : ''; ?>>Lunas</option>
                    </select>
                </div>

                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 rounded-lg font-medium transition duration-200">
                    Filter
                </button>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (count($invoices) > 0): ?>
                            <?php foreach ($invoices as $inv): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-blue-600 hover:underline cursor-pointer">
                                        <a href="print.php?id=<?php echo $inv['id']; ?>"><?php echo htmlspecialchars($inv['invoice_number']); ?></a>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($inv['customer_name']); ?></div>
                                    <?php if (!empty($inv['customer_company'])): ?>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($inv['customer_company']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo date('d M Y', strtotime($inv['invoice_date'])); ?></div>
                                    <div class="text-xs text-gray-500">Due: <?php echo date('d M Y', strtotime($inv['due_date'])); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo getStatusColor($inv['status']); ?>">
                                        <?php echo getStatusLabel($inv['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    Rp <?php echo number_format($inv['total'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="print.php?id=<?php echo $inv['id']; ?>" class="text-gray-500 hover:text-gray-700 p-1 hover:bg-gray-100 rounded transition" title="Print/View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                        <!-- Edit button potentially linking to create.php?id=... if implemented later -->
                                        <button onclick="deleteInvoice(<?php echo $inv['id']; ?>)" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <p class="mb-2">Belum ada invoice yang dibuat.</p>
                                    <a href="create.php" class="text-blue-600 font-medium hover:underline">Buat Invoice Sekarang</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">Menampilkan <?php echo count($invoices); ?> invoice</span>
            </div>
        </div>
        
    </main>

    <!-- Delete Confirmation Modal -->
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Invoice</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus invoice ini? Data pembayaran terkait juga akan terhapus.</p>
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
                            <div class="mt-2"><p class="text-sm text-gray-500">Invoice berhasil dihapus.</p></div>
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
        function deleteInvoice(id) {
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
        if (urlParams.has('deleted')) {
            document.getElementById('successModal').classList.remove('hidden');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }
    </script>
</body>
</html>
