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

if (!isset($_GET["id"])) {
    header("Location: my_movies.php");
    exit();
}

$movie_id = intval($_GET["id"]);
$creator_id = $_SESSION["user_id"];

$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM dbProj_movies
    WHERE movie_id = ? AND creator_id = ?
");

mysqli_stmt_bind_param($stmt, "ii", $movie_id, $creator_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$movie = mysqli_fetch_assoc($result);

if (!$movie) {
    die("Movie not found or access denied.");
}

$categories = mysqli_query($conn, "SELECT * FROM dbProj_categories");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST["category_id"];
    $title = trim($_POST["title"]);
    $short_description = trim($_POST["short_description"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    if (empty($title) || empty($short_description) || empty($description)) {
        $error = "All text fields are required.";
    } else {
        $stmt = mysqli_prepare($conn, "
            UPDATE dbProj_movies
            SET category_id = ?, title = ?, short_description = ?, description = ?, status = ?
            WHERE movie_id = ? AND creator_id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "issssii",
            $category_id,
            $title,
            $short_description,
            $description,
            $status,
            $movie_id,
            $creator_id
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Movie updated successfully.";

            $movie["category_id"] = $category_id;
            $movie["title"] = $title;
            $movie["short_description"] = $short_description;
            $movie["description"] = $description;
            $movie["status"] = $status;
        } else {
            $error = "Failed to update movie.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Movie - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Creator Panel</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="my_movies.php">My Movies</a>
        <a href="add_movie.php">Add Movie</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Edit Movie</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($movie["title"]); ?>" required><br><br>

        <label>Category:</label><br>
        <select name="category_id" required>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $category["category_id"]; ?>"
                    <?php if ($category["category_id"] == $movie["category_id"]) echo "selected"; ?>>
                    <?php echo htmlspecialchars($category["category_name"]); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Short Description:</label><br>
        <input type="text" name="short_description" value="<?php echo htmlspecialchars($movie["short_description"]); ?>" required><br><br>

        <label>Full Description:</label><br>
        <textarea name="description" rows="6" required><?php echo htmlspecialchars($movie["description"]); ?></textarea><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="draft" <?php if ($movie["status"] == "draft") echo "selected"; ?>>Draft</option>
            <option value="published" <?php if ($movie["status"] == "published") echo "selected"; ?>>Published</option>
        </select><br><br>

        <button type="submit">Update Movie</button>
    </form>
</div>

</body>
</html>