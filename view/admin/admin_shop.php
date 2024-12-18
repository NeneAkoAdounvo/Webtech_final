<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';

// Fetch shop items
$shopItemsQuery = "SELECT * FROM shopitems";
$shopItemsResult = $conn->query($shopItemsQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="shop-section">
            <div class="card">
                <div class="section-header">
                    <h2>Shop Items Management</h2>
                    <button class="add-button" onclick="addShopItem()">Add New Item</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $shopItemsResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['item_id'] ?></td>
                                <td>
                                    <?php if ($row['image_link']): ?>
                                        <img src="../../<?= htmlspecialchars($row['image_link']) ?>" 
                                             alt="Product Image" style="max-width: 50px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td>$<?= number_format($row['price'], 2) ?></td>
                                <td><?= $row['stock'] ?></td>
                                <td>
                                    <button onclick="confirmDeleteShopItem(<?= $row['item_id'] ?>)">Delete</button>
                                    <button onclick="editShopItem(<?= $row['item_id'] ?>)">Edit</button>
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
function addShopItem() {
    window.location.href = "../../functions/add_functions/add_shop_item.php";
}

function confirmDeleteShopItem(id) {
    if (confirm("Are you sure you want to delete this item?")) {
        window.location.href = "../../functions/delete_functions/delete_shop_item.php?id=" + id;
    }
}

function editShopItem(id) {
    window.location.href = "../../functions/edit_functions/edit_shop_item.php?id=" + id;
}
</script>