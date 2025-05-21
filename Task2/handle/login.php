<?php
include '../include/connection.php';
session_start();

// if (isset($_SESSION['user_id'])) {
    // header("location:home.php");
//    
// }

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error_msg = "Incorrect password.";
        }
    } else {
        $error_msg = "Email not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<?php if (!empty($error_msg)): ?>
    <p style="color:red"><?= htmlspecialchars($error_msg) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <a href="forget_pass.php">Forget Password?</a><br><br>

    <button type="submit" name="login">Login</button>
</form>

</body>
</html>
