<?php
session_start();
include '../include/connection.php';

if (!isset($_SESSION['email'])) {
    header("Location: forget_pass.php");
    exit();
}

$email = $_SESSION['email'];
$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if (mysqli_query($conn, $update)) {
            // Clean up the password reset record
            mysqli_query($conn, "DELETE FROM password_resets WHERE email='$email'");

            $success = "Password changed successfully!";
            session_destroy(); // Optionally destroy session
            header("Location: login.php"); // Redirect to login page
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Your Password</h2>
    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>New Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit" name="submit">Change Password</button>
    </form>
</body>
</html>
