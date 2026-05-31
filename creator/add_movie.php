<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["role_id"] != 2 && $_SESSION["role_id"] != 1) {
    die("Access denied. Creator only.");
}

$error = "";
$success = "";

$categories = mysqli_query($conn, "SELECT * FROM dbProj_categories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $creator_id = $_SESSION["user_id"];
    $category_id = $_POST["category_id"];
    $title = trim($_POST["title"]);
    $short_description = trim($_POST["short_description"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    $poster_image = "";

    if (!empty($_FILES["poster_image"]["name"])) {
        $poster_image = time() . "_" . basename($_FILES["poster_image"]["name"]);
        $target = "../uploads/" . $poster_image;
        move_uploaded_file($_FILES["poster_image"]["tmp_name"], $target);
    }

    if (empty($title) || empty($short_description) || empty($description)) {
        $error = "All text fields are required.";
    } else {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO dbProj_movies
            (creator_id, category_id, title, short_description, description, poster_image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "iisssss",
            $creator_id,
            $category_id,
            $title,
            $short_description,
            $description,
            $poster_image,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Movie added successfully.";
        } else {
            $error = "Failed to add movie.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Movie - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Creator Panel</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="my_movies.php">My Movies</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Add New Movie</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Category:</label><br>
        <select name="category_id" required>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $category["category_id"]; ?>">
                    <?php echo htmlspecialchars($category["category_name"]); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Short Description:</label><br>
        <input type="text" name="short_description" required><br><br>

        <label>Full Description:</label><br>
        <textarea name="description" rows="6" required></textarea><br><br>

        <label>Poster Image:</label><br>
        <input type="file" name="poster_image"><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select><br><br>

        <button type="submit">Add Movie</button>
    </form>
</div>

</body>
</html>