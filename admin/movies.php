<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    die("Access denied. Admin only.");
}

if (isset($_GET["remove"])) {
    $movie_id = intval($_GET["remove"]);
    $message = "Removed by administrator.";

    $stmt = mysqli_prepare($conn, "
        UPDATE dbProj_movies
        SET status = 'removed', removal_message = ?
        WHERE movie_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "si", $message, $movie_id);
    mysqli_stmt_execute($stmt);

    header("Location: movies.php");
    exit();
}

$movies = mysqli_query($conn, "
    SELECT m.*, u.username, c.category_name
    FROM dbProj_movies m
    JOIN dbProj_users u ON m.creator_id = u.user_id
    JOIN dbProj_categories c ON m.category_id = c.category_id
    ORDER BY m.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Movies - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Manage Movies</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="reports.php">Reports</a>
        <a href="comments.php">Comments</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>All Movies</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>Title</th>
            <th>Creator</th>
            <th>Category</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($movie = mysqli_fetch_assoc($movies)): ?>
            <tr>
                <td><?php echo htmlspecialchars($movie["title"]); ?></td>
                <td><?php echo htmlspecialchars($movie["username"]); ?></td>
                <td><?php echo htmlspecialchars($movie["category_name"]); ?></td>
                <td><?php echo htmlspecialchars($movie["status"]); ?></td>
                <td>
                    <?php if ($movie["status"] != "removed"): ?>
                        <a href="movies.php?remove=<?php echo $movie["movie_id"]; ?>" onclick="return confirm('Remove this movie?');">
                            Remove
                        </a>
                    <?php else: ?>
                        Removed
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>