<?php
require_once '../../config/database.php';
$db = getDB();

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Karena Foreign Key ON DELETE CASCADE, menghapus invoice akan otomatis menghapus invoice_items dan payments terkait
        $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: list.php?deleted=true");
        exit;
    } catch (PDOException $e) {
        // Jika gagal delete (misal karena constraint lain), log error
        error_log("Error deleting invoice: " . $e->getMessage());
        header("Location: list.php?error=delete_failed");
        exit;
    }
}

header("Location: list.php");
exit;
