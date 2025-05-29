<?php
session_start();
include './connection.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['quiz_id'])) {
    $quiz_id = intval($_POST['quiz_id']);
    $conn->query("DELETE FROM quizzes WHERE id = $quiz_id");
    header("Location: dashboard.php");
    exit;
}
