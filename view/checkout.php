<?php
// checkout.php
session_start();
require_once '../db/config.php';

if (empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        $conn->begin_transaction();
        
        // Create order record
        $user_id = $_SESSION['user_id'];
        $total_amount = 0;
        $shipping_address = $_POST['shipping_address'];
        $payment_method = $_POST['payment_method'];
        
        // Calculate total and verify stock
        $cart_items = getCartItems($conn);
        foreach ($cart_items as $item) {
            $total_amount += $item['subtotal'];
            
            // Verify stock availability
            $stock_query = "SELECT stock FROM shopitems WHERE item_id = ? FOR UPDATE";
            $stmt = $conn->prepare($stock_query);
            $stmt->bind_param("i", $item['item_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_stock = $result->fetch_assoc()['stock'];
            
            if ($current_stock < $item['quantity']) {
                throw new Exception("Insufficient stock for " . $item['name']);
            }
        }
        
        // Insert order
        $order_query = "INSERT INTO orders (user_id, total_amount, order_date, status, shipping_address, payment_method) 
                       VALUES (?, ?, NOW(), 'pending', ?, ?)";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("idss", $user_id, $total_amount, $shipping_address, $payment_method);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Insert order items and update stock
        foreach ($cart_items as $item) {
            // Calculate subtotal for each item
            $subtotal = $item['price'] * $item['quantity'];
            
            // Insert order item
            $item_query = "INSERT INTO orderitems (order_id, item_id, quantity, subtotal) 
                          VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($item_query);
            $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $subtotal);
            $stmt->execute();
            
            // Update stock
            $update_stock = "UPDATE shopitems SET stock = stock - ? WHERE item_id = ?";
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("ii", $item['quantity'], $item['item_id']);
            $stmt->execute();
        }
        
        $conn->commit();
        
        // Clear cart and redirect to success page
        unset($_SESSION['cart']);
        header("Location: order_success.php?order_id=" . $order_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}

// Get cart items function
function getCartItems($conn) {
    if (empty($_SESSION['cart'])) {
        return [];
    }
    
    $items = [];
    $item_ids = array_keys($_SESSION['cart']);
    $ids = implode(',', array_map('intval', $item_ids));
    
    $query = "SELECT * FROM shopitems WHERE item_id IN ($ids)";
    $result = $conn->query($query);
    
    while ($item = $result->fetch_assoc()) {
        $item['quantity'] = $_SESSION['cart'][$item['item_id']];
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $items[] = $item;
    }
    
    return $items;
}

$cart_items = getCartItems($conn);
$total = array_sum(array_column($cart_items, 'subtotal'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/highlandersshop.css">
    <style>
        .checkout-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #f5c518;
            border-radius: 10px;
            color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #f5c518;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #f5c518;
            background: #2a2a2a;
            color: #fff;
            border-radius: 5px;
        }
        .order-summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f5c518;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        
        <?php if (isset($error_message)): ?>
            <div style="color: red; margin-bottom: 20px;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="checkout-form">
            <div class="form-group">
                <label>Shipping Address</label>
                <input type="text" name="shipping_address" required>
            </div>
            
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="cash">Cash on Delivery</option>
                </select>
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cart_items as $item): ?>
                    <div>
                        <?php echo htmlspecialchars($item['name']); ?> x 
                        <?php echo $item['quantity']; ?> - 
                        $<?php echo number_format($item['subtotal'], 2); ?>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 10px;">
                    <strong>Total: $<?php echo number_format($total, 2); ?></strong>
                </div>
            </div>
            
            <button type="submit" name="place_order" class="checkout-btn">Place Order</button>
        </form>
    </div>
</body>
</html>