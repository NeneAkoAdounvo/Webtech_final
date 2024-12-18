<?php
session_start();
require_once '../../db/config.php';

if (!isset($_GET['id'])) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

$player_id = $_GET['id'];

// Fetch current player data
$stmt = $conn->prepare("SELECT * FROM players WHERE player_id = ?");
$stmt->bind_param("i", $player_id);
$stmt->execute();
$result = $stmt->get_result();
$player = $result->fetch_assoc();

if (!$player) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $position = $_POST['position'];
    $team_id = $_POST['team_id'];
    $nationality = $_POST['nationality'];
    
    // Handle profile image upload
    $profile_image = $player['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
        $target_dir = "../../assets/images/players/";
        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = "assets/images/players/" . $new_filename;
        }
    }

    $update_stmt = $conn->prepare("UPDATE players SET name = ?, age = ?, position = ?, team_id = ?, nationality = ?, profile_image = ? WHERE player_id = ?");
    $update_stmt->bind_param("sissssi", $name, $age, $position, $team_id, $nationality, $profile_image, $player_id);
    
    if ($update_stmt->execute()) {
        header('Location: ../../view/admin/admin_dashboard.php');
        exit();
    }
}

// Fetch teams for dropdown
$teams_result = $conn->query("SELECT team_id, team_name FROM teams");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Player</title>
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
        <h2>Edit Player</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($player['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" value="<?= $player['age'] ?>" required min="16" max="50">
            </div>
            
            <div class="form-group">
                <label>Position:</label>
                <select name="position" required>
                    <option value="Goalkeeper" <?= $player['position'] === 'Goalkeeper' ? 'selected' : '' ?>>Goalkeeper</option>
                    <option value="Defender" <?= $player['position'] === 'Defender' ? 'selected' : '' ?>>Defender</option>
                    <option value="Midfielder" <?= $player['position'] === 'Midfielder' ? 'selected' : '' ?>>Midfielder</option>
                    <option value="Forward" <?= $player['position'] === 'Forward' ? 'selected' : '' ?>>Forward</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Team:</label>
                <select name="team_id" required>
                    <?php while ($team = $teams_result->fetch_assoc()): ?>
                        <option value="<?= $team['team_id'] ?>" 
                                <?= $team['team_id'] == $player['team_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($team['team_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nationality:</label>
                <input type="text" name="nationality" value="<?= htmlspecialchars($player['nationality']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Current Profile Image:</label>
                <?php if ($player['profile_image']): ?>
                    <img src="../../<?= htmlspecialchars($player['profile_image']) ?>" style="max-width: 200px;">
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Upload New Profile Image (optional):</label>
                <input type="file" name="profile_image" accept="image/*">
            </div>
            
            <button type="submit" class="submit-btn">Update Player</button>
        </form>
    </div>
</body>
</html>