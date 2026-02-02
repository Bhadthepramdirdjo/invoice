<?php
/**
 * Database Configuration
 * Invoice App
 * 
 * File ini berisi konfigurasi koneksi database
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'invoice_app');
define('DB_CHARSET', 'utf8mb4');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (Development)
// Set ke 0 untuk production
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Database Connection Class
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $charset = DB_CHARSET;
    
    private $conn;
    private $error;
    
    /**
     * Connect to database
     */
    public function connect() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo "Connection Error: " . $this->error;
        }
        
        return $this->conn;
    }
    
    /**
     * Get connection instance
     */
    public function getConnection() {
        return $this->connect();
    }
}

/**
 * Helper function untuk get database connection
 */
function getDB() {
    $database = new Database();
    return $database->getConnection();
}

/**
 * Helper function untuk format currency
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Helper function untuk format date
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Helper function untuk format datetime
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Helper function untuk sanitize input
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Helper function untuk redirect
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Helper function untuk set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Helper function untuk get flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Helper function untuk generate invoice number
 */
function generateInvoiceNumber($conn) {
    try {
        // Call stored procedure
        $stmt = $conn->query("CALL sp_generate_invoice_number()");
        $result = $stmt->fetch();
        return $result['invoice_number'];
    } catch(PDOException $e) {
        // Fallback jika stored procedure gagal
        $year = date('Y');
        $stmt = $conn->query("SELECT invoice_next_number FROM company_settings LIMIT 1");
        $result = $stmt->fetch();
        $number = $result['invoice_next_number'];
        
        // Update next number
        $conn->query("UPDATE company_settings SET invoice_next_number = invoice_next_number + 1");
        
        return "INV-{$year}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}

/**
 * Helper function untuk get company settings
 */
function getCompanySettings($conn) {
    $stmt = $conn->query("SELECT * FROM company_settings LIMIT 1");
    return $stmt->fetch();
}

/**
 * Helper function untuk calculate invoice total
 */
function calculateInvoiceTotal($items, $taxRate = 0) {
    $subtotal = 0;
    
    foreach ($items as $item) {
        $itemSubtotal = $item['quantity'] * $item['unit_price'];
        
        // Apply item discount if any
        if (!empty($item['discount_type']) && !empty($item['discount_value'])) {
            if ($item['discount_type'] == 'percentage') {
                $itemSubtotal -= ($itemSubtotal * $item['discount_value'] / 100);
            } else {
                $itemSubtotal -= $item['discount_value'];
            }
        }
        
        $subtotal += $itemSubtotal;
    }
    
    $taxAmount = $subtotal * ($taxRate / 100);
    $total = $subtotal + $taxAmount;
    
    return [
        'subtotal' => $subtotal,
        'tax_amount' => $taxAmount,
        'total' => $total
    ];
}

/**
 * Helper function untuk get invoice status badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'draft' => 'badge-info',
        'sent' => 'badge-warning',
        'paid' => 'badge-success',
        'partial' => 'badge-warning',
        'overdue' => 'badge-danger',
        'cancelled' => 'badge-danger'
    ];
    
    return $classes[$status] ?? 'badge-info';
}

/**
 * Helper function untuk get invoice status label
 */
function getStatusLabel($status) {
    $labels = [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'paid' => 'Lunas',
        'partial' => 'Dibayar Sebagian',
        'overdue' => 'Jatuh Tempo',
        'cancelled' => 'Dibatalkan'
    ];
    
    return $labels[$status] ?? $status;
}

/**
 * Helper function untuk get stock status
 */
function getStockStatus($stock, $minStock) {
    if ($stock == 0) {
        return ['status' => 'Habis', 'class' => 'badge-danger'];
    } elseif ($stock <= $minStock) {
        return ['status' => 'Stok Menipis', 'class' => 'badge-warning'];
    } else {
        return ['status' => 'Tersedia', 'class' => 'badge-success'];
    }
}

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
