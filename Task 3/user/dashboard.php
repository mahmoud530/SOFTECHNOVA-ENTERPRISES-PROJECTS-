<?php
session_start();
include './connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get quizzes
$quiz_sql = "SELECT * FROM quizzes";
$quiz_result = $conn->query($quiz_sql);

// Get user's quiz results
$result_sql = "SELECT quiz_id, score, total_questions FROM results WHERE user_id = ?";
$stmt = $conn->prepare($result_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$quiz_results = $stmt->get_result();

// Store scores and totals for lookup
$scores = [];
$total = [];
while ($row = $quiz_results->fetch_assoc()) {
    $scores[$row['quiz_id']] = $row['score'];
    $total[$row['quiz_id']] = $row['total_questions'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        h1, h2 { text-align: center; }
        table { width: 80%; margin: 0 auto; border-collapse: collapse; background-color: #fff; }
        th, td { padding: 12px; text-align: center; border: 1px solid #ccc; }
        th { background-color: #333; color: white; }
        button { background: none; border: none; color: blue; text-decoration: underline; cursor: pointer; }
        .disabled { color: red; text-decoration: none; cursor: default; }
        .logout-btn { display: block; margin: 20px auto; padding: 10px 20px; background: #dc3545; color: #fff; border: none; border-radius: 5px; }
        .logout-btn:hover { background: #c82333; }
    </style>
</head>
<body>

<h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
<h2>Available Quizzes</h2>

<table>
    <tr>
        <th>#</th>
        <th>Quiz Title</th>
        <th>Description</th>
        <th>Action</th>
    </tr>
    <?php
    $count = 1;
    while ($quiz = $quiz_result->fetch_assoc()):
        $quiz_id = $quiz['id'];

        // Check if quiz has questions
        $has_questions = $conn->query("SELECT COUNT(*) as total FROM questions WHERE quiz_id = $quiz_id")->fetch_assoc()['total'] > 0;

        // Check if user has already completed the quiz
        $has_completed = isset($scores[$quiz_id]);
        $score_display = $has_completed ? " (Score: {$scores[$quiz_id]}/{$total[$quiz_id]})" : "";
        
        $quiz_title = htmlspecialchars($quiz['title']) . $score_display;
        $quiz_description = htmlspecialchars($quiz['description']);
        
        $title_style = $has_completed ? "style='color:green;'" : "";
    ?>
    <tr>
        <td><?= $count++ ?></td>
        <td><span <?= $title_style ?>><?= $quiz_title ?></span></td>
        <td><?= $quiz_description ?></td>
        <td>
            <?php if ($has_questions): ?>
                <?php if ($has_completed): ?>
                    <span class="disabled">Already Completed</span>
                <?php else: ?>
                    <form action="start_quiz.php" method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
                        <button type="submit" onclick="return confirm('Are you sure?')">Start Quiz</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <span class="disabled">No Questions Available</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<form method="GET">
    <button type="submit" name="logout" class="logout-btn">Logout</button>
</form>

</body>
</html>
