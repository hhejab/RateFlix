<?php
session_start();
include 'config.php';

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$movie_id = intval($_GET["id"]);
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];

    if (isset($_POST["rating_value"])) {
        $rating_value = intval($_POST["rating_value"]);

        $stmt = mysqli_prepare($conn, "
            INSERT INTO dbProj_ratings (movie_id, user_id, rating_value)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE rating_value = VALUES(rating_value)
        ");
        mysqli_stmt_bind_param($stmt, "iii", $movie_id, $user_id, $rating_value);
        mysqli_stmt_execute($stmt);

        $message = "Rating submitted.";
    }

    if (isset($_POST["comment_text"])) {
        $comment_text = trim($_POST["comment_text"]);

        if (!empty($comment_text)) {
            $stmt = mysqli_prepare($conn, "
                INSERT INTO dbProj_comments (movie_id, user_id, comment_text)
                VALUES (?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt, "iis", $movie_id, $user_id, $comment_text);
            mysqli_stmt_execute($stmt);

            $message = "Comment added.";
        }
    }
}

$stmt = mysqli_prepare($conn, "
    SELECT m.*, c.category_name, u.username,
    IFNULL(AVG(r.rating_value), 0) AS avg_rating
    FROM dbProj_movies m
    JOIN dbProj_categories c ON m.category_id = c.category_id
    JOIN dbProj_users u ON m.creator_id = u.user_id
    LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
    WHERE m.movie_id = ? AND m.status = 'published'
    GROUP BY m.movie_id
");

mysqli_stmt_bind_param($stmt, "i", $movie_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$movie = mysqli_fetch_assoc($result);

if (!$movie) {
    die("Movie not found.");
}

$comments_stmt = mysqli_prepare($conn, "
    SELECT c.comment_text, c.created_at, u.username
    FROM dbProj_comments c
    JOIN dbProj_users u ON c.user_id = u.user_id
    WHERE c.movie_id = ?
    ORDER BY c.created_at DESC
");
mysqli_stmt_bind_param($comments_stmt, "i", $movie_id);
mysqli_stmt_execute($comments_stmt);
$comments = mysqli_stmt_get_result($comments_stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($movie["title"]); ?> - RateFlix</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1>RateFlix</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">

    <?php if ($message): ?>
        <p style="color:green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="movie-detail">
        <div class="poster large">
            <?php if (!empty($movie["poster_image"])): ?>
                <img src="uploads/<?php echo htmlspecialchars($movie["poster_image"]); ?>">
            <?php else: ?>
                <div class="placeholder">No Image</div>
            <?php endif; ?>
        </div>

        <div>
            <h2><?php echo htmlspecialchars($movie["title"]); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($movie["category_name"]); ?></p>
            <p><strong>Creator:</strong> <?php echo htmlspecialchars($movie["username"]); ?></p>
            <p><strong>Average Rating:</strong> <?php echo number_format($movie["avg_rating"], 1); ?>/5</p>
            <p><?php echo nl2br(htmlspecialchars($movie["description"])); ?></p>
        </div>
    </div>

    <h3>Rate this movie</h3>

    <?php if (isset($_SESSION["user_id"])): ?>
        <form method="POST">
            <select name="rating_value" required>
                <option value="">Select rating</option>
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Good</option>
                <option value="3">3 - Average</option>
                <option value="2">2 - Poor</option>
                <option value="1">1 - Bad</option>
            </select>
            <button type="submit">Submit Rating</button>
        </form>

        <h3>Add Comment</h3>
        <form method="POST">
            <textarea name="comment_text" rows="4" required></textarea><br><br>
            <button type="submit">Add Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to rate or comment.</p>
    <?php endif; ?>

    <h3>Comments</h3>

    <?php while ($comment = mysqli_fetch_assoc($comments)): ?>
        <div class="comment">
            <strong><?php echo htmlspecialchars($comment["username"]); ?></strong>
            <p><?php echo htmlspecialchars($comment["comment_text"]); ?></p>
            <small><?php echo $comment["created_at"]; ?></small>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>