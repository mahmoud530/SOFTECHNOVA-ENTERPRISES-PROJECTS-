<?php
session_start();
include './connection.php';

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $admin_id = $_SESSION['user_id']; // Get the admin's ID from session

    if ($title !== '') {
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $description, $admin_id);
        $stmt->execute();
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Quiz title is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Quiz</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        form { max-width: 500px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 10px; }
        button { padding: 10px 15px; background: #28a745; color: white; border: none; }
        .error { color: red; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Create New Quiz</h2>
<form method="POST">
    <label>Quiz Title</label>
    <input type="text" name="title" required>
    <label>Description</label>
    <textarea name="description" rows="4"></textarea>
    <button type="submit">Add Quiz</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</form>

</body>
</html>
