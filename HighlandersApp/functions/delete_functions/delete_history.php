<?php
// delete_history.php
require_once '../../db/config.php';

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect back with an error message
    header("Location: ../../dashboard/admin/admin_dashboard.php?error=Invalid history ID");
    exit();
}

// Sanitize the ID
$history_id = mysqli_real_escape_string($conn, $_GET['id']);

// Prepare the delete query
$delete_query = "DELETE FROM history WHERE history_id = '$history_id'";

// Execute the query
if (mysqli_query($conn, $delete_query)) {
    // Successful deletion
    header("Location: ../../view/admin/admin_dashboard.php?success=History entry deleted successfully");
    exit();
} else {
    // Deletion failed
    header("Location: ../../view/admin/admin_dashboard.php?error=Failed to delete history entry: " . mysqli_error($conn));
    exit();
}

// Close the database connection
mysqli_close($conn);
?>