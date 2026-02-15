<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php');
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();

    // 1. Prepare Invoice Data
    $invoiceDate = $_POST['invoice_date'];
    $notes = $_POST['notes'] ?? '';
    $terms = $_POST['terms'] ?? '';
    
    // Customer Info
    $customerId = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
    $customerName = '';
    $customerCompany = '';
    $customerAddress = $_POST['to_address'] ?? '';
    
    if ($customerId) {
        // Fetch from DB
        $stmt = $db->prepare("SELECT name, company FROM customers WHERE id = ?");
        $stmt->execute([$customerId]);
        $cust = $stmt->fetch();
        if ($cust) {
            $customerName = $cust['name'];
            $customerCompany = $cust['company'];
        }
    } else {
        // Manual Input
        $customerCompany = $_POST['to_company_manual'] ?? '';
        $customerName = $customerCompany; // Use company name as name for manual
    }

    // 2. Generate Invoice Number (PHP Version - No Stored Procedure)
    // Get settings first
    $stmtSettings = $db->query("SELECT invoice_prefix, invoice_number_format, invoice_next_number FROM company_settings LIMIT 1");
    $settings = $stmtSettings->fetch();
    
    $prefix = $settings['invoice_prefix'] ?? 'INV';
    $nextNum = $settings['invoice_next_number'] ?? 1;
    $format = $settings['invoice_number_format'] ?? '{PREFIX}-{YEAR}-{NUMBER}';
    $year = date('Y');
    
    // Format: replace placeholders
    $invoiceNumber = str_replace('{PREFIX}', $prefix, $format);
    $invoiceNumber = str_replace('{YEAR}', $year, $invoiceNumber);
    $invoiceNumber = str_replace('{NUMBER}', str_pad($nextNum, 4, '0', STR_PAD_LEFT), $invoiceNumber);
    
    // Update next number immediately
    $db->query("UPDATE company_settings SET invoice_next_number = invoice_next_number + 1");

    // Capture status from form button (draft or sent)
    $status = $_POST['status'] ?? 'draft';

    // Get new fields
    $shippingFee = floatval($_POST['shipping_fee'] ?? 0);
    $packagingFee = floatval($_POST['packaging_fee'] ?? 0);
    
    // 3. Insert Invoice
    // We set initial totals to 0, will update after processing items
    // Added shipping_fee only
    $stmt = $db->prepare("INSERT INTO invoices (
        invoice_number, customer_id, customer_name, customer_company, customer_address, 
        invoice_date, due_date, notes, terms, status, tax_rate, subtotal, tax_amount, total,
        shipping_fee, packaging_fee
    ) VALUES (
        ?, ?, ?, ?, ?, 
        ?, DATE_ADD(?, INTERVAL 30 DAY), ?, ?, ?, 0, 0, 0, 0,
        ?, ?
    )");
    
    $stmt->execute([
        $invoiceNumber, $customerId, $customerName, $customerCompany, $customerAddress,
        $invoiceDate, $invoiceDate, $notes, $terms, $status,
        $shippingFee, $packagingFee
    ]);
    
    $invoiceId = $db->lastInsertId();

    // 4. Insert Items and Calculate Totals
    $grandSubtotal = 0;
    
    if (isset($_POST['items']) && is_array($_POST['items'])) {
        $stmtItem = $db->prepare("INSERT INTO invoice_items (
            invoice_id, product_id, product_name, description, quantity, unit_price, subtotal
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?
        )");

        foreach ($_POST['items'] as $item) {
            // Skip empty items
            if (empty($item['quantity']) || empty($item['price'])) {
                continue;
            }

            $productId = !empty($item['product_id']) ? $item['product_id'] : null;
            $quantity = floatval($item['quantity']);
            $price = floatval($item['price']);
            $subtotal = $quantity * $price;
            
            // Add to running total
            $grandSubtotal += $subtotal;
            
            // Get product name if product_id exists
            $productName = '';
            if ($productId) {
                // Fetch name separately or pass from form hidden? 
                // Form doesn't pass name, only ID.
                $pStmt = $db->prepare("SELECT name FROM products WHERE id = ?");
                $pStmt->execute([$productId]);
                $prod = $pStmt->fetch(); // Corrected variable name
                $productName = $prod ? $prod['name'] : 'Item';
            } else {
                $productName = 'Item'; 
            }
            
            // Description is implicitly the product name or we can leave it empty
            $description = $productName;

            $stmtItem->execute([
                $invoiceId, $productId, $productName, $description, $quantity, $price, $subtotal
            ]);
        }
    }
    
    // 5. Update Invoice Totals (Manual Calculation because we removed Triggers)
    $taxRate = 0; 
    $taxAmount = $grandSubtotal * ($taxRate / 100);
    
    // Grand Total = Items + Tax + Packaging + Shipping
    $finalTotal = $grandSubtotal + $taxAmount + $packagingFee + $shippingFee;
    
    $updateStmt = $db->prepare("UPDATE invoices SET subtotal = ?, tax_amount = ?, total = ? WHERE id = ?");
    $updateStmt->execute([$grandSubtotal, $taxAmount, $finalTotal, $invoiceId]);

    $db->commit();
    
    // Redirect directly to Print page
    header("Location: print.php?id=" . $invoiceId);
    exit;

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    // Log error and redirect back with error
    error_log($e->getMessage());
    echo "Error: " . $e->getMessage();
    // header('Location: create.php?error=save_failed');
    exit;
}
