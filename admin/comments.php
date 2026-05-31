<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    die("Access denied. Admin only.");
}

if (isset($_GET["delete"])) {
    $comment_id = intval($_GET["delete"]);

    $stmt = mysqli_prepare($conn, "DELETE FROM dbProj_comments WHERE comment_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);

    header("Location: comments.php");
    exit();
}

$comments = mysqli_query($conn, "
    SELECT c.comment_id, c.comment_text, c.created_at, u.username, m.title
    FROM dbProj_comments c
    JOIN dbProj_users u ON c.user_id = u.user_id
    JOIN dbProj_movies m ON c.movie_id = m.movie_id
    ORDER BY c.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Comments - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Manage Comments</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="reports.php">Reports</a>
        <a href="movies.php">Movies</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>All Comments</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>Movie</th>
            <th>User</th>
            <th>Comment</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php while ($comment = mysqli_fetch_assoc($comments)): ?>
            <tr>
                <td><?php echo htmlspecialchars($comment["title"]); ?></td>
                <td><?php echo htmlspecialchars($comment["username"]); ?></td>
                <td><?php echo htmlspecialchars($comment["comment_text"]); ?></td>
                <td><?php echo $comment["created_at"]; ?></td>
                <td>
                    <a href="comments.php?delete=<?php echo $comment["comment_id"]; ?>" onclick="return confirm('Delete this comment?');">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>