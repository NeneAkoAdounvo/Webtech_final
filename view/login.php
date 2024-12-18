<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Move this to the top

// Include database configuration
require_once '../db/config.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/SignUpstyles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2c3e50, #f1c40f);
            color: white;
            text-align: center;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .title {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        .button {
            width: 100%;
            padding: 12px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .button:hover {
            background: rgba(0, 0, 0, 0.9);
        }
        .link {
            color: #f1c40f;
            text-decoration: none;
            display: block;
            margin-top: 15px;
        }
        .link:hover {
            text-decoration: underline;
        }
        /* Error message styling */
        .error-message {
            color: red;
            font-size: 1rem;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Login to Highlanders FC</div>
        <form action="../actions/login_user.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Login</button>
        </form>
        <a href="signUp.php" class="link">Don't have an account? Sign Up</a>

        <!-- Error message display -->
        <div id="errorMessage" class="error-message"></div>
    </div>

    <script>
        // Function to display error messages dynamically
        function displayErrorMessage(message) {
            const errorMessageDiv = document.getElementById('errorMessage');
            if (message) {
                errorMessageDiv.textContent = message;
            }
        }

        // Get error message passed from PHP session (if any)
        <?php
        if (isset($_SESSION['error'])) {
            echo "displayErrorMessage('" . $_SESSION['error'] . "');";
            unset($_SESSION['error']); // Clear the error message after displaying
        }
        ?>
    </script>
</body>
</html>
