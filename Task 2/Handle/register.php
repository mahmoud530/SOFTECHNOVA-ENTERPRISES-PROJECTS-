<?php
include '../include/connection.php';
$errors = [];
$name = $email = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
   $name=mysqli_real_escape_string($conn,$_POST['name']);
    $email=mysqli_real_escape_string($conn,$_POST['email']);
    $password=mysqli_real_escape_string($conn,$_POST['password']);
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate name
    if (empty($name)) {
        $errors['name'] = "Name is required";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Name must be at least 3 characters long";
    } elseif (preg_match('/[0-9]/', $name)) {
        $errors['name'] = "Name cannot contain numbers";
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    } else {
        // Check if email already exists
        $sql = "SELECT user_id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $errors['email'] = "This email is already registered";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Password must contain at least one number";
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $errors['password'] = "Password must contain at least one letter";
    }

    // Validate confirm password
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $sql = "INSERT INTO users (user_name, email, password) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
        
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Registration successful! You can now login.";
                // Clear form data
                $name = $email = '';
            } else {
                $errors['general'] = "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>

        <?php if (!empty($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($errors['general'])): ?>
        <div class="error"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">


            <div class="form-group">
                <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>"
                    required>
                <?php if (isset($errors['name'])): ?>
                <div class="error"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>"
                    required>
                <?php if (isset($errors['email'])): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
                <?php if (isset($errors['password'])): ?>
                <div class="error"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                <div class="error"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>

</html>