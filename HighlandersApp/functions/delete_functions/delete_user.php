<?php
session_start();
require_once '../../db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    
    try {
        $conn->begin_transaction();
        
        // Delete related data first
        $conn->query("DELETE FROM orders WHERE user_id = $userId");
        $conn->query("DELETE FROM communityevents WHERE organizer_id = $userId");
        $conn->query("DELETE FROM news WHERE author_id = $userId");
        
        // Finally delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: ../../view/admin/admin_dashboard.php");
        } else {
            throw new Exception("Failed to delete user");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}
?>