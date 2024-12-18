<?php
session_start();
require_once '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $matchId = $_GET['id'];
    
    try {
        $conn->begin_transaction();
        
        // Delete match highlights first
        $conn->query("DELETE FROM matchhighlights WHERE match_id = $matchId");
        
        // Delete the match
        $stmt = $conn->prepare("DELETE FROM matches WHERE match_id = ?");
        $stmt->bind_param("i", $matchId);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: ../../view/admin/admin_dashboard.php");
        } else {
            throw new Exception("Failed to delete match");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting match: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}
?>