<?php
require_once '../../config/database.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? '';
        $code = $_POST['code'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'] ?: 0;
        $unit = $_POST['unit'];
        $description = $_POST['description'];

        $description = $_POST['description'];
        
        // Handle File Upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            
            // Validate extension
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            
            if (in_array($fileExtension, $allowedfileExtensions) && $fileSize < 2097152) { // Max 2MB
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;
                
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = $newFileName;
                }
            }
        }

        if ($id) {
            // Update logic
            $query = "UPDATE products SET code = ?, name = ?, price = ?, stock = ?, unit = ?, description = ?";
            $params = [$code, $name, $price, $stock, $unit, $description];
            
            if ($imageName) {
                $query .= ", image = ?";
                $params[] = $imageName;
            }
            
            $query .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            
        } else {
            // Insert logic
            $stmt = $db->prepare("INSERT INTO products (code, name, price, stock, unit, description, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$code, $name, $price, $stock, $unit, $description, $imageName]);
        }

        header("Location: list.php?status=success");
        exit;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
header("Location: list.php");
