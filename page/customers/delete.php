<?php
require_once '../../config/database.php';
$db = getDB();

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Soft Delete: Set is_active = 0
        $stmt = $db->prepare("UPDATE customers SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Error deleting customer: " . $e->getMessage());
    }
}

header("Location: list.php?deleted=true");
exit;
