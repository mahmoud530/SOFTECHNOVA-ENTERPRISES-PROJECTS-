<?php
$host = 'localhost';
$db = 'auth_system';  
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// logout
if(isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
