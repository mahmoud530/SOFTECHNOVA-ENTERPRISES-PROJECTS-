<?php
session_start();
include './connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$quiz_id = $_POST['quiz_id'] ?? null;

if (!$quiz_id) {
    header("Location: dashboard.php");
    exit;
}



// Get quiz title for display
$stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();
$title = $quiz['title'] ?? '';

// Get existing questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $question = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = $_POST['correct_option'];

    if ($question && $correct_option) {
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $quiz_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_option);
        $stmt->execute();
        $success = "Question added!";
        
        // Refresh the questions list
        $stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        $questions = $stmt->get_result();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: auto; }
        .form-container { max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; }
        .questions-container { max-width: 800px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; }
        input, textarea, select { width: 100%; margin-bottom: 10px; padding: 10px; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .message { text-align: center; }
        .error { color: red; }
        .success { color: green; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: white; }
        .actions { margin-top: 20px; text-align: center; }
        .back-btn { background: #6c757d; }
        .back-btn:hover { background: #5a6268; }
        .delete-btn { background: #dc3545; }
        .delete-btn:hover { background: #c82333; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;">Add Questions to Quiz: <?= htmlspecialchars($title) ?></h2>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <label>Question</label>
            <textarea name="question" required></textarea>

            <label>Option A</label>
            <input type="text" name="option_a" required>

            <label>Option B</label>
            <input type="text" name="option_b" required>

            <label>Option C</label>
            <input type="text" name="option_c" required>

            <label>Option D</label>
            <input type="text" name="option_d" required>

            <label>Correct option</label>
            <select name="correct_option" required>
                <option value="">--Select--</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <button type="submit">Add Question</button>
        </form>

        <div class="message">
            <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </div>
    </div>

    <div class="questions-container">
        <h3>Existing Questions</h3>
        <table>
            <tr>
                <th>Question</th>
                <th>Options</th>
                <th>Correct option</th>
                <th>Actions</th>
            </tr>
            <?php while ($q = $questions->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($q['question_text']) ?></td>
                    <td>
                        A: <?= htmlspecialchars($q['option_a']) ?><br>
                        B: <?= htmlspecialchars($q['option_b']) ?><br>
                        C: <?= htmlspecialchars($q['option_c']) ?><br>
                        D: <?= htmlspecialchars($q['option_d']) ?>
                    </td>
                    <td><?= htmlspecialchars($q['correct_option']) ?></td>
                    <td>
                        <form method="POST" action="delete_question.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this question?')">
                            <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="actions">
        <a href="dashboard.php" class="button back-btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
