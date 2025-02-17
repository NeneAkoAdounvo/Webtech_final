<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure JSON response
header('Content-Type: application/json');

// Database connection
require_once '../db/config.php';

// Response array
$response = [
    'success' => false,
    'errors' => []
];

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    $errors = [];

    // Validate First Name
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required";
    } elseif (strlen($first_name) < 2) {
        $errors['first_name'] = "First name must be at least 2 characters";
    }

    // Validate Last Name
    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required";
    } elseif (strlen($last_name) < 2) {
        $errors['last_name'] = "Last name must be at least 2 characters";
    }

    // Validate Email
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Validate Password
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }

    // Confirm Password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Check if there are any validation errors
    if (!empty($errors)) {
        $response['errors'] = $errors;
        echo json_encode($response);
        exit();
    }

    // Check for existing user by email
    $check_query = "SELECT * FROM Users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $response['errors']['email'] = "Email already exists";
        echo json_encode($response);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL insert statement
    $insert_query = "INSERT INTO Users (username, email, password, role, created_at) 
                     VALUES ('$first_name $last_name', '$email', '$hashed_password', 'user', NOW())";

    // Execute the query
    if (mysqli_query($conn, $insert_query)) {
        // Registration successful
        $response = [
            'success' => true,
            'redirect' => '../view/login.php'  // Explicitly add redirect URL
        ];
        echo json_encode($response);
        exit();
    }else {
        // Database insertion error
        $response['errors']['general'] = "Registration failed: " . mysqli_error($conn);
        echo json_encode($response);
        exit();
    }
} else {
    // If not a POST request
    $response['errors']['general'] = "Invalid request method";
    echo json_encode($response);
    exit();
}

?>
