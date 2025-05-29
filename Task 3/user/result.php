<?php
session_start();
include 'connection.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM results WHERE user_id = $user_id ORDER BY taken_at DESC LIMIT 1");
$row = $result->fetch_assoc();

echo "<h2>Quiz Result</h2>";
echo "<p>Score: {$row['score']} / {$row['total_questions']}</p>";
echo "<p>Taken at: {$row['taken_at']}</p>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>result</title>
</head>
<body>
    <a href="dashboard.php">Back To Your Dshboard</a>

</body>
</html>
