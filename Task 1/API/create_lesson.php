<?php
include '../includes/db.php';

$course_id = $_POST['course_id'];
$title = $_POST['title'];
$content = $_POST['content'];

$stmt = $conn->prepare("INSERT INTO lessons (course_id, title, content) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $course_id, $title, $content);
$stmt->execute();

echo json_encode(["status" => "lesson_created"]);
?>