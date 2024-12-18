<?php
session_start();
require_once '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $playerId = $_GET['id'];
    
    try {
        // Delete player profile image if exists
        $stmt = $conn->prepare("SELECT profile_image FROM players WHERE player_id = ?");
        $stmt->bind_param("i", $playerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $player = $result->fetch_assoc();
        
        if ($player && $player['profile_image']) {
            $image_path = "../../" . $player['profile_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the player
        $stmt = $conn->prepare("DELETE FROM players WHERE player_id = ?");
        $stmt->bind_param("i", $playerId);
        
        if ($stmt->execute()) {
            header("Location: ../../view/admin/dashboard.php");
        } else {
            throw new Exception("Failed to delete player");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting player: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}

?>