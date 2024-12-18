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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    // Image upload handling
    $image_link = '';
    if (isset($_FILES['history_image']) && $_FILES['history_image']['error'] == 0) {
        $upload_dir = '../../uploads/history';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($_FILES['history_image']['name']);
        $upload_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['history_image']['tmp_name'], $upload_path)) {
            $image_link = str_replace('../../', '', $upload_path);
        } else {
            $_SESSION['error'] = "Failed to upload image.";
        }
    }

    // Prepare SQL
    $sql = "INSERT INTO history (title, content, image_link) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $content, $image_link);

    if ($stmt->execute()) {
        $_SESSION['success'] = "History entry added successfully!";
        header("Location: ../../view/admin/admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding history entry: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add History Entry</title>
    <style>
        /* Copy the same CSS from your add_community_event.php */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #121212;
            color: #f5c518;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background: #1e1e1e;
            border: 2px solid #f5c518;
            border-radius: 10px;
            padding: 20px;
            width: 100%;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #f5c518;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #f5c518;
            background: #2a2a2a;
            color: #ffffff;
        }

        .submit-button {
            background-color: #f5c518;
            color: #121212;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: #ffd700;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="main-content">
            <h1>Add New History Entry</h1>
            
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
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="6" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="history_image">Image</label>
                        <input type="file" id="history_image" name="history_image" accept="image/*">
                    </div>

                    <button type="submit" class="submit-button">Add History Entry</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>