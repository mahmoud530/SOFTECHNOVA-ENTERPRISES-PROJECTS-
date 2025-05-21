<?php
include '../include/connection.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: forget_pass.php");
    exit();
}

$email = $_SESSION['email'];
$error = '';

if (isset($_POST['submit'])) {
    $entered_otp = $_POST['otp'];

    $query = "SELECT * FROM password_resets WHERE email = '$email' ORDER BY created_at DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row && $row['otp'] == $entered_otp) {
        header("Location: change_pass.php");
        exit();
    } else {
        $error = "Incorrect OTP";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter OTP</title>
</head>
<body>
    <h2>Enter OTP</h2>
    <form method="POST">
        <input type="number" name="otp" required><br><br>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <button type="submit" name="submit">Verify</button>
    </form>
</body>
</html>
