<?php
include '../includes/db.php';

$result = $conn->query("SELECT * FROM courses");
$courses = [];

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
?>