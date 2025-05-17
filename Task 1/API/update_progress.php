<?php
include '../includes/db.php';

$user_id = $_POST['user_id'];
$lesson_id = $_POST['lesson_id'];

$stmt = $conn->prepare("REPLACE INTO progress (user_id, lesson_id, completed) VALUES (?, ?, 1)");
$stmt->bind_param("ii", $user_id, $lesson_id);
$stmt->execute();

echo json_encode(["status" => "progress_updated"]);
?>