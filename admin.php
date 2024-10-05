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

// Fetch contact submissions from the database
$sql = "SELECT * FROM contact ORDER BY regdate DESC";
$result = $conn->query($sql);

// Add new submission
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_POST['service'];
    $budget = $_POST['budget'];
    $message = $_POST['message'];

    $sql = "INSERT INTO contact (name, email, service, budget, message, regdate) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $service, $budget, $message);
    $stmt->execute();
    header("Location: admin.php?message=Submission added successfully!");
    exit();
}

// Edit submission
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM contact WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

// Update submission
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_POST['service'];
    $budget = $_POST['budget'];
    $message = $_POST['message'];

    $sql = "UPDATE contact SET name = ?, email = ?, service = ?, budget = ?, message = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $email, $service, $budget, $message, $id);
    $stmt->execute();
    header("Location: admin.php?message=Submission updated successfully!");
    exit();
}

// Delete submission
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM contact WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin.php?message=Submission deleted successfully!");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Contact Submissions</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<h2>Contact Submissions</h2>

<?php
// Display messages
if (isset($_GET['message'])) {
    echo "<p>" . $_GET['message'] . "</p>";
}
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Service</th>
            <th>Budget</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["service"] . "</td>";
                echo "<td>" . $row["budget"] . "</td>";
                echo "<td>" . $row["message"] . "</td>";
                echo "<td>" . $row["regdate"] . "</td>";
                echo "<td><a href='admin.php?edit=" . $row["id"] . "'>Edit</ a> | <a href='admin.php?delete=" . $row["id"] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No submissions found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
if (isset($_GET['edit'])) {
    ?>
    <h2>Edit Submission</h2>
    <form action="admin.php" method="post">
        <input type="hidden" name="id" value="<?php echo $_GET['edit']; ?>">
        <?php
        $sql = "SELECT * FROM contact WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_GET['edit']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        ?>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>"><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>"><br><br>
        <label for="service">Service:</label>
        <select id="service" name="service">
            <option value="<?php echo $row['service']; ?>"><?php echo $row['service']; ?></option>
            <option value="Web Development">Web Development</option>
            <option value="Graphic Design">Graphic Design</option>
            <option value="SEO">SEO</option>
        </select><br><br>
        <label for="budget">Budget:</label>
        <select id="budget" name="budget">
            <option value="<?php echo $row['budget']; ?>"><?php echo $row['budget']; ?></option>
            <option value="Below $1000">Below $1000</option>
            <option value="$1000 - $5000">$1000 - $5000</option>
            <option value="Above $5000">Above $5000</option>
        </select><br><br>
        <label for="message">Message:</label>
        <textarea id="message" name="message"><?php echo $row['message']; ?></textarea><br><br>
        <input type="submit" name="update" value="Update">
    </form>
    <?php
} else {
    ?>
    <h2>Add New Submission</h2>
    <form action="admin.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name"><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br><br>
        <label for="service">Service:</label>
        <select id="service" name="service">
            <option value="">Select service</option>
            <option value="Web Development">Web Development</option>
            <option value="Graphic Design">Graphic Design</option>
            <option value="SEO">SEO</option>
        </select><br><br>
        <label for="budget">Budget:</label>
        <select id="budget" name="budget">
            <option value="">Select budget</option>
            <option value="Below $1000">Below $1000</option>
            <option value="$1000 - $5000">$1000 - $5000</option>
            <option value="Above $5000">Above $5000</option>
        </select><br><br>
        <label for="message">Message:</label>
        <textarea id="message" name="message"></textarea><br><br>
        <input type="submit" name="add" value="Add">
    </form>
    <?php
}
?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>