<?php
session_start();
include './connection.php';
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin/login.php");
    exit;
}

// Fetch all quizzes with creator information
$sql = "SELECT q.id AS quiz_id, q.title, q.description, q.created_at, u.name as creator_name, 
        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as total_questions 
        FROM quizzes q 
        LEFT JOIN users u ON q.created_by = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        h1 { text-align: center; }
        .actions { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #333; color: white; }
        a.button { padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; }
        a.button:hover { background: #218838; }
        .danger { background: #dc3545; }
        .danger:hover { background: #c82333; }
        form { display: inline; }
    </style>
</head>
<body>

<h1>Admin Dashboard</h1>

<div class="actions">
    <a href="add_quiz.php" class="button">+ Create New Quiz</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Quiz Title</th>
        <th>Description</th>
        <th>Created By</th>
        <th>Total Questions</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
    <?php while ($quiz = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $quiz['quiz_id'] ?></td>
            <td><?= htmlspecialchars($quiz['title']) ?></td>
            <td><?= htmlspecialchars($quiz['description']) ?></td>
            <td><?= htmlspecialchars($quiz['creator_name'] ?? 'Unknown') ?></td>
            <td><?= $quiz['total_questions'] ?></td>
            <td><?= $quiz['created_at'] ?></td>
            <td>
                <form action="add_questions.php" method="POST">
                    <input type="hidden" name="quiz_id" value="<?= $quiz['quiz_id'] ?>">
                    <button type="submit" class="button">Add Questions</button>
                </form>
                <form action="delete_quiz.php" method="POST" onsubmit="return confirm('Are you sure?')">
                    <input type="hidden" name="quiz_id" value="<?= $quiz['quiz_id'] ?>">
                    <button type="submit" class="button danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<form action="" method="get">
    
    
<button type="submit" name="logout">Logout</button>

</form>
</body>
</html>
