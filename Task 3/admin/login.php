<?php
session_start();
include './connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; 
        header("Location:dashboard.php");
    } else {
        echo "Invalid login!";
    }
}
?>

<form method="post">
    <input name="email" type="email" required><br>
    <input name="password" type="password" required><br>
    <button type="submit">Login</button>
</form>
