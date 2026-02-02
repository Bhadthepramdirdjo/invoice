<?php
require_once '../../config/database.php';
$db = getDB();
$company = getCompanySettings($db);

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invoice ID is required");
}

// Fetch Invoice
$stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die("Invoice not found");
}

// Fetch Items
$stmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

// Override company info if needed (based on user request)
// We use the defaults requested: HomeBake33
$fromCompany = "HomeBake33";
$fromAddress = "Jl. Batukali No.33 Bojong Raya";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo $invoice['invoice_number']; ?></title>
    
    <!-- Normalize & Tailwind (for easy styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { 
            background: #525659; 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .page {
            background: white;
            width: 21cm;
            min-height: 29.7cm;
            display: block;
            margin: 0 auto;
            margin-bottom: 0.5cm;
            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
            padding: 2cm;
            position: relative;
        }
        @media print {
            body { background: none; margin: 0; }
            .page { 
                width: 100%; 
                height: auto; 
                margin: 0; 
                box-shadow: none; 
                padding: 0;
            }
            .no-print { display: none !important; }
        }
        
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
        .invoice-title { font-size: 2rem; font-weight: bold; color: #333; }
        .invoice-meta { text-align: right; }
        
        .bill-info { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th { text-align: left; border-bottom: 2px solid #ddd; padding: 0.5rem; font-weight: 600; }
        td { border-bottom: 1px solid #eee; padding: 0.5rem; }
        .text-right { text-align: right; }
        .amount-col { width: 150px; }
        
        .summary { display: flex; justify-content: flex-end; }
        .summary-box { width: 300px; }
        .summary-row { display: flex; justify-content: space-between; padding: 0.5rem 0; }
        .summary-row.total { font-weight: bold; font-size: 1.2rem; border-top: 2px solid #333; }
        
        .notes-section { margin-top: 3rem; color: #666; font-size: 0.9rem; }
        
        /* Floating Action Buttons */
        .fab-container {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            gap: 1rem;
            z-index: 100;
        }
        .btn {
            background: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 999px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s, background 0.2s;
        }
        .btn:hover { transform: translateY(-2px); background: #2563eb; }
        .btn-green { background: #10b981; }
        .btn-green:hover { background: #059669; }
        .btn-gray { background: #4b5563; }
        .btn-gray:hover { background: #374151; }
    </style>
</head>
<body>

    <!-- Printable Area -->
    <div class="page">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">INVOICE</h1>
                <p class="text-gray-500 font-medium"><?php echo $invoice['invoice_number']; ?></p>
            </div>
            <div class="text-right">
                <p class="font-bold text-xl text-gray-800"><?php echo $fromCompany; ?></p>
                <p class="text-gray-600 w-64 ml-auto whitespace-pre-line"><?php echo $fromAddress; ?></p>
            </div>
        </div>
        
        <hr class="border-gray-300 my-6">
        
        <!-- Billing Info -->
        <div class="bill-info">
            <div>
                <h3 class="text-gray-500 font-semibold mb-1 text-sm uppercase tracking-wide">Bill To</h3>
                <p class="font-bold text-gray-800 text-lg"><?php echo !empty($invoice['customer_company']) ? $invoice['customer_company'] : $invoice['customer_name']; ?></p>
                <?php if (!empty($invoice['customer_address'])): ?>
                    <p class="text-gray-600 whitespace-pre-line mt-1"><?php echo $invoice['customer_address']; ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <div class="mb-2">
                    <span class="text-gray-500 font-semibold text-sm uppercase mr-4">Invoice Date</span>
                    <span class="font-medium text-gray-800"><?php echo date('d M Y', strtotime($invoice['invoice_date'])); ?></span>
                </div>
                <!-- Calculate Due Date based on created_at + 30 days if not set, or just use +30 days logic -->
                <div class="mb-2">
                    <span class="text-gray-500 font-semibold text-sm uppercase mr-4">Due Date</span>
                    <span class="font-medium text-gray-800"><?php echo date('d M Y', strtotime($invoice['due_date'])); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="w-12">#</th>
                    <th>Description</th>
                    <th class="text-center w-24">Qty</th>
                    <th class="text-right amount-col">Price</th>
                    <th class="text-right amount-col">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($items as $item): 
                ?>
                <tr>
                    <td class="text-gray-500"><?php echo $i++; ?></td>
                    <td>
                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></div>
                    </td>
                    <td class="text-center text-gray-600"><?php echo floatval($item['quantity']); ?></td>
                    <td class="text-right text-gray-600"><?php echo 'Rp ' . number_format($item['unit_price'], 0, ',', '.'); ?></td>
                    <td class="text-right font-medium text-gray-900"><?php echo 'Rp ' . number_format($item['subtotal'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-box">
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo 'Rp ' . number_format($invoice['total'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Footer Notes -->
        <div class="notes-section">
            <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-2">Rekening Pembayaran:</h4>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-blue-800">BCA</span>
                    <span class="font-mono text-lg font-medium text-gray-800">139 2828 936</span>
                </div>
                <div class="text-gray-600 mt-1">a.n. Dian Rosdiana</div>
            </div>

            <?php if (!empty($invoice['notes'])): ?>
                <div class="mb-4">
                    <h4 class="font-bold text-gray-800 mb-1">Notes:</h4>
                    <p class="whitespace-pre-line"><?php echo htmlspecialchars($invoice['notes']); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($invoice['terms'])): ?>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">Terms:</h4>
                    <p class="whitespace-pre-line"><?php echo htmlspecialchars($invoice['terms']); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Action Buttons -->
    <div class="fab-container no-print">
        <a href="list.php" class="btn btn-gray">
            ← Kembali
        </a>
        <button onclick="window.print()" class="btn btn-green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download PDF
        </button>
    </div>

</body>
</html>
