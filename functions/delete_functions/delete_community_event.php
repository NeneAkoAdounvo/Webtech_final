<?php
session_start();
require_once '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];
    
    try {
        // Delete the event image if exists
        $stmt = $conn->prepare("SELECT image_link FROM communityevents WHERE event_id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        
        if ($event && $event['image_link']) {
            $image_path = "../../" . $event['image_link'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the event
        $stmt = $conn->prepare("DELETE FROM communityevents WHERE event_id = ?");
        $stmt->bind_param("i", $eventId);
        
        if ($stmt->execute()) {
            header("Location: ../../view/admin/admin_dashboard.php");
        } else {
            throw new Exception("Failed to delete event");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}
?>