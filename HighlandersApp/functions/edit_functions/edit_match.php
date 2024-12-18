<?php
session_start();
require_once '../../db/config.php';

if (!isset($_GET['id'])) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

$match_id = $_GET['id'];

// Fetch current match data
$stmt = $conn->prepare("SELECT * FROM matches WHERE match_id = ?");
$stmt->bind_param("i", $match_id);
$stmt->execute();
$result = $stmt->get_result();
$match = $result->fetch_assoc();

if (!$match) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home_team = $_POST['home_team'];
    $away_team = $_POST['away_team'];
    $match_date = $_POST['match_date'];
    $match_time = $_POST['match_time'];
    $score = $_POST['score'];
    $stadium = $_POST['stadium'];
    $highlights_link = $_POST['highlights_link'];

    $update_stmt = $conn->prepare("UPDATE matches SET home_team = ?, away_team = ?, match_date = ?, match_time = ?, score = ?, stadium = ?, highlights_link = ? WHERE match_id = ?");
    $update_stmt->bind_param("iisssssi", $home_team, $away_team, $match_date, $match_time, $score, $stadium, $highlights_link, $match_id);
    
    if ($update_stmt->execute()) {
        header('Location: ../../view/admin/admin_dashboard.php');
        exit();
    }
}

// Fetch teams for dropdowns
$teams_result = $conn->query("SELECT team_id, team_name FROM teams");
$teams = [];
while ($team = $teams_result->fetch_assoc()) {
    $teams[] = $team;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Match</title>
    <style>
        .edit-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #f5c518;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #f5c518;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.9);
        }
        .submit-btn {
            background: #f5c518;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .current-image {
            max-width: 200px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="edit-form">
        <h2>Edit Match</h2>
        <form method="POST">
            <div class="form-group">
                <label>Home Team:</label>
                <select name="home_team" required>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['team_id'] ?>" 
                                <?= $team['team_id'] == $match['home_team'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($team['team_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Away Team:</label>
                <select name="away_team" required>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['team_id'] ?>" 
                                <?= $team['team_id'] == $match['away_team'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($team['team_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Match Date:</label>
                <input type="date" name="match_date" value="<?= $match['match_date'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Match Time:</label>
                <input type="time" name="match_time" value="<?= $match['match_time'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Score:</label>
                <input type="text" name="score" value="<?= htmlspecialchars($match['score']) ?>" 
                       placeholder="e.g., 2-1" pattern="\d+-\d+">
            </div>
            
            <div class="form-group">
                <label>Stadium:</label>
                <input type="text" name="stadium" value="<?= htmlspecialchars($match['stadium']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Highlights Link:</label>
                <input type="url" name="highlights_link" value="<?= htmlspecialchars($match['highlights_link']) ?>">
            </div>
            
            <button type="submit" class="submit-btn">Update Match</button>
        </form>
    </div>
</body>
</html>