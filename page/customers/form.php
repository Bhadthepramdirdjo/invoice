<?php
$pageTitle = 'Form Customer';
require_once '../../config/database.php';
$db = getDB();
$company = getCompanySettings($db);

$id = $_GET['id'] ?? null;
$customer = [
    'code' => '',
    'name' => '',
    'company' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'province' => '',
    'postal_code' => '',
    'tax_id' => '',
    'notes' => ''
];
$isEdit = false;

if ($id) {
    $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    if ($data) {
        $customer = $data;
        $isEdit = true;
    }
} else {
    // Generate Code: CST-XXXXX
    $customer['code'] = 'CST-' . strtoupper(substr(uniqid(), -5));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Customer' : 'Tambah Customer'; ?> - <?php echo $company['company_name']; ?></title>
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
    <main class="container mx-auto px-4 md:px-6 pt-24 pb-12">
        <div class="max-w-3xl mx-auto">
            
            <form action="save_customer.php" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">

                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo $isEdit ? 'Ubah Customer' : 'Tambah Customer'; ?></h1>
                    <div class="self-start md:self-auto px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-100">
                        <?php echo htmlspecialchars($customer['code']); ?>
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($customer['code']); ?>">
                    </div>
                </div>
                <hr class="border-gray-100 mb-6">
                
                <!-- Main Grid: 1 col mobile, 2 col desktop -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6 mb-6">
                    
                    <!-- Nama & Perusahaan -->
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap / PIC <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="<?php echo htmlspecialchars($customer['name']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Perusahaan (Opsional)</label>
                        <input type="text" name="company" value="<?php echo htmlspecialchars($customer['company']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="Contoh: PT. Maju Jaya">
                    </div>

                    <!-- Kontak -->
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="email@example.com">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon / WA</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="08123456789">
                    </div>
                    
                    <!-- Alamat Lengkap (Full Width) -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="Nama Jalan, No. Rumah, RT/RW..."><?php echo htmlspecialchars($customer['address']); ?></textarea>
                    </div>

                    <!-- Detail Lokasi -->
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kota / Kabupaten</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="Contoh: Jakarta Selatan">
                    </div>
                    
                    <!-- Provinsi & Kode Pos (Nested Grid) -->
                    <div class="col-span-1 md:col-span-1">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi</label>
                                <input type="text" name="province" value="<?php echo htmlspecialchars($customer['province']); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="DKI Jakarta">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos</label>
                                <input type="text" name="postal_code" value="<?php echo htmlspecialchars($customer['postal_code']); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="12xxx">
                            </div>
                        </div>
                    </div>

                    <!-- Tambahan -->
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">NPWP (Opsional)</label>
                        <input type="text" name="tax_id" value="<?php echo htmlspecialchars($customer['tax_id']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="Nomor NPWP">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Internal</label>
                        <textarea name="notes" rows="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400" placeholder="Catatan khusus..."><?php echo htmlspecialchars($customer['notes']); ?></textarea>
                    </div>
                </div>

                <div class="flex flex-col-reverse md:flex-row justify-end gap-4 pt-6 border-t border-gray-100">
                    <a href="list.php" class="text-center px-6 py-2.5 bg-red-50 text-red-600 font-medium rounded-lg hover:bg-red-100 transition transform hover:-translate-y-0.5">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition transform hover:-translate-y-0.5">
                        Simpan Customer
                    </button>
                </div>
            </form>
            
        </div>
    </main>

</body>
</html>
