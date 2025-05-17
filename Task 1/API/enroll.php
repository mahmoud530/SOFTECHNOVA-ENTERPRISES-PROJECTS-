<?php
include '../includes/db.php';

$user_id = $_POST['user_id'];
$course_id = $_POST['course_id'];

$stmt = $conn->prepare("INSERT IGNORE INTO enrollments (user_id, course_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();

echo json_encode(["status" => "enrolled"]);
?>