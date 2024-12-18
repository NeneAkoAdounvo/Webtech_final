<?php
session_start();
require_once '../../db/config.php';

if (!isset($_GET['id'])) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

$order_id = $_GET['id'];

// Fetch current order data
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_amount = $_POST['total_amount'];
    $user_id = $_POST['user_id'];

    $update_stmt = $conn->prepare("UPDATE orders SET total_amount = ?, user_id = ? WHERE order_id = ?");
    $update_stmt->bind_param("dii", $total_amount, $user_id, $order_id);
    
    if ($update_stmt->execute()) {
        header('Location: ../../view/admin/admin_dashboard.php');
        exit();
    }
}

// Fetch users for dropdown
$users_result = $conn->query("SELECT user_id, username FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
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
        <h2>Edit Order</h2>
        <form method="POST">
            <div class="form-group">
                <label>User:</label>
                <select name="user_id" required>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['user_id'] ?>" 
                                <?= $user['user_id'] == $order['user_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Total Amount:</label>
                <input type="number" step="0.01" name="total_amount" 
                       value="<?= $order['total_amount'] ?>" required>
            </div>
            
            <button type="submit" class="submit-btn">Update Order</button>
        </form>
    </div>
</body>
</html>