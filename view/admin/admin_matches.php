<?php 
include 'admin_header.php';
//require_once '../../utils/session_check.php';


// Fetch matches with team names
$matchesQuery = "SELECT m.*, 
                 t1.team_name as home_team_name,
                 t2.team_name as away_team_name
                 FROM matches m 
                 LEFT JOIN teams t1 ON m.home_team = t1.team_id
                 LEFT JOIN teams t2 ON m.away_team = t2.team_id";
$matchesResult = $conn->query($matchesQuery);
?>

<div class="dashboard-container">
    <div class="main-content">
        <section class="matches-section">
            <div class="card">
                <div class="section-header">
                    <h2>Matches Management</h2>
                    <button class="add-button" onclick="addMatch()">Add New Match</button>
                </div>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Match ID</th>
                            <th>Home Team</th>
                            <th>Away Team</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Score</th>
                            <th>Stadium</th>
                            <th>Highlights</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $matchesResult->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['match_id'] ?></td>
                                <td><?= htmlspecialchars($row['home_team_name']) ?></td>
                                <td><?= htmlspecialchars($row['away_team_name']) ?></td>
                                <td><?= $row['match_date'] ?></td>
                                <td><?= $row['match_time'] ?></td>
                                <td><?= htmlspecialchars($row['score']) ?></td>
                                <td><?= htmlspecialchars($row['stadium']) ?></td>
                                <td>
                                    <?php if ($row['highlights_link']): ?>
                                        <a href="<?= htmlspecialchars($row['highlights_link']) ?>" 
                                           target="_blank">View Highlights</a>
                                    <?php else: ?>
                                        No Highlights
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="confirmDeleteMatch(<?= $row['match_id'] ?>)">Delete</button>
                                    <button onclick="editMatch(<?= $row['match_id'] ?>)">Edit</button>
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
function addMatch() {
    window.location.href = "../../functions/add_functions/add_match.php";
}

function confirmDeleteMatch(id) {
    if (confirm("Are you sure you want to delete this match?")) {
        window.location.href = "../../functions/delete_functions/delete_match.php?id=" + id;
    }
}

function editMatch(id) {
    window.location.href = "../../functions/edit_functions/edit_match.php?id=" + id;
}
</script>