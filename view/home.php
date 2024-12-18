<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Highlanders FC App</title>
    <link rel="stylesheet" href="styles.css">
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .welcome-message {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .description {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .info {
            font-size: 1rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .buttons {
            display: flex;
            gap: 20px;
        }
        .button {
            text-decoration: none;
            background: #000; /* Black background */
            color: #f1c40f; /* Yellow text to match the theme */
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s, color 0.3s;
        }
        .button:hover {
            background: #333; /* Slightly lighter black for hover effect */
            color: white; /* White text for contrast on hover */
        }

    </style>
</head>
<body>
    <div class="container">
        <img src="../assets/images/Highlanders_logo.png" alt="Highlanders FC Logo" class="logo">
        <div class="welcome-message">Welcome to Highlanders FC</div>
        <div class="description">
            Join us for an unforgettable matchday experience, live updates, player stats, and more!
        </div>
        <div class="info">
            Established in 2018, Highlanders FC is renowned for our attractive brand of football.<br>
            Based at Ashesi University in Berekuso, we are proud to represent our community.<br>
            Our patron, Dr. Milicent Adjei, inspires us to reach new heights on and off the field.
        </div>
        <div class="buttons">
            <a href="login.php" class="button">Login</a>
            <a href="signup.php" class="button">Sign Up</a>
        </div>
    </div>
</body>
</html>
