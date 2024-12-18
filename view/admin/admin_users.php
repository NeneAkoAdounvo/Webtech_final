<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';




// Fetch users
$usersQuery = "SELECT * FROM users";
$usersResult = $conn->query($usersQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="users-section">
            <div class="card">
                <div class="section-header">
                    <h2>Users Management</h2>
                    <button class="add-button" onclick="addUser()">Add New User</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $usersResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['user_id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= $row['role'] ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <button onclick="confirmDeleteUser(<?= $row['user_id'] ?>)">Delete</button>
                                    <button onclick="editUser(<?= $row['user_id'] ?>)">Edit</button>
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
function addUser() {
    window.location.href = "../../functions/add_functions/add_user.php";
}

function confirmDeleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        window.location.href = "../../functions/delete_functions/delete_user.php?id=" + id;
    }
}

function editUser(id) {
    window.location.href = "../../functions/edit_functions/edit_user.php?id=" + id;
}
</script>