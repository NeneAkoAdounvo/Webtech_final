<?php
//require_once '../utils/session_check.php';
session_start();
require_once '../db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    try {
        $conn->begin_transaction();

        // Get item details and check stock
        $item_query = "SELECT * FROM shopitems WHERE item_id = ? AND stock >= ?";
        $stmt = $conn->prepare($item_query);
        $stmt->bind_param("ii", $item_id, $quantity);
        $stmt->execute();
        $item_result = $stmt->get_result();
        
        if ($item_result->num_rows > 0) {
            $item = $item_result->fetch_assoc();
            $subtotal = $item['price'] * $quantity;

            // Create order
            $order_query = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
            $stmt = $conn->prepare($order_query);
            $stmt->bind_param("id", $user_id, $subtotal);
            $stmt->execute();
            $order_id = $conn->insert_id;

            // Add order item
            $order_item_query = "INSERT INTO orderitems (order_id, item_id, quantity, subtotal) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($order_item_query);
            $stmt->bind_param("iiid", $order_id, $item_id, $quantity, $subtotal);
            $stmt->execute();

            // Update stock
            $update_stock = "UPDATE shopitems SET stock = stock - ? WHERE item_id = ?";
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("ii", $quantity, $item_id);
            $stmt->execute();

            $conn->commit();
            $success_message = "Purchase successful! Order ID: " . $order_id;
        } else {
            throw new Exception("Not enough stock available");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}

// Fetch available items
try {
    $query = "SELECT * FROM shopitems WHERE stock > 0 ORDER BY item_id";
    $result = $conn->query($query);
} catch (Exception $e) {
    $error_message = "Error loading products";
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/highlandersshop.css">
    <style>
        .product-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #f5c518;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            color: white;
        }
        .buy-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }
        .quantity-input {
            padding: 5px;
            width: 60px;
        }
        .buy-button {
            background: #f5c518;
            color: black;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: rgba(0, 255, 0, 0.1);
            color: #f5c518;
        }
        .error {
            background: rgba(255, 0, 0, 0.1);
            color: #ff4444;
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
            <a href="shop.php" class="tab-link active">Shop</a>
            <a href="myorders.php" class="tab-link">My orders</a>


        </div>
    </div>

    <div class="container">
        <h1>Official Merchandise</h1>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="products-list">
            <?php
            if ($result && $result->num_rows > 0) {
                while($item = $result->fetch_assoc()) {
                    ?>
                    <div class="product-card">
                        <?php if (!empty($item['image_link'])): ?>
                            <img src="<?php echo htmlspecialchars('../' . $item['image_link']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 style="max-width: 200px;">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <p>In Stock: <?php echo $item['stock']; ?></p>
                        
                        <form method="POST" class="buy-form">
                            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" 
                                   max="<?php echo $item['stock']; ?>" class="quantity-input" required>
                            <button type="submit" name="buy_now" class="buy-button">Buy Now</button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No products available at the moment.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        // Simple quantity validation
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const max = parseInt(this.getAttribute('max'));
                const value = parseInt(this.value);
                if (value > max) {
                    this.value = max;
                    alert('Quantity adjusted to maximum available stock');
                }
            });
        });
    </script>
</body>
</html>