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

    // 2. Generate Invoice Number
    $stmt = $db->query("CALL sp_generate_invoice_number()");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $invoiceNumber = $result['invoice_number'];
    $stmt->closeCursor(); // Important for calling another stored procedure or query

    // Capture status from form button (draft or sent)
    $status = $_POST['status'] ?? 'draft';

    // 3. Insert Invoice
    // Note: Totals will be updated by triggers, but we set initial values
    $stmt = $db->prepare("INSERT INTO invoices (
        invoice_number, customer_id, customer_name, customer_company, customer_address, 
        invoice_date, due_date, notes, terms, status, tax_rate
    ) VALUES (
        ?, ?, ?, ?, ?, 
        ?, DATE_ADD(?, INTERVAL 30 DAY), ?, ?, ?, 0
    )");
    
    $stmt->execute([
        $invoiceNumber, $customerId, $customerName, $customerCompany, $customerAddress,
        $invoiceDate, $invoiceDate, $notes, $terms, $status
    ]);
    
    $invoiceId = $db->lastInsertId();

    // 4. Insert Items
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
            $quantity = $item['quantity'];
            $price = $item['price'];
            $subtotal = $quantity * $price;
            
            // Get product name if product_id exists
            $productName = '';
            if ($productId) {
                // If using dropdown (create.php sets product_id)
                // We could fetch name, but for now we rely on what we have.
                // Actually user might submit product_id but NOT name.
                // Let's fetch name if needed, or assume manual entry?
                // The current Create.php sends product_id.
                // Let's fetch product name to be safe/complete.
                $pStmt = $db->prepare("SELECT name FROM products WHERE id = ?");
                $pStmt->execute([$productId]);
                $prod = $pStmt->fetch();
                $productName = $prod ? $prod['name'] : 'Unknown Product';
            } else {
                // Should not happen with new dropdown, but for safety
                $productName = $item['description'] ?? 'Item'; 
            }
            
            // Description is implicitly the product name or we can leave it empty
            $description = $productName;

            $stmtItem->execute([
                $invoiceId, $productId, $productName, $description, $quantity, $price, $subtotal
            ]);
        }
    }

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
