<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capture_moment";
$port = "3306";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $service = $_POST["service"];
    $budget = $_POST["budget"];
    $message = $_POST["message"];

    // Prepare SQL query to insert data
    $sql = "INSERT INTO contact (name, email, service, budget, message, regdate)
            VALUES (?, ?, ?, ?, ?, NOW())";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute
    $stmt->bind_param("sssss", $name, $email, $service, $budget, $message);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: test.php?message=Submitted Successfully!");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
</head>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: #333;
}

header {
    background-color: #fff;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: #FF5A5F;
}

.nav-links {
    list-style-type: none;
    margin: 0;
    padding: 10;
    display: flex;
}

.nav-links li {
    margin-left: 150px;
}

.nav-links a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

/* Contact Section Styles */
.contact-section {
    display: flex;
    justify-content: space-between;
    padding: 50px;
    background-color: #fff;
}

/* Contact Information Styles */
.contact-info {
    flex: 1;
    max-width: 40%;
}

.contact-info h1 {
    font-size: 36px;
    margin-bottom: 20px;
}

.contact-info h2 {
    font-size: 24px;
    margin-top: 30px;
}

.contact-info p {
    font-size: 18px;
    margin: 10px 0;
}

.contact-info a {
    color: #333;
    text-decoration: none;
    font-weight: bold;
}

.contact-info a:hover {
 color: #FF5A5F;
}

/* Contact Form Styles */
.contact-form {
    flex: 1;
    max-width: 40%;
}

.contact-form form {
    display: flex;
    flex-direction: column;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.contact-form label {
    font-size: 18px;
    margin-bottom: 10px;
}

.contact-form input, .contact-form select, .contact-form textarea {
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.contact-form button[type="submit"] {
    padding: 10px 20px;
    background-color: #FF5A5F;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.contact-form button[type="submit"]:hover {
    background-color: #FF8A8A;
}
</style>
<body>
    <header>
        <nav>
            <div class="logo">Explore Moments</div>
            <ul class="nav-links">
                <li><a href="home.html">Home</a></li>
                <li><a href="Team.html">Team members</a></li>
                <li><a href="gallery.html">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header> 
    <section class="contact-section">
        <div class="contact-form">
            <h1>Let's Talk</h1>
            <p>Have some big idea or brand to develop and need help? Then reach out, we'd love to hear about your project and provide help.</p>
            <h2>Email</h2>
            <p><a href="mailto:beebs@gmail.com">beebs@gmail.com</a></p>
            <h2>Socials</h2>
            <p><a href="#">Instagram</a></p>
            <p><a href="#">Twitter</a></p>
            <p><a href="#">Facebook</a></p>
        </div>
        <div class="contact-form">
            <form action="test.php" method="post">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                
                <label for="service">What service are you interested in</label>
                <select id="service" name="service" required>
                    <option value="">Select project type</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Graphic Design">Graphic Design</option>
                    <option value="SEO">SEO</option>
                </select>
                
                <label for="budget">Budget</label>
                <select id="budget" name="budget" required>
                    <option value="">Select project budget</option>
                    <option value="Below $1000">Below $1000</option>
                    <option value="$1000 - $5000">$1000 - $5000</option>
                    <option value="Above $5000">Above $5000</option>
                </select>
                
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required></textarea>
                <button type="submit">Submit</button>
            </form>
        </div>
    </section>
</body>
</html>