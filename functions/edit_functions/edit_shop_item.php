<?php
session_start();
require_once '../../db/config.php';

// Check if item ID is provided
if (!isset($_GET['id'])) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

$item_id = $_GET['id'];

// Fetch current item data
$stmt = $conn->prepare("SELECT * FROM shopitems WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    // Handle file upload if new image is provided
    $image_link = $item['image_link']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../../assets/images/shop/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_link = "assets/images/shop/" . $new_filename;
        }
    }

    // Update database
    $update_stmt = $conn->prepare("UPDATE shopitems SET name = ?, description = ?, price = ?, stock = ?, image_link = ? WHERE item_id = ?");
    $update_stmt->bind_param("ssddsi", $name, $description, $price, $stock, $image_link, $item_id);
    
    if ($update_stmt->execute()) {
        header('Location: ../../view/admin/admin_dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shop Item</title>
    <link rel="stylesheet" href="../../assets/css/Admin.css">
    <style>
        .edit-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #f5c518;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #f5c518;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.9);
        }
        .submit-btn {
            background: #f5c518;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .current-image {
            max-width: 200px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="edit-form">
        <h2>Edit Shop Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Price:</label>
                <input type="number" name="price" step="0.01" value="<?= $item['price'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" value="<?= $item['stock'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Current Image:</label>
                <?php if ($item['image_link']): ?>
                    <img src="../../<?= htmlspecialchars($item['image_link']) ?>" class="current-image" alt="Current Image">
                <?php else: ?>
                    <p>No image currently set</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Upload New Image (optional):</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="submit-btn">Update Item</button>
        </form>
    </div>
</body>
</html>
