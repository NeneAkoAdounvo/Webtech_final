<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';


// Fetch players with team names
$playersQuery = "SELECT p.*, t.team_name 
                 FROM players p 
                 LEFT JOIN teams t ON p.team_id = t.team_id";
$playersResult = $conn->query($playersQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="players-section">
            <div class="card">
                <div class="section-header">
                    <h2>Players Management</h2>
                    <button class="add-button" onclick="addPlayer()">Add New Player</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Player ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Position</th>
                            <th>Team</th>
                            <th>Nationality</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $playersResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['player_id'] ?></td>
                                <td>
                                    <?php if ($row['profile_image']): ?>
                                        <img src="../../<?= htmlspecialchars($row['profile_image']) ?>" 
                                             alt="Player Photo" style="max-width: 50px; border-radius: 50%;">
                                    <?php else: ?>
                                        No Photo
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= $row['age'] ?></td>
                                <td><?= $row['position'] ?></td>
                                <td><?= htmlspecialchars($row['team_name']) ?></td>
                                <td><?= htmlspecialchars($row['nationality']) ?></td>
                                <td>
                                    <button onclick="confirmDeletePlayer(<?= $row['player_id'] ?>)">Delete</button>
                                    <button onclick="editPlayer(<?= $row['player_id'] ?>)">Edit</button>
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
function addPlayer() {
    window.location.href = "../../functions/add_functions/add_player.php";
}

function confirmDeletePlayer(id) {
    if (confirm("Are you sure you want to delete this player?")) {
        window.location.href = "../../functions/delete_functions/delete_player.php?id=" + id;
    }
}

function editPlayer(id) {
    window.location.href = "../../functions/edit_functions/edit_player.php?id=" + id;
}
</script>