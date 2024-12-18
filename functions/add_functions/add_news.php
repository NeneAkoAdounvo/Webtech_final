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
    $author_id = $_SESSION['user_id'];

    // Image upload handling
    $image_link = '';
    if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] == 0) {
        $upload_dir = '../../uploads/news/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = uniqid() . '_' . basename($_FILES['news_image']['name']);
        $upload_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['news_image']['tmp_name'], $upload_path)) {
            $image_link = str_replace('../../', '', $upload_path);
        } else {
            $_SESSION['error'] = "Failed to upload image.";
        }
    }

    // Prepare SQL
    $sql = "INSERT INTO news (title, content, author_id, image_link) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $content, $author_id, $image_link);

    if ($stmt->execute()) {
        $_SESSION['success'] = "News added successfully!";
        header("Location: ../../view/admin/admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding news: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News</title>
    <style>
    body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background-color: #121212; /* Black background */
    color: #f5c518; /* Gold color for text */
}

h1 {
    color: #f5c518;
    text-align: center;
    margin-bottom: 20px;
}

/* Dashboard Container */
.dashboard-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

/* Form Container */
.form-container {
    background: #1e1e1e; /* Darker black for contrast */
    border: 2px solid #f5c518; /* Gold border */
    border-radius: 10px;
    padding: 20px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    color: #f5c518;
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #f5c518;
    border-radius: 5px;
    background: #2a2a2a;
    color: #f5c518;
    outline: none;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #ffd700; /* Brighter gold for focus */
}

textarea {
    resize: none;
}

/* Submit Button */
.submit-button {
    background-color: #f5c518; /* Gold button */
    color: #121212; /* Black text */
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.submit-button:hover {
    background-color: #ffd700; /* Brighter gold on hover */
    color: #000; /* Deeper black */
    transform: scale(1.05);
}

/* Success and Error Messages */
.success-message,
.error-message {
    text-align: center;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-weight: bold;
    color: #121212; /* Black text */
}

.success-message {
    background-color: #f5c518; /* Gold background for success */
}

.error-message {
    background-color: #ff4c4c; /* Red background for error */
    color: #fff; /* White text */
}

/* Main Content */
.main-content {
    background: #1e1e1e;
    border-radius: 10px;
    padding: 30px;
    max-width: 800px;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
}
</style>
</head>
<body>
    <div class="dashboard-container">
        <div class="main-content">
            <h1>Add News Article</h1>
            
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
                        <textarea id="content" name="content" rows="10" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="news_image">Image</label>
                        <input type="file" id="news_image" name="news_image" accept="image/*">
                    </div>

                    <button type="submit" class="submit-button">Add News Article</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>