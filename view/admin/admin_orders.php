<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';

// Fetch orders with user details
$ordersQuery = "SELECT o.*, u.username, u.email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.user_id
                ORDER BY o.order_date DESC";
$ordersResult = $conn->query($ordersQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="orders-section">
            <div class="card">
                <div class="section-header">
                    <h2>Orders Management</h2>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $ordersResult->fetch_assoc()) { 
                            // Fetch order items
                            $itemsQuery = "SELECT si.name, oi.quantity 
                                         FROM orderitems oi 
                                         JOIN shopitems si ON oi.item_id = si.item_id 
                                         WHERE oi.order_id = " . $row['order_id'];
                            $itemsResult = $conn->query($itemsQuery);
                            $items = [];
                            while ($item = $itemsResult->fetch_assoc()) {
                                $items[] = $item['quantity'] . 'x ' . $item['name'];
                            }
                        ?>
                            <tr>
                                <td><?= $row['order_id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($row['username']) ?><br>
                                    <small><?= htmlspecialchars($row['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars(implode(', ', $items)) ?></td>
                                <td>$<?= number_format($row['total_amount'], 2) ?></td>
                                <td><?= $row['order_date'] ?></td>
                                <td>
                                    <button onclick="viewOrderDetails(<?= $row['order_id'] ?>)">View Details</button>
                                    <button onclick="confirmDeleteOrder(<?= $row['order_id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
function viewOrderDetails(id) {
    window.location.href = "view_order.php?id=" + id;
}

function confirmDeleteOrder(id) {
    if (confirm("Are you sure you want to delete this order?")) {
        window.location.href = "../../functions/delete_functions/delete_order.php?id=" + id;
    }
}
</script>