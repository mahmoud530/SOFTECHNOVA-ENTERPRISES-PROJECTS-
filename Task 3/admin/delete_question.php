<?php
session_start();
include './connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['question_id']) && isset($_POST['quiz_id'])) {
    $question_id = intval($_POST['question_id']);
    $quiz_id = intval($_POST['quiz_id']);
    
    // Delete the question
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $question_id, $quiz_id);
    $stmt->execute();
}

// Redirect back to add questions page
header("Location: add_questions.php");
exit;
?> 