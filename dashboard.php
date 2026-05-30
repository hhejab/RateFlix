<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$roleName = "Viewer";

if ($_SESSION["role_id"] == 1) {
    $roleName = "Admin";
} elseif ($_SESSION["role_id"] == 2) {
    $roleName = "Creator";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - RateFlix</title>
</head>
<body>

<h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>

<p>You logged in successfully.</p>

<p>Your role: <?php echo $roleName; ?></p>

<p><a href="index.php">Home</a></p>
<p><a href="logout.php">Logout</a></p>

</body>
</html>