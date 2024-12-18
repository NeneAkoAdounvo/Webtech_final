<?php
//require_once '../utils/session_check.php';
// order_success.php
session_start();
require_once '../db/config.php';

if (!isset($_GET['order_id'])) {
    header("Location: shop.php");
    exit();
}

$order_id = $_GET['order_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/highlandersshop.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #f5c518;
            border-radius: 10px;
            text-align: center;
            color: #fff;
        }
        .success-icon {
            color: #f5c518;
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>Order Placed Successfully!</h1>
        <p>Your order #<?php echo htmlspecialchars($order_id); ?> has been placed successfully.</p>
        <p>We will process your order soon.</p>
        <a href="shop.php" class="checkout-btn">Continue Shopping</a>
    </div>
</body>
</html>