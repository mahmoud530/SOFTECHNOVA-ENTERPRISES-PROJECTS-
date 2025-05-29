<?php
include './connection.php';

// Add created_by column to quizzes table
$sql = "ALTER TABLE quizzes ADD COLUMN created_by INT, ADD FOREIGN KEY (created_by) REFERENCES users(id)";

if ($conn->query($sql) === TRUE) {
    echo "Column created_by added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?> 