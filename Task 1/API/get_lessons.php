<?php
include '../includes/db.php';

$course_id = $_GET['course_id'];

$result = $conn->query("SELECT * FROM lessons WHERE course_id = $course_id");
$lessons = [];

while ($row = $result->fetch_assoc()) {
    $lessons[] = $row;
}

echo json_encode($lessons);
?>