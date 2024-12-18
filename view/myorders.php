<?php
//require_once '../utils/session_check.php';
session_start();
require_once '../db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle order editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_order'])) {
    try {
        $conn->begin_transaction();

        $order_id = $_POST['order_id'];
        $item_id = $_POST['item_id'];
        $new_quantity = $_POST['quantity'];

        // Verify the order belongs to the user
        $check_query = "SELECT 1 FROM orders WHERE order_id = ? AND user_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("You do not have permission to edit this order.");
        }

        // Check stock availability
        $stock_query = "SELECT stock, price FROM shopitems WHERE item_id = ?";
        $stmt = $conn->prepare($stock_query);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $item_data = $stmt->get_result()->fetch_assoc();

        if ($new_quantity > $item_data['stock']) {
            throw new Exception("Requested quantity exceeds available stock.");
        }

        // Calculate new subtotal
        $new_subtotal = $new_quantity * $item_data['price'];

        // Update the order item
        $update_item_query = "UPDATE orderitems SET quantity = ?, subtotal = ? WHERE order_id = ? AND item_id = ?";
        $stmt = $conn->prepare($update_item_query);
        $stmt->bind_param("idii", $new_quantity, $new_subtotal, $order_id, $item_id);
        $stmt->execute();

        // Recalculate total order amount
        $recalc_total_query = "UPDATE orders o 
            SET total_amount = (
                SELECT SUM(subtotal) 
                FROM orderitems oi 
                WHERE oi.order_id = o.order_id
            ) 
            WHERE order_id = ?";
        $stmt = $conn->prepare($recalc_total_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $conn->commit();
        $success_message = "Order successfully updated.";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error updating order: " . $e->getMessage();
    }
}

// Handle order deletion
if (isset($_POST['delete_order'])) {
    try {
        $conn->begin_transaction();

        $order_id = $_POST['order_id'];

        // Delete order items
        $delete_orderitems_query = "DELETE FROM orderitems WHERE order_id = ?";
        $stmt = $conn->prepare($delete_orderitems_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        // Delete the order
        $delete_order_query = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $conn->prepare($delete_order_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $conn->commit();
        $success_message = "Order successfully deleted.";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error deleting order: " . $e->getMessage();
    }
}

// Fetch user's order history
try {
    $orders_query = "
        SELECT 
            o.order_id, 
            o.total_amount, 
            o.order_date,
            oi.item_id,
            si.name AS item_name,
            si.price AS item_price,
            si.stock AS item_stock,
            oi.quantity AS order_quantity
        FROM orders o
        JOIN orderitems oi ON o.order_id = oi.order_id
        JOIN shopitems si ON oi.item_id = si.item_id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ";
    $stmt = $conn->prepare($orders_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders_result = $stmt->get_result();
} catch (Exception $e) {
    $error_message = "Error loading order history: " . $e->getMessage();
    $orders_result = null;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/highlandersshop.css">
    <style>
    .order-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        border-radius: 10px;
    }

    .order-card {
        background-color: rgba(30, 30, 30, 0.9);
        border: 2px solid #f5c518;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f5c518;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .order-header h2 {
        color: #f5c518;
        margin: 0;
    }

    .delete-order-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .delete-order-btn:hover {
        background-color: #a71d2a;
    }

    .order-details {
        line-height: 1.6;
    }

    .order-details p {
        margin: 10px 0;
    }

    .edit-form {
        display: flex;
        align-items: center;
        gap: 10px;
        background-color: rgba(50, 50, 50, 0.9);
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
    }

    .edit-quantity-input {
        width: 70px;
        padding: 5px;
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 3px;
    }

    .edit-order-btn {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .edit-order-btn:hover {
        background-color: #218838;
    }

    .no-orders {
        text-align: center;
        padding: 50px 20px;
        background-color: rgba(30, 30, 30, 0.9);
        border-radius: 10px;
    }

    .no-orders h2 {
        color: #f5c518;
        margin-bottom: 15px;
    }

    .no-orders p {
        color: #cccccc;
    }

    .message {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        text-align: center;
    }

    .message.success {
        background-color: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }

    .message.error {
        background-color: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    @media (max-width: 600px) {
        .edit-form {
            flex-direction: column;
            align-items: stretch;
        }

        .edit-form label {
            margin-bottom: 5px;
        }
    }




        .edit-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .edit-quantity-input {
            width: 60px;
            padding: 5px;
        }
        .edit-order-btn {
            background-color: #f5c518;
            color: black;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-order-btn:hover {
            background-color: #d4a808;
        }
    </style>
</head>
<body>
    <!-- Header -->
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
           <a href="myorders.php" class="tab-link active">My orders</a>


       </div>
   </div>

    <div class="order-container">
        <h1>My Orders</h1>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php 
        // Group orders by their order ID
        $grouped_orders = [];
        if ($orders_result) {
            while($order = $orders_result->fetch_assoc()) {
                $grouped_orders[$order['order_id']][] = $order;
            }
        }
        ?>

        <?php if (!empty($grouped_orders)): ?>
            <?php foreach($grouped_orders as $order_id => $order_items): ?>
                <div class="order-card">
                    <div class="order-header">
                        <h2>Order #<?php echo htmlspecialchars($order_id); ?></h2>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <button type="submit" name="delete_order" class="delete-order-btn">
                                Delete Order
                            </button>
                        </form>
                    </div>
                    <div class="order-details">
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($order_items[0]['order_date']); ?></p>
                        
                        <?php foreach($order_items as $item): ?>
                            <div>
                                <p>
                                    <strong>Item:</strong> <?php echo htmlspecialchars($item['item_name']); ?> 
                                    ($<?php echo number_format($item['item_price'], 2); ?>)
                                </p>
                                
                                <form method="POST" class="edit-form" onsubmit="return confirm('Are you sure you want to update this item quantity?');">
                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <label for="quantity-<?php echo $item['item_id']; ?>">Quantity:</label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        id="quantity-<?php echo $item['item_id']; ?>"
                                        value="<?php echo $item['order_quantity']; ?>" 
                                        min="1" 
                                        max="<?php echo $item['item_stock']; ?>" 
                                        class="edit-quantity-input"
                                        required
                                    >
                                    <button type="submit" name="edit_order" class="edit-order-btn">
                                        Update Quantity
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>

                        <p><strong>Total Amount:</strong> $<?php echo number_format($order_items[0]['total_amount'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-orders">
                <h2>No Orders Found</h2>
                <p>You haven't placed any orders yet. Check out our shop!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>