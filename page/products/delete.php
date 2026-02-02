<?php
require_once '../../config/database.php';
$db = getDB();

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Soft delete (set status to inactive)
        $stmt = $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
        
        // Alternatively, hard delete if preferred (use with caution)
        // $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        // $stmt->execute([$id]);

    } catch (PDOException $e) {
        // Log error
    }
}

header("Location: list.php?deleted=true");
exit;
