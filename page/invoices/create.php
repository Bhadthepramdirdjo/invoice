<?php
$pageTitle = 'Buat Invoice';
$currentPage = 'invoices';
$baseUrl = '../../';

require_once '../../config/database.php';
$db = getDB();
$company = getCompanySettings($db);

// Get customers
$customers = $db->query("SELECT id, name, email, phone, address FROM customers WHERE is_active = 1 ORDER BY name")->fetchAll();

// Get products
$products = $db->query("SELECT id, name, price, stock FROM products WHERE is_active = 1 ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo $company['company_name']; ?></title>
    <title><?php echo $pageTitle; ?> - <?php echo $company['company_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
        .invoice {
            padding: 40px;
            border: 1px solid #efefef;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            background: white;
            border-radius: 0.5rem;
        }
        @media (max-width: 640px) {
            .invoice { padding: 20px; }
            
            /* Responsive Table for Invoice Items */
            #itemsTable thead { display: none; }
            #itemsTable, #itemsTable tbody, #itemsTable tr, #itemsTable td {
                display: block;
                width: 100%;
            }
            #itemsTable tr {
                margin-bottom: 1rem;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                padding: 1rem;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            #itemsTable td {
                padding: 0.5rem 0;
                text-align: left !important;
                border: none;
                position: relative;
            }
            #itemsTable td::before {
                content: attr(data-label);
                font-weight: 600;
                display: block;
                font-size: 0.75rem;
                color: #6b7280;
                margin-bottom: 0.25rem;
            }
            #itemsTable td:last-child {
                border-top: 1px solid #e5e7eb;
                margin-top: 0.5rem;
                padding-top: 0.75rem;
                text-align: right !important;
            }
            #itemsTable td:last-child::before {
                display: none;
            }
        }
        /* Custom form styles that don't conflict with Tailwind */
        .form-control-sm {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .form-control-sm:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity));
            border-color: #3b82f6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.375rem;
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-duration: 200ms;
        }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; padding: 0.75rem; font-size: 0.875rem; font-weight: 600; color: #4b5563; border-bottom: 2px solid #e5e7eb; }
        table td { padding: 0.75rem; vertical-align: top; border-bottom: 1px solid #e5e7eb; }
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
                    <a href="../customers/list.php" class="text-gray-600 hover:text-blue-600 font-medium transition duration-200">Customer</a>
                    <a href="list.php" class="text-blue-600 font-semibold transition duration-200">Invoice</a>
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
                    <a href="../customers/list.php" class="text-gray-500 hover:text-blue-600 font-medium">Customer</a>
                    <a href="list.php" class="text-blue-600 font-medium">Invoice</a>
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
    <div class="container mx-auto px-6 pt-24 pb-12">
        <div class="max-w-5xl mx-auto">
            
            <div class="invoice">
                <h2 class="text-2xl font-bold mb-4 text-gray-900">Invoice</h2>
                <hr class="border-gray-200 my-6" />
                
                <form id="invoiceForm" method="POST" action="save.php">
                    
                    <!-- From/To and Date Section -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8">
                        <!-- Left Side -->
                        <div class="col-span-1 md:col-span-5">
                            <input class="form-control-sm mb-2" type="text" name="from_company" id="from_company" 
                                   placeholder="From company" value="HomeBake33" />
                            <textarea class="form-control-sm mb-2" name="from_address" id="from_address" rows="2" 
                                      placeholder="From address">Jl. Batukali No.33 Bojong Raya</textarea>
                            
                            <!-- Toggle for customer input mode -->
                            <div class="mb-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="manualCustomerToggle" class="mr-2 accent-blue-600" onchange="toggleCustomerInput()">
                                    <span class="text-xs text-gray-600">Tulis manual (tidak dari database)</span>
                                </label>
                            </div>
                            
                            <!-- Customer from Database -->
                            <div id="customerFromDB">
                                <select class="form-control-sm mb-2" name="customer_id" id="customer_id">
                                    <option value="">To company (pilih dari database)</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                                data-address="<?php echo htmlspecialchars($customer['address']); ?>">
                                            <?php echo htmlspecialchars($customer['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Manual Customer Input (Hidden by default) -->
                            <div id="customerManual" style="display: none;">
                                <input class="form-control-sm mb-2" type="text" name="to_company_manual" id="to_company_manual" 
                                       placeholder="To company" />
                            </div>
                            
                            <textarea class="form-control-sm" name="to_address" id="to_address" rows="2" 
                                      placeholder="To address"></textarea>
                        </div>
                        
                        <div class="col-span-1 md:col-span-7">
                            <div class="grid grid-cols-12 gap-2 mb-2">
                                <div class="col-span-4 font-semibold text-gray-700">Date</div>
                                <div class="col-span-8">
                                    <input class="form-control-sm" type="date" name="invoice_date" id="invoice_date" 
                                           value="<?php echo date('Y-m-d'); ?>" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Items Table -->
                    <div class="mb-6 overflow-x-auto">
                        <table id="itemsTable" class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left w-2/5">Item</th>
                                    <th class="text-left w-1/6">Quantity</th>
                                    <th class="text-left w-1/5">Price</th>
                                    <th class="text-left w-1/5">Amount</th>
                                    <th class="w-auto"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="pt-4">
                                        <button type="button" class="btn bg-green-600 hover:bg-green-700 text-white w-full md:w-auto" id="addItemBtn">
                                            <span class="mr-2 text-lg">+</span> Tambah Item
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <div class="grid grid-cols-12 gap-2 mt-6 border-t pt-4">
                            <div class="col-span-6 md:col-span-10 font-bold text-right text-lg text-gray-800">Total</div>
                            <div class="col-span-6 md:col-span-2 text-right font-bold text-lg text-blue-600" id="summaryTotal">Rp 0</div>
                        </div>
                        
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-4">
                        <h5 class="font-semibold mb-2 text-gray-700">Notes</h5>
                        <textarea class="form-control-sm" name="notes" rows="3"></textarea>
                    </div>
                    
                    <!-- Terms -->
                    <div class="mb-6">
                        <h5 class="font-semibold mb-2 text-gray-700">Terms</h5>
                        <textarea class="form-control-sm" name="terms" rows="3"></textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="status" value="draft" class="btn btn-secondary bg-gray-600 hover:bg-gray-700 text-white flex-1 md:flex-none">Simpan Draft</button>
                        <button type="submit" name="status" value="sent" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white flex-1 md:flex-none">Simpan & Cetak</button>
                        <button type="button" class="btn btn-secondary bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 flex-1 md:flex-none" onclick="window.location.href='list.php'">Batal</button>
                    </div>
                    
                    <input type="hidden" name="subtotal" id="hiddenSubtotal" value="0">
                    <input type="hidden" name="total" id="hiddenTotal" value="0">
                    
                </form>
            </div>
            
        </div>
    </div>
    
    <script>
        // Products data
        const productsData = <?php echo json_encode($products); ?>;
        
        // Customer change - auto fill address
        const custSelect = document.getElementById('customer_id');
        if(custSelect) {
            custSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const address = selected.getAttribute('data-address') || '';
                document.getElementById('to_address').value = address;
            });
        }
        
        // Toggle customer input mode
        function toggleCustomerInput() {
            const isManual = document.getElementById('manualCustomerToggle').checked;
            const dbDiv = document.getElementById('customerFromDB');
            const manualDiv = document.getElementById('customerManual');
            
            if (isManual) {
                // Show manual input, hide database dropdown
                dbDiv.style.display = 'none';
                manualDiv.style.display = 'block';
                document.getElementById('customer_id').removeAttribute('required');
                document.getElementById('customer_id').value = '';
            } else {
                // Show database dropdown, hide manual input
                dbDiv.style.display = 'block';
                manualDiv.style.display = 'none';
                document.getElementById('to_company_manual').value = '';
            }
        }
        
        let itemCounter = 0;
        // Add item button
        const addBtn = document.getElementById('addItemBtn');
        if(addBtn) {
            addBtn.addEventListener('click', addBlankProduct);
        }
        
        function addBlankProduct() {
            itemCounter++;
            const tbody = document.getElementById('itemsBody');
            
            const tr = document.createElement('tr');
            tr.id = 'item-' + itemCounter;
            
            tr.innerHTML = `
                <td data-label="Item / Product">
                    <select class="form-control-sm" name="items[${itemCounter}][product_id]" 
                            id="product${itemCounter}" required onchange="selectProduct(${itemCounter})">
                        <option value="">Select product...</option>
                        ${productsData.map(p => `<option value="${p.id}" data-price="${p.price}" data-name="${p.name}">${p.name}</option>`).join('')}
                    </select>
                </td>
                <td data-label="Quantity">
                    <input class="form-control-sm" type="number" name="items[${itemCounter}][quantity]" 
                           id="qty${itemCounter}" value="1" min="1" required onchange="productPriceChange(${itemCounter})" />
                </td>
                <td data-label="Price">
                    <input class="form-control-sm" type="number" name="items[${itemCounter}][price]" 
                           id="price${itemCounter}" value="0" min="0" required onchange="productPriceChange(${itemCounter})" />
                </td>
                <td class="text-right" data-label="Amount">
                    <span id="amount${itemCounter}" class="font-medium text-gray-900">Rp 0</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn bg-red-600 hover:bg-red-700 text-white w-full md:w-auto shadow-sm" onclick="removeProduct(${itemCounter})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="hidden md:inline">Hapus</span>
                        <span class="md:hidden">Hapus Item</span>
                    </button>
                </td>
            `;
            
            tbody.appendChild(tr);
        }
        
        function selectProduct(itemId) {
            const select = document.getElementById('product' + itemId);
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            
            document.getElementById('price' + itemId).value = price;
            productPriceChange(itemId);
        }
        
        function productPriceChange(itemId) {
            const qty = parseFloat(document.getElementById('qty' + itemId).value) || 0;
            const price = parseFloat(document.getElementById('price' + itemId).value) || 0;
            const amount = qty * price;
            
            document.getElementById('amount' + itemId).textContent = formatRupiah(amount);
            
            calculateTotal();
        }
        
        function removeProduct(itemId) {
            const row = document.getElementById('item-' + itemId);
            if (row) {
                row.remove();
                calculateTotal();
            }
        }
        
        function calculateTotal() {
            let total = 0;
            
            for (let i = 1; i <= itemCounter; i++) {
                const qtyEl = document.getElementById('qty' + i);
                const priceEl = document.getElementById('price' + i);
                
                if (qtyEl && priceEl) {
                    const qty = parseFloat(qtyEl.value) || 0;
                    const price = parseFloat(priceEl.value) || 0;
                    total += qty * price;
                }
            }
            
            document.getElementById('summaryTotal').textContent = formatRupiah(total);
            document.getElementById('hiddenTotal').value = total.toFixed(2);
        }
        
        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
        }

        // Add first item on load
        window.addEventListener('load', function() {
            addBlankProduct();
        });
    </script>
    
</body>
</html>
