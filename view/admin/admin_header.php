<!-- admin_header.php -->
<?php
session_start();
require_once '../../db/config.php'; // Ensure the correct path to config.php
require_once '../../utils/session_check.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['roleID'] != 2) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Highlanders FC</title>
    <link rel="stylesheet" href="../../assets/css/Admin.css">
    <style>
        .admin-nav {
            background: #333;
            padding: 10px;
            margin-bottom: 20px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            margin: 0 5px;
            border-radius: 4px;
        }
        .admin-nav a:hover {
            background: #444;
        }
        .admin-nav a.active {
            background: #f5c518;
            color: black;
        }
    </style>
</head>
<body>
    <div class="admin-nav">
        <a href="admin_dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a>
        <a href="admin_users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'class="active"' : ''; ?>>Users</a>
        <a href="admin_community.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_community.php' ? 'class="active"' : ''; ?>>Community Events</a>
        <a href="admin_news.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_news.php' ? 'class="active"' : ''; ?>>News</a>
        <a href="admin_matches.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_matches.php' ? 'class="active"' : ''; ?>>Matches</a>
        <a href="admin_players.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_players.php' ? 'class="active"' : ''; ?>>Players</a>
        <a href="admin_shop.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_shop.php' ? 'class="active"' : ''; ?>>Shop</a>
        <a href="admin_orders.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_orders.php' ? 'class="active"' : ''; ?>>Orders</a>
        <a href="admin_history.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_history.php' ? 'class="active"' : ''; ?>>History</a>
    </div>