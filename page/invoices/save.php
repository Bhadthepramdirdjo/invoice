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

    // 3. Insert Invoice
    // We set initial totals to 0, will update after processing items
    $stmt = $db->prepare("INSERT INTO invoices (
        invoice_number, customer_id, customer_name, customer_company, customer_address, 
        invoice_date, due_date, notes, terms, status, tax_rate, subtotal, tax_amount, total
    ) VALUES (
        ?, ?, ?, ?, ?, 
        ?, DATE_ADD(?, INTERVAL 30 DAY), ?, ?, ?, 0, 0, 0, 0
    )");
    
    $stmt->execute([
        $invoiceNumber, $customerId, $customerName, $customerCompany, $customerAddress,
        $invoiceDate, $invoiceDate, $notes, $terms, $status
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
                $pStmt = $db->prepare("SELECT name FROM products WHERE id = ?");
                $pStmt->execute([$productId]);
                $prod = $pStmt->fetch();
                $productName = $prod ? $prod['name'] : 'Unknown Product';
            } else {
                $productName = $item['description'] ?? 'Item'; 
            }
            
            // Description is implicitly the product name or we can leave it empty
            $description = $productName;

            $stmtItem->execute([
                $invoiceId, $productId, $productName, $description, $quantity, $price, $subtotal
            ]);
        }
    }
    
    // 5. Update Invoice Totals (Manual Calculation because we removed Triggers)
    // Get Tax Rate (could be from settings or input, assuming 0 or default for now)
    // Ideally we fetch default tax rate from settings again or use a hardcoded value/input
    // Let's rely on what was inserted or default.
    // For simplicity, let's re-fetch default tax rate or just use 0 if not set in UI.
    // Modify: Add tax update if needed. Assuming 0 for now as per previous logic.
    $taxRate = 0; // Or fetch from company_settings if you want auto-tax
    $taxAmount = $grandSubtotal * ($taxRate / 100);
    $finalTotal = $grandSubtotal + $taxAmount;
    
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
