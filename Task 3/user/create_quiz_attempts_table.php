<?php
include 'connection.php';

// Create quiz_attempts table
$sql = "CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT,
    attempt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    UNIQUE KEY unique_attempt (user_id, quiz_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table quiz_attempts created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 