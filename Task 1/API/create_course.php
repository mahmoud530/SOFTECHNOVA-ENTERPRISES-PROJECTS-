<?php
include '../includes/db.php';

$title = $_POST['title'];
$desc = $_POST['description'];
$instructor_id = $_POST['instructor_id'];

$stmt = $conn->prepare("INSERT INTO courses (title, description, instructor_id) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $title, $desc, $instructor_id);
$stmt->execute();

echo json_encode(["status" => "course_created"]);
?>