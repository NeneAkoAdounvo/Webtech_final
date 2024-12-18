<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../db/config.php';

// Check if user has admin privileges
if ($_SESSION['roleID'] !== 2) {
    header("Location: ../../view/login.php");
    exit();
}

// Fetch available items for dropdown
$items_query = "SELECT item_id, name, price FROM shopitems WHERE stock > 0";
$items_result = $conn->query($items_query);
$items = [];
if ($items_result) {
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Fetch users for dropdown
$users_query = "SELECT user_id, username FROM users";
$users_result = $conn->query($users_query);
$users = [];
if ($users_result) {
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = (int)$_POST['user_id'];
    $item_ids = $_POST['item_id'];
    $quantities = $_POST['quantity'];
    $total_amount = 0;

    // Start transaction
    $conn->begin_transaction();

    try {
        // First, create the order
        $order_sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("id", $user_id, $total_amount);
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // Then, add order items and calculate total
        $item_sql = "INSERT INTO orderitems (order_id, item_id, quantity, subtotal) VALUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_sql);

        for ($i = 0; $i < count($item_ids); $i++) {
            if ($quantities[$i] > 0) {
                // Get item price
                $price_query = "SELECT price FROM shopitems WHERE item_id = ?";
                $price_stmt = $conn->prepare($price_query);
                $price_stmt->bind_param("i", $item_ids[$i]);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_row = $price_result->fetch_assoc();
                
                $subtotal = $price_row['price'] * $quantities[$i];
                $total_amount += $subtotal;

                $item_stmt->bind_param("iiid", $order_id, $item_ids[$i], $quantities[$i], $subtotal);
                $item_stmt->execute();

                // Update stock
                $update_stock = "UPDATE shopitems SET stock = stock - ? WHERE item_id = ?";
                $stock_stmt = $conn->prepare($update_stock);
                $stock_stmt->bind_param("ii", $quantities[$i], $item_ids[$i]);
                $stock_stmt->execute();
            }
        }

        // Update total amount
        $update_total = "UPDATE orders SET total_amount = ? WHERE order_id = ?";
        $total_stmt = $conn->prepare($update_total);
        $total_stmt->bind_param("di", $total_amount, $order_id);
        $total_stmt->execute();

        $conn->commit();
        $_SESSION['success'] = "Order added successfully!";
        header("Location: ../../view/admin/admin_dashboard.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error adding order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order</title>
    <style>
        
    body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background-color: #121212; /* Black background */
    color: #f5c518; /* Gold color for text */
}

h1 {
    color: #f5c518;
    text-align: center;
    margin-bottom: 20px;
}

/* Dashboard Container */
.dashboard-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

/* Form Container */
.form-container {
    background: #1e1e1e; /* Darker black for contrast */
    border: 2px solid #f5c518; /* Gold border */
    border-radius: 10px;
    padding: 20px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    color: #f5c518;
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #f5c518;
    border-radius: 5px;
    background: #2a2a2a;
    color: #f5c518;
    outline: none;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #ffd700; /* Brighter gold for focus */
}

textarea {
    resize: none;
}

/* Submit Button */
.submit-button {
    background-color: #f5c518; /* Gold button */
    color: #121212; /* Black text */
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.submit-button:hover {
    background-color: #ffd700; /* Brighter gold on hover */
    color: #000; /* Deeper black */
    transform: scale(1.05);
}

/* Success and Error Messages */
.success-message,
.error-message {
    text-align: center;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-weight: bold;
    color: #121212; /* Black text */
}

.success-message {
    background-color: #f5c518; /* Gold background for success */
}

.error-message {
    background-color: #ff4c4c; /* Red background for error */
    color: #fff; /* White text */
}

/* Main Content */
.main-content {
    background: #1e1e1e;
    border-radius: 10px;
    padding: 30px;
    max-width: 800px;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
}

        .item-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="main-content">
            <h1>Add New Order</h1>
            
            <?php 
            if (isset($_SESSION['error'])) {
                echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<div class='success-message'>" . $_SESSION['success'] . "</div>";
                unset($_SESSION['success']);
            }
            ?>

            <div class="form-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="user_id">Customer</label>
                        <select id="user_id" name="user_id" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="items-container">
                        <div class="item-row">
                            <div class="form-group">
                                <label for="item_id[]">Item</label>
                                <select name="item_id[]" required>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?php echo $item['item_id']; ?>">
                                            <?php echo htmlspecialchars($item['name'] . ' - $' . $item['price']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantity[]">Quantity</label>
                                <input type="number" name="quantity[]" required min="1">
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="addItemRow()" class="submit-button" style="margin-bottom: 10px;">Add Another Item</button>
                    <button type="submit" class="submit-button">Create Order</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addItemRow() {
            const container = document.getElementById('items-container');
            const row = container.querySelector('.item-row').cloneNode(true);
            row.querySelectorAll('input').forEach(input => input.value = '');
            container.appendChild(row);
        }
    </script>
</body>
</html>