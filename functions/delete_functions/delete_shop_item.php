<?php
session_start();
require_once '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    
    try {
        $conn->begin_transaction();
        
        // Delete related order items first
        $conn->query("DELETE FROM orderitems WHERE item_id = $itemId");
        
        // Delete the item image if exists
        $stmt = $conn->prepare("SELECT image_link FROM shopitems WHERE item_id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        
        if ($item && $item['image_link']) {
            $image_path = "../../" . $item['image_link'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the shop item
        $stmt = $conn->prepare("DELETE FROM shopitems WHERE item_id = ?");
        $stmt->bind_param("i", $itemId);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: ../../view/admin/admin_dashboard.php");
        } else {
            throw new Exception("Failed to delete item");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting item: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}
?>