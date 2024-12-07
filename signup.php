<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php"); // Redirect to admin page if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "capture_moment";
    $port = "3306";

    // Database connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    // Sanitize inputs
    $admin_username = mysqli_real_escape_string($conn, $admin_username);
    $admin_password = mysqli_real_escape_string($conn, $admin_password);

    // Check if the username already exists
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Username already exists!";
    } else {
        // Insert the new admin user
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $admin_username, $admin_password);
        if ($stmt->execute()) {
            header("Location: login.php"); // Redirect to login page after successful sign-up
            exit();
        } else {
            $error_message = "There was an error signing up. Please try again.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Admin Sign Up</h2>
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Sign Up">
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
