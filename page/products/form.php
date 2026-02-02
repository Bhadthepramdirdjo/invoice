<?php
$pageTitle = 'Kelola Produk';
$currentPage = 'products';
$baseUrl = '../../';

require_once '../../config/database.php';
$db = getDB();

$id = $_GET['id'] ?? null;
$product = [
    'code' => '',
    'name' => '',
    'price' => '',
    'stock' => '',
    'stock' => '',
    'unit' => 'pcs',
    'items_per_unit' => 1,
    'description' => ''
];
$isEdit = false;

// If Edit Mode
if ($id) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $items = $stmt->fetch();
    if ($items) {
        $product = $items;
        $isEdit = true;
    }
}

// Generate Code if Empty (New Product)
if (!$isEdit) {
    // Simple logic: PROD + Timestamp (or random)
    $product['code'] = 'PROD-' . strtoupper(substr(uniqid(), -5));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Produk' : 'Tambah Produk'; ?> - Invoice App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body class="text-gray-800">

    <!-- Navbar (Simplified) -->
    <nav class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="list.php" class="flex items-center text-gray-500 hover:text-gray-900 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                </div>
                <div class="font-bold text-gray-800">
                    <?php echo $isEdit ? 'Edit Produk' : 'Tambah Produk Baru'; ?>
                </div>
                <div class="w-20"></div> <!-- Spacer -->
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <div class="max-w-2xl mx-auto">
            
            <form action="save_product.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Kode Produk -->
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Produk</label>
                        <input type="text" name="code" value="<?php echo htmlspecialchars($product['code']); ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition uppercase font-mono bg-gray-50">
                    </div>
                    
                    <!-- Harga -->
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Harga (Rp)</label>
                        <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    
                    <!-- Nama Produk -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required
                               placeholder="Contoh: Jasa Desain Website"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <!-- Stok & Satuan -->
                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Stok Saat Ini</label>
                        <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <!-- Logic Parsing Unit -->
                    <?php
                    $currentUnit = $product['unit'];
                    $selectedType = 'pcs'; // default
                    $dimL = '';
                    $dimW = '';

                    // Cek apakah format dimensi "10 X 20 cm"
                    if (strpos($currentUnit, 'cm') !== false && strpos($currentUnit, 'X') !== false) {
                        $selectedType = 'cm';
                        // Ambil hanya angkanya
                        $clean = str_ireplace(' cm', '', $currentUnit); // "10 X 20"
                        $parts = explode(' X ', $clean);
                        $dimL = $parts[0] ?? '';
                        $dimW = $parts[1] ?? '';
                    } elseif (in_array(strtolower($currentUnit), ['pcs', 'box', 'ml'])) {
                        $selectedType = strtolower($currentUnit);
                    }
                    ?>

                    <div class="col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan (Unit)</label>
                        
                        <!-- Input Hidden yang akan dikirim ke Server -->
                        <input type="hidden" name="unit" id="finalUnit" value="<?php echo htmlspecialchars($product['unit']); ?>">
                        
                        <!-- Dropdown Selector -->
                        <select id="unitSelector" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white" onchange="updateUnitLogic()">
                            <option value="pcs" <?php echo $selectedType == 'pcs' ? 'selected' : ''; ?>>Pcs</option>
                            <option value="box" <?php echo $selectedType == 'box' ? 'selected' : ''; ?>>Box</option>
                            <option value="ml" <?php echo $selectedType == 'ml' ? 'selected' : ''; ?>>Ml</option>
                            <option value="cm" <?php echo $selectedType == 'cm' ? 'selected' : ''; ?>>Custom Ukuran (cm)</option>
                        </select>

                        <!-- Input Dimensi (Hanya muncul jika pilih cm) -->
                        <div id="dimContainer" class="mt-3 <?php echo $selectedType == 'cm' ? '' : 'hidden'; ?>">
                            <div class="flex items-center gap-2">
                                <input type="number" id="dimL" placeholder="P" value="<?php echo htmlspecialchars($dimL); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center" oninput="updateUnitLogic()">
                                <span class="text-gray-500 font-bold">X</span>
                                <input type="number" id="dimW" placeholder="L" value="<?php echo htmlspecialchars($dimW); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center" oninput="updateUnitLogic()">
                                <span class="text-gray-500 font-bold">cm</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: Panjang X Lebar cm</p>
                        </div>
                        
                        <!-- Input Isi per Box (Hanya muncul jika pilih box) -->
                        <div id="boxContainer" class="mt-3 <?php echo $selectedType == 'box' ? '' : 'hidden'; ?>">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Isi per Box (pcs)</label>
                            <input type="number" name="items_per_unit" id="itemsPerUnit" value="<?php echo htmlspecialchars($product['items_per_unit'] ?? 1); ?>" min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <p class="text-xs text-gray-500 mt-1">Contoh: 1 Box = 24 Pcs</p>
                        </div>
                    </div>

                    <script>
                    function updateUnitLogic() {
                        const selector = document.getElementById('unitSelector');
                        const dimContainer = document.getElementById('dimContainer');
                        const boxContainer = document.getElementById('boxContainer');
                        const finalUnit = document.getElementById('finalUnit');
                        const dimL = document.getElementById('dimL');
                        const dimW = document.getElementById('dimW');
                        
                        // Default hidden
                        dimContainer.classList.add('hidden');
                        boxContainer.classList.add('hidden');
                        
                        if (selector.value === 'cm') {
                            // Tampilkan input dimensi
                            dimContainer.classList.remove('hidden');
                            
                            // Format: "30 X 50 cm"
                            const p = dimL.value || 0;
                            const l = dimW.value || 0;
                            finalUnit.value = `${p} X ${l} cm`;
                        
                        } else if (selector.value === 'box') {
                            // Tampilkan input isi per box
                            boxContainer.classList.remove('hidden');
                            finalUnit.value = 'box';
                            
                        } else {
                            // Isi value sesuai dropdown (pcs/ml)
                            finalUnit.value = selector.value;
                        }
                    }
                    </script>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <!-- Image Upload -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Produk</label>
                        <div class="flex items-center gap-4">
                            <div id="imagePreview" class="w-24 h-24 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="../../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="image" id="imageInput" accept="image/*"
                                       class="block w-full text-sm text-slate-500
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded-full file:border-0
                                       file:text-sm file:font-semibold
                                       file:bg-blue-50 file:text-blue-700
                                       file:cursor-pointer hover:file:bg-blue-100 mb-2">
                                <p class="text-xs text-gray-500">Format: JPG, PNG, GIF. Max: 2MB.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="list.php" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition transform hover:-translate-y-0.5">
                        Simpan Produk
                    </button>
                </div>

            </form>
            
        </div>
    </main>

    <script>
        // Image Preview
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = 
                        '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
