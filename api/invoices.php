<?php
/**
 * Invoice API
 * Handle CRUD operations for invoices
 */

require_once '../config/database.php';

header('Content-Type: application/json');

$db = getDB();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            createInvoice($db);
            break;
            
        case 'update':
            updateInvoice($db);
            break;
            
        case 'delete':
            deleteInvoice($db);
            break;
            
        case 'get':
            getInvoice($db);
            break;
            
        case 'list':
            listInvoices($db);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Create new invoice
 */
function createInvoice($db) {
    // Validate input
    if (empty($_POST['customer_id']) || empty($_POST['items'])) {
        throw new Exception('Customer dan items harus diisi');
    }
    
    try {
        $db->beginTransaction();
        
        // Generate invoice number
        $invoiceNumber = generateInvoiceNumber($db);
        
        // Prepare invoice data
        $stmt = $db->prepare("
            INSERT INTO invoices (
                invoice_number, customer_id, customer_name, customer_company,
                customer_email, customer_phone, customer_address, customer_city,
                invoice_date, due_date, subtotal, tax_rate, tax_amount, total,
                status, notes, terms
            ) VALUES (
                :invoice_number, :customer_id, :customer_name, :customer_company,
                :customer_email, :customer_phone, :customer_address, :customer_city,
                :invoice_date, :due_date, :subtotal, :tax_rate, :tax_amount, :total,
                :status, :notes, :terms
            )
        ");
        
        $stmt->execute([
            'invoice_number' => $invoiceNumber,
            'customer_id' => $_POST['customer_id'],
            'customer_name' => $_POST['customer_name'],
            'customer_company' => $_POST['customer_company'] ?? null,
            'customer_email' => $_POST['customer_email'] ?? null,
            'customer_phone' => $_POST['customer_phone'] ?? null,
            'customer_address' => $_POST['customer_address'] ?? null,
            'customer_city' => $_POST['customer_city'] ?? null,
            'invoice_date' => $_POST['invoice_date'],
            'due_date' => $_POST['due_date'] ?? null,
            'subtotal' => $_POST['subtotal'],
            'tax_rate' => $_POST['tax_rate'] ?? 0,
            'tax_amount' => $_POST['tax_amount'] ?? 0,
            'total' => $_POST['total'],
            'status' => $_POST['status'] ?? 'draft',
            'notes' => $_POST['notes'] ?? null,
            'terms' => $_POST['terms'] ?? null
        ]);
        
        $invoiceId = $db->lastInsertId();
        
        // Insert invoice items
        $itemStmt = $db->prepare("
            INSERT INTO invoice_items (
                invoice_id, product_id, product_code, product_name,
                quantity, unit, unit_price, subtotal, sort_order
            ) VALUES (
                :invoice_id, :product_id, :product_code, :product_name,
                :quantity, :unit, :unit_price, :subtotal, :sort_order
            )
        ");
        
        $sortOrder = 0;
        foreach ($_POST['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }
            
            $sortOrder++;
            $itemStmt->execute([
                'invoice_id' => $invoiceId,
                'product_id' => $item['product_id'],
                'product_code' => $item['product_code'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'pcs',
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
                'sort_order' => $sortOrder
            ]);
        }
        
        $db->commit();
        
        // Set flash message and redirect
        setFlash('success', 'Invoice berhasil dibuat dengan nomor: ' . $invoiceNumber);
        header('Location: ../page/invoices/view.php?id=' . $invoiceId);
        exit;
        
    } catch (PDOException $e) {
        $db->rollBack();
        throw new Exception('Gagal menyimpan invoice: ' . $e->getMessage());
    }
}

/**
 * Update invoice
 */
function updateInvoice($db) {
    if (empty($_POST['id'])) {
        throw new Exception('ID invoice tidak valid');
    }
    
    try {
        $db->beginTransaction();
        
        // Check if invoice can be edited (only draft)
        $stmt = $db->prepare("SELECT status FROM invoices WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $invoice = $stmt->fetch();
        
        if (!$invoice) {
            throw new Exception('Invoice tidak ditemukan');
        }
        
        if ($invoice['status'] !== 'draft') {
            throw new Exception('Hanya invoice draft yang bisa diedit');
        }
        
        // Update invoice
        $stmt = $db->prepare("
            UPDATE invoices SET
                customer_id = :customer_id,
                customer_name = :customer_name,
                customer_company = :customer_company,
                customer_email = :customer_email,
                customer_phone = :customer_phone,
                customer_address = :customer_address,
                invoice_date = :invoice_date,
                due_date = :due_date,
                tax_rate = :tax_rate,
                status = :status,
                notes = :notes,
                terms = :terms
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $_POST['id'],
            'customer_id' => $_POST['customer_id'],
            'customer_name' => $_POST['customer_name'],
            'customer_company' => $_POST['customer_company'] ?? null,
            'customer_email' => $_POST['customer_email'] ?? null,
            'customer_phone' => $_POST['customer_phone'] ?? null,
            'customer_address' => $_POST['customer_address'] ?? null,
            'invoice_date' => $_POST['invoice_date'],
            'due_date' => $_POST['due_date'] ?? null,
            'tax_rate' => $_POST['tax_rate'] ?? 0,
            'status' => $_POST['status'] ?? 'draft',
            'notes' => $_POST['notes'] ?? null,
            'terms' => $_POST['terms'] ?? null
        ]);
        
        // Delete old items
        $stmt = $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $stmt->execute([$_POST['id']]);
        
        // Insert new items
        $itemStmt = $db->prepare("
            INSERT INTO invoice_items (
                invoice_id, product_id, product_code, product_name,
                quantity, unit, unit_price, subtotal, sort_order
            ) VALUES (
                :invoice_id, :product_id, :product_code, :product_name,
                :quantity, :unit, :unit_price, :subtotal, :sort_order
            )
        ");
        
        $sortOrder = 0;
        foreach ($_POST['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }
            
            $sortOrder++;
            $itemStmt->execute([
                'invoice_id' => $_POST['id'],
                'product_id' => $item['product_id'],
                'product_code' => $item['product_code'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'pcs',
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
                'sort_order' => $sortOrder
            ]);
        }
        
        $db->commit();
        
        setFlash('success', 'Invoice berhasil diupdate');
        header('Location: ../page/invoices/view.php?id=' . $_POST['id']);
        exit;
        
    } catch (PDOException $e) {
        $db->rollBack();
        throw new Exception('Gagal mengupdate invoice: ' . $e->getMessage());
    }
}

/**
 * Delete invoice
 */
function deleteInvoice($db) {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (empty($id)) {
        throw new Exception('ID invoice tidak valid');
    }
    
    try {
        // Check if invoice can be deleted
        $stmt = $db->prepare("SELECT status FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch();
        
        if (!$invoice) {
            throw new Exception('Invoice tidak ditemukan');
        }
        
        if ($invoice['status'] === 'paid') {
            throw new Exception('Invoice yang sudah lunas tidak bisa dihapus');
        }
        
        // Delete invoice (items will be deleted by CASCADE)
        $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        
        setFlash('success', 'Invoice berhasil dihapus');
        header('Location: ../page/invoices/list.php');
        exit;
        
    } catch (PDOException $e) {
        throw new Exception('Gagal menghapus invoice: ' . $e->getMessage());
    }
}

/**
 * Get single invoice
 */
function getInvoice($db) {
    $id = $_GET['id'] ?? null;
    
    if (empty($id)) {
        throw new Exception('ID invoice tidak valid');
    }
    
    $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
    $stmt->execute([$id]);
    $invoice = $stmt->fetch();
    
    if (!$invoice) {
        throw new Exception('Invoice tidak ditemukan');
    }
    
    // Get items
    $stmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY sort_order");
    $stmt->execute([$id]);
    $invoice['items'] = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $invoice
    ]);
}

/**
 * List invoices
 */
function listInvoices($db) {
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $limit = $_GET['limit'] ?? 50;
    $offset = $_GET['offset'] ?? 0;
    
    $sql = "SELECT * FROM view_invoice_summary WHERE 1=1";
    $params = [];
    
    if ($search) {
        $sql .= " AND (invoice_number LIKE :search OR customer_name LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if ($status) {
        $sql .= " AND status = :status";
        $params['status'] = $status;
    }
    
    $sql .= " ORDER BY invoice_date DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $invoices = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $invoices
    ]);
}
