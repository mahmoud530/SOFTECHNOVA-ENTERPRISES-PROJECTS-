<?php
include 'mail.php';
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email;

    $select = "SELECT * FROM users WHERE email = '$email'";
    $run_select = mysqli_query($conn, $select);

    if (mysqli_num_rows($run_select) > 0) {
        $otp = rand(10000, 99999);
        $msg = "Hello, your OTP is: $otp";

        // Send Email
        $mail->setFrom('malllam146@gmail.com', 'Softechnova');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body = $msg;
        $mail->send();

        // Store OTP in DB
        mysqli_query($conn, "DELETE FROM password_resets WHERE email = '$email'");
        $insert = "INSERT INTO password_resets (email, otp) VALUES ('$email', '$otp')";
        mysqli_query($conn, $insert);

        header("Location: otp.php");
        exit;
    } else {
        $error_msg = "Email not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forget Password</title>
</head>
<body>
    <h2>Forget Password</h2>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <?php if (isset($error_msg)) { echo "<p style='color:red;'>$error_msg</p>"; } ?>
        <button type="submit" name="submit">Send OTP</button>
    </form>
</body>
</html>
