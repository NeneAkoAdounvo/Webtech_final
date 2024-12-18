<?php
session_start();
require_once '../../db/config.php';

if (!isset($_GET['id'])) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

$event_id = $_GET['id'];

// Fetch current event data
$stmt = $conn->prepare("SELECT * FROM communityevents WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $organizer_id = $_POST['organizer_id'];
    
    // Handle image upload if provided
    $image_link = $event['image_link'];
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../../uploads/community/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_link = "uploads/community/" . $new_filename;
        }
    }

    $update_stmt = $conn->prepare("UPDATE communityevents SET event_name = ?, description = ?, event_date = ?, location = ?, organizer_id = ?, image_link = ? WHERE event_id = ?");
    $update_stmt->bind_param("ssssssi", $event_name, $description, $event_date, $location, $organizer_id, $image_link, $event_id);
    
    if ($update_stmt->execute()) {
        header('Location: ../../view/admin/admin_dashboard.php');
        exit();
    }
}

// Fetch organizers for dropdown
$organizers_result = $conn->query("SELECT user_id, username FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Community Event</title>
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
        <h2>Edit Community Event</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Event Name:</label>
                <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Location:</label>
                <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Organizer:</label>
                <select name="organizer_id">
                    <?php while ($organizer = $organizers_result->fetch_assoc()): ?>
                        <option value="<?= $organizer['user_id'] ?>" 
                                <?= $organizer['user_id'] == $event['organizer_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($organizer['username']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Current Image:</label>
                <?php if ($event['image_link']): ?>
                    <img src="../../<?= htmlspecialchars($event['image_link']) ?>" style="max-width: 200px;">
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Upload New Image (optional):</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="submit-btn">Update Event</button>
        </form>
    </div>
</body>
</html>