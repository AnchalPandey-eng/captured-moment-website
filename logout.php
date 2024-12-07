<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Destroy the session and log the user out
    session_unset();
    session_destroy();

    // Redirect to login page after logging out
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="logout-container">
        <h2>Are you sure you want to log out?</h2>
        <form method="POST" action="">
            <input type="submit" value="Yes, Log Out">
        </form>
        <a href="admin.php">Cancel</a>
    </div>
</body>
</html>
