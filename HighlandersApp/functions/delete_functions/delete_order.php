<?php
session_start();
require_once '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    try {
        $conn->begin_transaction();
        
        // Delete order items first
        $conn->query("DELETE FROM orderitems WHERE order_id = $orderId");
        
        // Delete the order
        $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: ../../view/admin/admin_dashboard.php");
        } else {
            throw new Exception("Failed to delete order");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting order: " . $e->getMessage();
        header("Location: ../../view/admin/admin_dashboard.php");
    }
    exit();
}

?>