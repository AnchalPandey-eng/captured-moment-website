<?php
session_start();
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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $bio = $_POST['bio'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $image = NULL; // Default to NULL for image if none is uploaded

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/"; // Folder to store uploaded images

        // Create the directory if it does not exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory with permissions
        }

        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Limit the file types (only allow jpg, png, jpeg, gif)
            if ($image_type == "jpg" || $image_type == "png" || $image_type == "jpeg" || $image_type == "gif") {
                // Check if file already exists
                if (!file_exists($target_file)) {
                    // Upload image
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image = $target_file; // Set the image path
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                } else {
                    echo "Sorry, file already exists.";
                }
            } else {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // If editing an existing team member
    if (isset($_POST['id']) && $_POST['id'] != "") {
        $id = $_POST['id'];

        // Modify the query and bind the parameters dynamically based on the presence of an image
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
        // Add New Team Member
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

// Handle Deletion of Team Member
if (isset($_GET['delete'])) {
    // Only allow deletion by admin or team member itself
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {
        $id = $_GET['delete'];
        $sql = "DELETE FROM team_members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else if (isset($_SESSION['user_id'])) {
        // Allow team members to delete only their own data
        $user_id = $_SESSION['user_id'];
        $id = $_GET['delete'];
        if ($user_id == $id) {
            $sql = "DELETE FROM team_members WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
}

// Fetch Team Members
$sql = "SELECT * FROM team_members";
$result = $conn->query($sql);

// Fetch Team Member data for editing (if any)
$editData = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM team_members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editResult = $stmt->get_result();
    $editData = $editResult->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team Members</title>
    <link rel="stylesheet" href="team.css">
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

    <section class="team-section">
        <h1>Our Team</h1>
        <p>We are the people who make up Company Name</p>
        <div class="team-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="team-member">
                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="clickable" onclick="openModal('<?php echo $row['image']; ?>')">
                    <h3><?php echo $row['name']; ?></h3>
                    <p><strong>Role:</strong> <?php echo $row['role']; ?></p>
                    <p><strong>Bio:</strong> <?php echo $row['bio']; ?></p>
                    <p><strong>Skills:</strong> <?php echo $row['skills']; ?></p>
                    <p><strong>Experience:</strong> <?php echo $row['experience']; ?></p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id'] || isset($_SESSION['admin_logged_in'])): ?>
                        <a href="team.php?edit=<?php echo $row['id']; ?>"><button>Edit</button></a>
                        <a href="team.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this team member?')"><button>Delete</button></a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="admin-section">
        <h2>Manage Your Information</h2>
        <form method="POST" action="team.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo isset($editData['id']) ? $editData['id'] : ''; ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo isset($editData['name']) ? $editData['name'] : ''; ?>" required>

            <label for="role">Role:</label>
            <input type="text" name="role" value="<?php echo isset($editData['role']) ? $editData['role'] : ''; ?>" required>

            <label for="bio">Bio:</label>
            <textarea name="bio" required><?php echo isset($editData['bio']) ? $editData['bio'] : ''; ?></textarea>

            <label for="skills">Skills:</label>
            <textarea name="skills" required><?php echo isset($editData['skills']) ? $editData['skills'] : ''; ?></textarea>

            <label for="experience">Experience:</label>
            <textarea name="experience" required><?php echo isset($editData['experience']) ? $editData['experience'] : ''; ?></textarea>

            <label for="image">Image:</label>
            <input type="file" name="image">

            <button type="submit">Save</button>
        </form>
    </section>

    <script>
        function openModal(imageSrc) {
            var modal = document.createElement('div');
            modal.className = 'modal';
            var img = document.createElement('img');
            img.src = imageSrc;
            modal.appendChild(img);
            document.body.appendChild(modal);
            modal.onclick = function() {
                modal.remove();
            };
        }
    </script>

</body>
</html>

