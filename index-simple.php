<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$baseUrl = '';

require_once 'includes/header-simple.php';
?>

<h1 class="text-3xl font-bold mb-4">Dashboard - Invoice App</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <a href="page/invoices/create.php" class="bg-white p-6 rounded-lg shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">Buat Invoice</h3>
        <p class="text-gray-600">Buat invoice baru</p>
    </a>
    
    <a href="page/invoices/list.php" class="bg-white p-6 rounded-lg shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">Daftar Invoice</h3>
        <p class="text-gray-600">Lihat semua invoice</p>
    </a>
    
    <a href="page/products/list.php" class="bg-white p-6 rounded-lg shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">Produk</h3>
        <p class="text-gray-600">Kelola produk</p>
    </a>
    
    <a href="page/customers/list.php" class="bg-white p-6 rounded-lg shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">Customer</h3>
        <p class="text-gray-600">Kelola customer</p>
    </a>
    
</div>

<div class="bg-white p-6 rounded-lg shadow mt-6">
    <h2 class="text-xl font-bold mb-4">Invoice Terbaru</h2>
    
    <?php
    try {
        $stmt = $db->query("SELECT * FROM view_invoice_summary ORDER BY invoice_date DESC LIMIT 5");
        $invoices = $stmt->fetchAll();
        
        if (empty($invoices)) {
            echo '<p class="text-gray-500">Belum ada invoice</p>';
        } else {
            echo '<table class="w-full">';
            echo '<thead><tr class="border-b">';
            echo '<th class="text-left p-2">No. Invoice</th>';
            echo '<th class="text-left p-2">Customer</th>';
            echo '<th class="text-left p-2">Total</th>';
            echo '<th class="text-left p-2">Status</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            foreach ($invoices as $inv) {
                echo '<tr class="border-b">';
                echo '<td class="p-2">' . htmlspecialchars($inv['invoice_number']) . '</td>';
                echo '<td class="p-2">' . htmlspecialchars($inv['customer_name']) . '</td>';
                echo '<td class="p-2">Rp ' . number_format($inv['total'], 0, ',', '.') . '</td>';
                echo '<td class="p-2">' . htmlspecialchars($inv['status']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
    } catch (Exception $e) {
        echo '<p class="text-red-500">Error: ' . $e->getMessage() . '</p>';
    }
    ?>
</div>

<?php require_once 'includes/footer.php'; ?>
