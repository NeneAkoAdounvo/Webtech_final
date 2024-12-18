<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Highlanders FC</title>
    <link rel="stylesheet" href="../assets/css/SignUpstyles.css">
</head>
<body>
    <div class="container">
        <div class="title">Become a God merchant</div>
        <form id="signup-form" action="../actions/register_user.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="button">Sign Up</button>
        </form>
        <div id="error-messages"></div>

        <a href="login.php" class="link">Already have an account? Login</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#signup-form').on('submit', function(e) {
                e.preventDefault();  // Prevent default form submission
    
                $.ajax({
                    type: 'POST',
                    url: '../actions/register_user.php',
                    data: $(this).serialize(),  // Serialize form data
                    dataType: 'json',  // Expect JSON response
                    success: function(response) {
    if (response.success) {
        // Check if a redirect URL is provided
        if (response.redirect) {
            window.location.href = response.redirect;
        } else {
            // Fallback redirect
            window.location.href = "login.php";
        }
    } else {
        // Display validation errors
        displayErrors(response.errors);
    }
},
error: function(xhr, status, error) {
    console.error("AJAX error:", xhr.responseText);
    $('#error-messages').html('<p>An unexpected error occurred. Please try again.</p>');
}
                });
            });
    
            function displayErrors(errors) {
                $('#error-messages').html(''); // Clear previous errors
    
                // Loop through each error and display it
                for (var key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        var errorMessage = errors[key];
                        $('#error-messages').append('<p>' + errorMessage + '</p>');
                    }
                }
            }
        });
    </script>
</body>
</html>