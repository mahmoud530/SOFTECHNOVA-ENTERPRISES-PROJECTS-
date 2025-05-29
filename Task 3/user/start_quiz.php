<?php
include 'connection.php';
session_start();

if(isset($_POST['quiz_id'])){
    $quiz_id = $_POST['quiz_id'];
} else {
    header("Location: dashboard.php");
    exit();
}

if(isset($_POST['user_id'])){
    $user_id = $_POST['user_id'];
} else {
    header("Location: dashboard.php");
    exit();
}

// Check if user has already taken this quiz
$check_stmt = $conn->prepare("SELECT * FROM results WHERE user_id = ? AND quiz_id = ?");
$check_stmt->bind_param("ii", $user_id, $quiz_id);
$check_stmt->execute();
$attempt_result = $check_stmt->get_result();

if($attempt_result->num_rows > 0) {
    // User has already taken this quiz
    echo "<script>alert('You have already taken this quiz!'); window.location.href='dashboard.php';</script>";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Quiz</title>
</head>
<body>
    <form method="post" action="submit_quiz.php">
    <?php while($q = $questions->fetch_assoc()): ?>
        <p><?= $q['question_text'] ?></p>
        <input type="radio" name="q<?= $q['id'] ?>" value="A"> <?= $q['option_a'] ?><br>
        <input type="radio" name="q<?= $q['id'] ?>" value="B"> <?= $q['option_b'] ?><br>
        <input type="radio" name="q<?= $q['id'] ?>" value="C"> <?= $q['option_c'] ?><br>
        <input type="radio" name="q<?= $q['id'] ?>" value="D"> <?= $q['option_d'] ?><br>
    <?php endwhile; ?>
    <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
    <button type="submit">Submit Quiz</button>
</form>

</body>
</html>










