<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role_id"] != 1) {
    die("Access denied. Admin only.");
}

$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbProj_users"))["total"];
$movies = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbProj_movies"))["total"];
$comments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbProj_comments"))["total"];
$ratings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM dbProj_ratings"))["total"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - RateFlix</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Admin Panel</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="users.php">Users</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>System Overview</h2>

    <p>Total Users: <?php echo $users; ?></p>
    <p>Total Movies: <?php echo $movies; ?></p>
    <p>Total Comments: <?php echo $comments; ?></p>
    <p>Total Ratings: <?php echo $ratings; ?></p>
</div>

</body>
</html>