<?php
session_start();
require_once '../db/config.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart items
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $item_ids = array_keys($_SESSION['cart']);
    $items_list = implode(',', $item_ids);
    
    $sql = "SELECT * FROM shopitems WHERE item_id IN ($items_list)";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($item = $result->fetch_assoc()) {
            $item['quantity'] = $_SESSION['cart'][$item['item_id']];
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $cart_items[] = $item;
            $total += $item['subtotal'];
        }
    }
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!empty($cart_items)) {
        $user_id = $_SESSION['user_id']; // Make sure user is logged in
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create order
            $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("id", $user_id, $total);
            $stmt->execute();
            
            $order_id = $conn->insert_id;
            
            // Add order items
            $sql = "INSERT INTO orderitems (order_id, item_id, quantity, subtotal) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            foreach ($cart_items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $subtotal);
                $stmt->execute();
                
                // Update stock
                $sql_update = "UPDATE shopitems SET stock = stock - ? WHERE item_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ii", $item['quantity'], $item['item_id']);
                $stmt_update->execute();
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $conn->commit();
            $_SESSION['message'] = "Order placed successfully!";
            header("Location: shop.php");
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Error placing order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/highlandersshop.css">
    <style>
        .cart-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .cart-item {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #f5c518;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-item-info {
            flex-grow: 1;
        }

        .cart-item-name {
            color: #f5c518;
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: #fff;
        }

        .cart-total {
            text-align: right;
            color: #f5c518;
            font-size: 1.5em;
            margin: 20px 0;
        }

        .checkout-button {
            background: #f5c518;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
        }

        .checkout-button:hover {
            background: #ffd700;
        }

        .empty-cart {
            text-align: center;
            color: #f5c518;
            font-size: 1.2em;
            margin: 50px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Highlanders FC</div>
        <div class="tabs">
            <a href="userHome.php" class="tab-link">Home</a>
            <a href="matchday.php" class="tab-link">Matchday</a>
            <a href="latest.php" class="tab-link">Latest News</a>
            <a href="players.php" class="tab-link">Player Profiles</a>
            <a href="community.php" class="tab-link">Community</a>
            <a href="history.php" class="tab-link">History</a>
            <a href="shop.php" class="tab-link">Shop</a>
        </div>
    </div>

    <div class="cart-container">
        <h1>Your Cart</h1>

        <?php if (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <?php if (!empty($item['image_link'])): ?>
                        <img src="<?php echo htmlspecialchars('../' . $item['image_link']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php endif; ?>
                    <div class="cart-item-info">
                        <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="cart-item-price">
                            $<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?>
                            = $<?php echo number_format($item['subtotal'], 2); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-total">
                Total: $<?php echo number_format($total, 2); ?>
            </div>

            <form method="POST">
                <button type="submit" name="place_order" class="checkout-button">
                    Place Order
                </button>
            </form>
            
        <?php else: ?>
            <div class="empty-cart">
                Your cart is empty. <a href="shop.php" style="color: #f5c518;">Continue shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>