<?php 
include 'admin_header.php';
require_once '../../utils/session_check.php';



// Fetch history entries
$historyQuery = "SELECT * FROM history ORDER BY history_id DESC";
$historyResult = $conn->query($historyQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="history-section">
            <div class="card">
                <div class="section-header">
                    <h2>History Management</h2>
                    <button class="add-button" onclick="addHistory()">Add New Entry</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Content Preview</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $historyResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['history_id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars(substr($row['content'], 0, 150)) ?>...</td>
                                <td>
                                    <?php if ($row['image_link']): ?>
                                        <img src="../../<?= htmlspecialchars($row['image_link']) ?>" 
                                             alt="History Image" style="max-width: 50px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="confirmDeleteHistory(<?= $row['history_id'] ?>)">Delete</button>
                                    <button onclick="editHistory(<?= $row['history_id'] ?>)">Edit</button>
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
function addHistory() {
    window.location.href = "../../functions/add_functions/add_history.php";
}

function confirmDeleteHistory(id) {
    if (confirm("Are you sure you want to delete this history entry?")) {
        window.location.href = "../../functions/delete_functions/delete_history.php?id=" + id;
    }
}

function editHistory(id) {
    window.location.href = "../../functions/edit_functions/edit_history.php?id=" + id;
}
</script>