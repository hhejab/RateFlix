<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    die("Access denied. Admin only.");
}

$start_date = $_GET["start_date"] ?? "2000-01-01";
$end_date = $_GET["end_date"] ?? date("Y-m-d");

$stmt = mysqli_prepare($conn, "
    SELECT m.title, COUNT(r.rating_id) AS rating_count, IFNULL(AVG(r.rating_value), 0) AS avg_rating
    FROM dbProj_movies m
    LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
    WHERE DATE(m.created_at) BETWEEN ? AND ?
    GROUP BY m.movie_id
    ORDER BY avg_rating DESC, rating_count DESC
");

mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt);
$popular_movies = mysqli_stmt_get_result($stmt);

$creator_movies = mysqli_query($conn, "
    SELECT u.username, COUNT(m.movie_id) AS total_movies
    FROM dbProj_users u
    LEFT JOIN dbProj_movies m ON u.user_id = m.creator_id
    WHERE u.role_id = 2
    GROUP BY u.user_id
    ORDER BY total_movies DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Admin Reports</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="comments.php">Comments</a>
        <a href="movies.php">Movies</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Most Popular Movies Within Date Range</h2>

    <form method="GET" class="search-form">
        <input type="date" name="start_date" value="<?php echo $start_date; ?>">
        <input type="date" name="end_date" value="<?php echo $end_date; ?>">
        <button type="submit">Generate Report</button>
    </form>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>Movie</th>
            <th>Ratings Count</th>
            <th>Average Rating</th>
        </tr>

        <?php while ($movie = mysqli_fetch_assoc($popular_movies)): ?>
            <tr>
                <td><?php echo htmlspecialchars($movie["title"]); ?></td>
                <td><?php echo $movie["rating_count"]; ?></td>
                <td><?php echo number_format($movie["avg_rating"], 1); ?>/5</td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Content Created by Specific Users</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>Creator</th>
            <th>Total Movies</th>
        </tr>

        <?php while ($creator = mysqli_fetch_assoc($creator_movies)): ?>
            <tr>
                <td><?php echo htmlspecialchars($creator["username"]); ?></td>
                <td><?php echo $creator["total_movies"]; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>