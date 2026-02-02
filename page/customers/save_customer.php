<?php
require_once '../../config/database.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $code = $_POST['code'];
        $name = $_POST['name'];
        $company = $_POST['company'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $postal_code = $_POST['postal_code'];
        $tax_id = $_POST['tax_id'];
        $notes = $_POST['notes'];

        if ($id) {
            // Update
            $stmt = $db->prepare("UPDATE customers SET code=?, name=?, company=?, email=?, phone=?, address=?, city=?, province=?, postal_code=?, tax_id=?, notes=? WHERE id=?");
            $stmt->execute([$code, $name, $company, $email, $phone, $address, $city, $province, $postal_code, $tax_id, $notes, $id]);
            $msg = "updated";
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO customers (code, name, company, email, phone, address, city, province, postal_code, tax_id, notes, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$code, $name, $company, $email, $phone, $address, $city, $province, $postal_code, $tax_id, $notes]);
            $msg = "created";
        }

        header("Location: list.php?status=success");
        exit;

    } catch (PDOException $e) {
        // Log Error
        error_log($e->getMessage());
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: list.php"); // Redirect if accessed directly without POST
    exit;
}
