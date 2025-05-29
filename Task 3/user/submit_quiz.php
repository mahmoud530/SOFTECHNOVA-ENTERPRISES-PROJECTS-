<?php
include 'connection.php';
session_start();
$user_id = $_SESSION['user_id'];
$quiz_id = $_POST['quiz_id'];

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

$questions = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id");

$score = 0;
$total = $questions->num_rows;

while ($q = $questions->fetch_assoc()) {
    $qid = $q['id'];
    $correct = $q['correct_option'];
    $user_answer = $_POST["q$qid"] ?? '';

    if ($user_answer == $correct) {
        $score++;
    }
}


// Record the detailed results
$stmt = $conn->prepare("INSERT INTO results (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $user_id, $quiz_id, $score, $total);
$stmt->execute();

header("Location: result.php");

?>











