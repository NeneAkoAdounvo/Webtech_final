<?php
session_start(); // Start session to use session variables

// Include database configuration
require_once '../db/config.php'; // Ensure the correct path to config.php

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    echo 'reached here';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header("Location: ../view/login.php");
        exit();
        echo 'reached here 2';
    }

    // Prepare SQL query with more comprehensive user details
    $sql = "SELECT user_id, email, password, roleID, role FROM users WHERE email = ?";

    // Use prepared statements with mysqli
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("s", $email);
        echo 'reached here 3';

        // Execute statement
        if ($stmt->execute()) {
            // Get result
            $result = $stmt->get_result();
            echo 'reached here 4';

            // Check if a user is found
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc(); // Fetch the user data
                echo 'reached here 5';
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    echo 'reached here 6';
                    // Clear any previous error messages
                    unset($_SESSION['error']);
                
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['roleID'] = $user['roleID'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['last_activity'] = time();
                    $_SESSION['created'] = time();
                
                    // Ensure session is written before redirecting
                    session_write_close();
                
                    // Redirect based on roleID
                    if ($user['roleID'] == 2) {
                        header("Location: ../view/admin/admin_dashboard.php");
                    } else {
                        header("Location: ../view/userHome.php");
                    }
                    exit();
                
                } else {
                    // Invalid password
                    $_SESSION['error'] = 'Incorrect email or password.';
                    error_log("Login failed: Incorrect password for email " . $email);
                }
            } else {
                // User not found
                $_SESSION['error'] = 'No user found with that email address.';
                error_log("Login failed: No user found for email " . $email);
            }
        } else {
            // Query execution failed
            $_SESSION['error'] = 'Database error. Please try again later.';
            error_log("Login query execution failed: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } else {
        // SQL preparation failed
        $_SESSION['error'] = 'Database error. Please try again later.';
        error_log("Login statement preparation failed: " . $conn->error);
    }

    // Redirect back to the login page with error
    header("Location: ../view/login.php");
    exit();
} else {
    // Invalid request method
    
    $_SESSION['error'] = 'Invalid request method.';
    header("Location: ../view/login.php");
    exit();
}
?>