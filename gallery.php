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

// Get the search query and category filter from GET request
$searchQuery = $_GET['search'] ?? ''; // Search term entered by the user
$categoryFilter = $_GET['categoryFilter'] ?? ''; // Category selected by the user
$sortOption = $_GET['sortOption'] ?? 'title ASC'; // Sorting option for images

// Predefined categories
$categories = ["Nature", "Urban", "People", "Abstract", "Architecture"];

// Photographer names for each image ID (from 1 to 25)
$photographers = [
    1 => 'Alice Johnson', 2 => 'Bob Smith', 3 => 'Clara Lee', 4 => 'David Brown', 
    5 => 'Emma Wilson', 6 => 'Frank Harris', 7 => 'Grace Moore', 8 => 'Henry White', 
    9 => 'Isabelle Clark', 10 => 'James Adams', 11 => 'Kelly Scott', 12 => 'Liam Cooper', 
    13 => 'Maria Martinez', 14 => 'Nathan King', 15 => 'Olivia Harris', 16 => 'Peter Young', 
    17 => 'Quinn Taylor', 18 => 'Rachel Brooks', 19 => 'Steven Perez', 20 => 'Tina Lee', 
    21 => 'Uriel Carter', 22 => 'Victoria Gray', 23 => 'William Harris', 24 => 'Xander Miller', 
    25 => 'Yara Jenkins'
];

// Query to fetch all categories with at least one image
$categorySql = "SELECT DISTINCT category FROM gallery";
$categoryResult = $conn->query($categorySql);
$validCategories = [];
if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $validCategories[] = $row['category'];
    }
}

// Add "All Categories" option at the beginning
array_unshift($validCategories, "All Categories");

// Start building the SQL query for the gallery display
$sql = "SELECT * FROM gallery WHERE title LIKE ?"; // Search by title

// Apply category filter if a category is selected
if (!empty($categoryFilter) && $categoryFilter != 'All Categories') {
    $sql .= " AND category = ?";
}

// Apply sorting
$sql .= " ORDER BY $sortOption";

// Prepare and execute the SQL query
$stmt = $conn->prepare($sql);

// Bind parameters for the search query and category filter
$likeSearchQuery = '%' . $searchQuery . '%'; // Add wildcards to search query

if (!empty($categoryFilter) && $categoryFilter != 'All Categories') {
    $stmt->bind_param("ss", $likeSearchQuery, $categoryFilter); // Bind both search query and category
} else {
    $stmt->bind_param("s", $likeSearchQuery); // Bind only the search query if no category filter
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="gallery.css">
    <script src="gallery.js"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Explore Moments</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="team.php">Team Members</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero-about">
        <h1>Welcome To Our Gallery Page</h1>
        <p>Captured by our creative Team Members</p>
    </section>

    <section class="gallery-section">
        <div class="gallery-controls">
            <form method="GET" action="gallery.php">
                <input type="text" name="search" placeholder="Search photos..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <select name="categoryFilter">
                    <?php foreach ($validCategories as $category): ?>
                        <option value="<?php echo $category; ?>" <?php echo $categoryFilter == $category ? 'selected' : ''; ?>>
                            <?php echo $category; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="sortOption">
                    <option value="title ASC" <?php echo $sortOption == 'title ASC' ? 'selected' : ''; ?>>Title (A-Z)</option>
                    <option value="title DESC" <?php echo $sortOption == 'title DESC' ? 'selected' : ''; ?>>Title (Z-A)</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <h2>Explore Our Gallery</h2>
        <div class="gallery">
            <!-- Loop through images fetched from database -->
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="gallery-item" data-id="<?php echo $row['id']; ?>">
                        <!-- Assuming images are stored in the 'uploads' directory -->
                        <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" class="gallery-image">
                        <div class="image-info">
                            <h3><?php echo $row['title']; ?></h3>
                            <p>Category: <?php echo $row['category']; ?></p>
                            <p>Photographer: <?php echo $photographers[$row['id']] ?? 'Unknown'; ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No images found for the selected category or search term.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>Capture Moments, By Anchal_Pandey.</p>
    </footer>
</body>
</html>
