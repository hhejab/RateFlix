<?php
include 'config.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $role_id = 3;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO dbProj_users (role_id, username, email, password_hash) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $role_id, $username, $email, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Account created successfully. You can now login.";
        } else {
            $error = "Username or email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - RateFlix</title>
</head>
<body>

<h1>RateFlix Sign Up</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" onsubmit="return validateSignup();">
    <label>Username:</label><br>
    <input type="text" name="username" id="username"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" id="email"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" id="password"><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password" id="confirm_password"><br><br>

    <button type="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>
<p><a href="index.php">Back to Home</a></p>

<script>
function validateSignup() {
    let username = document.getElementById("username").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm_password").value;

    if (username === "" || email === "" || password === "" || confirmPassword === "") {
        alert("All fields are required.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
}
</script>

</body>
</html>