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

    // Check if the username exists in the database
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        
        // Verify the password (assuming the password is stored as a hash)
        if (password_verify($admin_password, $user['password'])) {
            // Password is correct, set up session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin_username;
            header("Location: admin.php"); // Redirect to admin page on successful login
            exit();
        } else {
            $error_message = "Invalid username or password";
        }
    } else {
        $error_message = "Invalid username or password";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Admin Login</h2>
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
</body>
</html>
