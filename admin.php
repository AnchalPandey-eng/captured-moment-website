<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capture_moment";
$port = "3306";
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add/Edit/Delete Team Member
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['team_member'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $bio = $_POST['bio'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $image = NULL; // Default to NULL for image if none is uploaded

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/"; // Folder to store uploaded images
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if ($image_type == "jpg" || $image_type == "png" || $image_type == "jpeg" || $image_type == "gif") {
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image = $target_file; // Set the image path
                    }
                }
            }
        }
    }

    // Check if we are editing or adding
    if (isset($_POST['id']) && $_POST['id'] != "") {
        // Edit existing team member
        $id = $_POST['id'];
        if ($image === NULL) {
            $sql = "UPDATE team_members SET name = ?, role = ?, bio = ?, skills = ?, experience = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $name, $role, $bio, $skills, $experience, $id);
        } else {
            $sql = "UPDATE team_members SET name = ?, role = ?, bio = ?, skills = ?, experience = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $name, $role, $bio, $skills, $experience, $image, $id);
        }
    } else {
        // Add new team member
        if ($image === NULL) {
            $sql = "INSERT INTO team_members (name, role, bio, skills, experience) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $role, $bio, $skills, $experience);
        } else {
            $sql = "INSERT INTO team_members (name, role, bio, skills, experience, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $role, $bio, $skills, $experience, $image);
        }
    }
    $stmt->execute();
}

// Handle Delete Team Member
if (isset($_GET['delete_team'])) {
    $id = $_GET['delete_team'];

    // Delete the image if it exists
    $delete_sql = "SELECT image FROM team_members WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $team_member = $result->fetch_assoc();

    // Check if image file exists and delete it
    if ($team_member['image'] && file_exists($team_member['image'])) {
        unlink($team_member['image']); // Delete the image file
    }

    // Delete team member record from database
    $delete_sql = "DELETE FROM team_members WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redirect back to the same page after deletion
    header("Location: admin.php");
    exit();
}

// Fetch Team Members
$team_sql = "SELECT * FROM team_members";
$team_result = $conn->query($team_sql);

// Handle Edit Team Member
if (isset($_GET['edit_team'])) {
    $id = $_GET['edit_team'];
    $edit_sql = "SELECT * FROM team_members WHERE id = ?";
    $stmt = $conn->prepare($edit_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_team = $edit_result->fetch_assoc();
}


// Contact Management: Handle Add/Edit/Delete Contact
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_POST['service'];
    $budget = $_POST['budget'];
    $message = $_POST['message'];

    if (isset($_POST['id']) && $_POST['id'] != "") {
        // Edit existing contact
        $id = $_POST['id'];
        $sql = "UPDATE contacts SET name = ?, email = ?, service = ?, budget = ?, message = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $service, $budget, $message, $id);
    } else {
        // Add new contact
        $sql = "INSERT INTO contacts (name, email, service, budget, message, regdate) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $service, $budget, $message);
    }
    $stmt->execute();
}

// Handle Delete Contact
if (isset($_GET['delete_contact'])) {
    $id = $_GET['delete_contact'];
    $sql = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // Redirect back to the same page after deletion
    header("Location: admin.php");
    exit();
}

// Fetch Contacts
$contact_sql = "SELECT * FROM contacts ORDER BY regdate DESC";
$contact_result = $conn->query($contact_sql);

// Handle Edit Contact
if (isset($_GET['edit_contact'])) {
    $id = $_GET['edit_contact'];
    $edit_contact_sql = "SELECT * FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($edit_contact_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_contact_result = $stmt->get_result();
    $edit_contact = $edit_contact_result->fetch_assoc();
}
// Predefined categories
$categories = ["jpeg", "jpg", "photo"];

// Handle Add/Edit/Delete requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'add') {
            $title = $_POST['title'];
            $category = $_POST['category'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $imageName = $_FILES['image']['name'];
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($imageName);

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $stmt = $conn->prepare("INSERT INTO gallery (title, category, image) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $title, $category, $imageName);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Image upload error.";
            }
        } elseif ($action === 'edit') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $category = $_POST['category'];

            // Check if a new image is uploaded
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $imageName = $_FILES['image']['name'];
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($imageName);

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $stmt = $conn->prepare("UPDATE gallery SET title = ?, category = ?, image = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $title, $category, $imageName, $id);
                }
            } else {
                $stmt = $conn->prepare("UPDATE gallery SET title = ?, category = ? WHERE id = ?");
                $stmt->bind_param("ssi", $title, $category, $id);
            }

            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'delete') {
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch gallery items
$sql = "SELECT * FROM gallery";
$result = $conn->query($sql);

// Handle Edit Gallery Item
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_sql = "SELECT * FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($edit_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_gallery_item = $edit_result->fetch_assoc();
}
?>>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Team Members</title>
    <link rel="stylesheet" href="admin.css">
    <script src="admin.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Explore Moments</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="team.php">Team</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <!-- Manage Team Members -->
        <h1>Manage Team Members</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo isset($edit_team) ? $edit_team['id'] : ''; ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo isset($edit_team) ? $edit_team['name'] : ''; ?>" required><br><br>
            
            <label for="role">Role:</label>
            <input type="text" name="role" value="<?php echo isset($edit_team) ? $edit_team['role'] : ''; ?>" required><br><br>
            
            <label for="bio">Bio:</label>
            <textarea name="bio" required><?php echo isset($edit_team) ? $edit_team['bio'] : ''; ?></textarea><br><br>
            
            <label for="skills">Skills:</label>
            <input type="text" name="skills" value="<?php echo isset($edit_team) ? $edit_team['skills'] : ''; ?>" required><br><br>
            
            <label for="experience">Experience:</label>
            <input type="text" name="experience" value="<?php echo isset($edit_team) ? $edit_team['experience'] : ''; ?>" required><br><br>
            
            <label for="image">Image:</label>
            <?php if (isset($edit_team) && $edit_team['image']): ?>
                <img src="<?php echo $edit_team['image']; ?>" width="100" alt="Team Member Image"><br><br>
                <label for="image">Change Image:</label>
            <?php endif; ?>
            <input type="file" name="image"><br><br>

            <button type="submit" name="team_member">Save Team Member</button>
        </form>

        <h2>Team Members List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Bio</th>
                <th>Skills</th>
                <th>Experience</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $team_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td><?php echo $row['bio']; ?></td>
                    <td><?php echo $row['skills']; ?></td>
                    <td><?php echo $row['experience']; ?></td>
                    <td><img src="<?php echo $row['image']; ?>" width="100" alt="Team Member Image"></td>
                    <td>
                        <a href="admin.php?edit_team=<?php echo $row['id']; ?>">Edit</a> | 
                        <a href="admin.php?delete_team=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this team member?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Manage Contacts -->
        <h1>Manage Contacts</h1>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo isset($edit_contact) ? $edit_contact['id'] : ''; ?>">

            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo isset($edit_contact) ? $edit_contact['name'] : ''; ?>" required><br><br>
            
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo isset($edit_contact) ? $edit_contact['email'] : ''; ?>" required><br><br>
            
            <label for="service">Service:</label>
            <input type="text" name="service" value="<?php echo isset($edit_contact) ? $edit_contact['service'] : ''; ?>" required><br><br>
            
            <label for="budget">Budget:</label>
            <input type="text" name="budget" value="<?php echo isset($edit_contact) ? $edit_contact['budget'] : ''; ?>" required><br><br>
            
            <label for="message">Message:</label>
            <textarea name="message" required><?php echo isset($edit_contact) ? $edit_contact['message'] : ''; ?></textarea><br><br>

            <button type="submit" name="contact">Save Contact</button>
        </form>

        <h2>Contacts List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Service</th>
                <th>Budget</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $contact_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['service']; ?></td>
                    <td><?php echo $row['budget']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                    <td>
                        <a href="admin.php?edit_contact=<?php echo $row['id']; ?>">Edit</a> | 
                        <a href="admin.php?delete_contact=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this contact?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <h1>Gallery Management</h1>
        <section>
        <?php if (isset($edit_gallery_item)): ?>
            <h3>Edit Gallery Item</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $edit_gallery_item['id']; ?>">
                <label for="title">Title:</label>
                <input type="text" name="title" value="<?php echo $edit_gallery_item['title']; ?>" required>
                <label for="category">Category:</label>
                <select name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category; ?>" <?php echo ($edit_gallery_item['category'] === $category) ? 'selected' : ''; ?>>
                            <?php echo $category; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="image">Image:</label>
                <input type="file" name="image">
                <p>Current Image: <img src="uploads/<?php echo $edit_gallery_item['image']; ?>" alt="<?php echo $edit_gallery_item['title']; ?>" width="100"></p>
                <button type="submit">Save Changes</button>
            </form>
        <?php else: ?>
            <h3>Add New Item</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <label for="title">Title:</label>
                <input type="text" name="title" required>
                <label for="category">Category:</label>
                <select name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="image">Image:</label>
                <input type="file" name="image" required>
                <button type="submit">Add Item</button>
            </form>
        <?php endif; ?>
    </section>

    <section>
        <h3>Gallery Items</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" width="100"></td>
                        <td>
                            <!-- Edit Button -->
                            <a href="admin.php?edit=<?php echo $row['id']; ?>">Edit</a>
                            
                            <!-- Delete Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</body>
</html>