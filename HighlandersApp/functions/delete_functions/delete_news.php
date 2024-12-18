<?php
session_start();
require_once '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $newsId = $_GET['id'];
    
    try {
        // Delete the news image if exists
        $stmt = $conn->prepare("SELECT image_link FROM news WHERE news_id = ?");
        $stmt->bind_param("i", $newsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $news = $result->fetch_assoc();
        
        if ($news && $news['image_link']) {
            $image_path = "../../" . $news['image_link'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the news
        $stmt = $conn->prepare("DELETE FROM news WHERE news_id = ?");
        $stmt->bind_param("i", $newsId);
        
        if ($stmt->execute()) {
            header("Location: ../../view/admin/admin_dashboard.php");
        } else {
            throw new Exception("Failed to delete news");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting news: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}
?>