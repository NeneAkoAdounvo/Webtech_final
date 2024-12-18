<?php
session_start();
require_once '../../db/config.php';

if (!isset($_GET['id'])) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

$news_id = $_GET['id'];

// Fetch current news data
$stmt = $conn->prepare("SELECT * FROM news WHERE news_id = ?");
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    header('Location: ../../view/admin/admin_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_POST['author_id'];
    
    // Handle image upload
    $image_link = $news['image_link'];
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../../uploads/news/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_link = "uploads/news/" . $new_filename;
        }
    }

    $update_stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, author_id = ?, image_link = ? WHERE news_id = ?");
    $update_stmt->bind_param("ssisi", $title, $content, $author_id, $image_link, $news_id);
    
    if ($update_stmt->execute()) {
        header('Location: ../../view/admin/admin_dashboard.php');
        exit();
    }
}

// Fetch authors for dropdown
$authors_result = $conn->query("SELECT user_id, username FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit News</title>
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
        <h2>Edit News Article</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" value="<?= htmlspecialchars($news['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Content:</label>
                <textarea name="content" rows="10" required><?= htmlspecialchars($news['content']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Author:</label>
                <select name="author_id">
                    <?php while ($author = $authors_result->fetch_assoc()): ?>
                        <option value="<?= $author['user_id'] ?>" 
                                <?= $author['user_id'] == $news['author_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($author['username']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Current Image:</label>
                <?php if ($news['image_link']): ?>
                    <img src="../../<?= htmlspecialchars($news['image_link']) ?>" style="max-width: 200px;">
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Upload New Image (optional):</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="submit-btn">Update News</button>
        </form>
    </div>
</body>
</html>