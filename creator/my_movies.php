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

$creator_id = $_SESSION["user_id"];

$stmt = mysqli_prepare($conn, "
    SELECT m.*, c.category_name
    FROM dbProj_movies m
    JOIN dbProj_categories c ON m.category_id = c.category_id
    WHERE m.creator_id = ?
    ORDER BY m.created_at DESC
");

mysqli_stmt_bind_param($stmt, "i", $creator_id);
mysqli_stmt_execute($stmt);
$movies = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Movies - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Creator Panel</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="add_movie.php">Add Movie</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>My Movies</h2>

    <?php if (mysqli_num_rows($movies) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>

            <?php while ($movie = mysqli_fetch_assoc($movies)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($movie["title"]); ?></td>
                    <td><?php echo htmlspecialchars($movie["category_name"]); ?></td>
                    <td><?php echo htmlspecialchars($movie["status"]); ?></td>
                    <td><?php echo $movie["created_at"]; ?></td>
                    <td>
                        <a href="edit_movie.php?id=<?php echo $movie["movie_id"]; ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You have not added any movies yet.</p>
    <?php endif; ?>
</div>

</body>
</html>