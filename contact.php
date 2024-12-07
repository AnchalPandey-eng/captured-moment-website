<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capture_moment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

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
    $sql = "INSERT INTO contacts (name, email, service, budget, message, regdate)
            VALUES (?, ?, ?, ?, ?, NOW())";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute
    $stmt->bind_param("sssss", $name, $email, $service, $budget, $message);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: contact.php?message=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Handle delete functionality
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: contact.php?message=deleted");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle edit functionality
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all contacts for display
$sql = "SELECT * FROM contacts ORDER BY regdate DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Explore Moments</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="team.php">Team members</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <section class="contact-section">
        <div class="contact-form">
            <h1>Let's Talk</h1>
            <form action="contact.php" method="post" onsubmit="return validateForm(event);">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($contact) ? $contact['name'] : ''; ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($contact) ? $contact['email'] : ''; ?>">

                <label for="service">What service are you interested in</label>
                <select id="service" name="service" required>
                    <option value="Web Development" <?php echo isset($contact) && $contact['service'] == 'Web Development' ? 'selected' : ''; ?>>Web Development</option>
                    <option value="Graphic Design" <?php echo isset($contact) && $contact['service'] == 'Graphic Design' ? 'selected' : ''; ?>>Graphic Design</option>
                    <option value="SEO" <?php echo isset($contact) && $contact['service'] == 'SEO' ? 'selected' : ''; ?>>SEO</option>
                </select>

                <label for="budget">Budget</label>
                <select id="budget" name="budget" required>
                    <option value="Below $1000" <?php echo isset($contact) && $contact['budget'] == 'Below $1000' ? 'selected' : ''; ?>>Below $1000</option>
                    <option value="$1000 - $5000" <?php echo isset($contact) && $contact['budget'] == '$1000 - $5000' ? 'selected' : ''; ?>>$1000 - $5000</option>
                    <option value="Above $5000" <?php echo isset($contact) && $contact['budget'] == 'Above $5000' ? 'selected' : ''; ?>>Above $5000</option>
                </select>

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required><?php echo isset($contact) ? $contact['message'] : ''; ?></textarea>

                <button type="submit"><?php echo isset($contact) ? 'Update' : 'Submit'; ?></button>
            </form>
        </div>

        <?php if (isset($_GET['message']) && $_GET['message'] == 'success'): ?>
            <script>alert("Information submitted successfully!");</script>
        <?php endif; ?>

        <?php if (isset($_GET['message']) && $_GET['message'] == 'deleted'): ?>
            <script>alert("Contact deleted successfully!");</script>
        <?php endif; ?>

        <!-- Display Submitted Contacts -->
        <div class="contact-info">
            <h2>Contact Entries</h2>
            <div class="contact-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="contact-item">
                        <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                        <p><strong>Service:</strong> <?php echo $row['service']; ?></p>
                        <p><strong>Budget:</strong> <?php echo $row['budget']; ?></p>
                        <p><strong>Message:</strong> <?php echo $row['message']; ?></p>
                        <a href="contact.php?edit=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="contact.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <script>
        function validateForm(event) {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;

            if (!name || !email || !message) {
                alert('Please fill all the required fields.');
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
