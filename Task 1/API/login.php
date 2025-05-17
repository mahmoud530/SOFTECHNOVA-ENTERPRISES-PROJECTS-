<?php
session_start();
include '../includes/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response array
$response = [
    'status' => 'error',
    'message' => '',
    'errors' => []
];

// Validate required fields
if (empty($_POST['email']) || empty($_POST['password'])) {
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['errors'][] = 'Please enter a valid email address';
}

// Validate password (basic check for non-empty)
if (strlen($password) < 1) {
    $response['errors'][] = 'Password is required';
}

// If there are validation errors, return them
if (!empty($response['errors'])) {
    $response['message'] = 'Validation failed';
    echo json_encode($response);
    exit;
}

try {
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

       
       
       
       

        // Remove sensitive data before sending response
        unset($user['password']);
        
        $response['status'] = 'success';
        $response['message'] = 'Login successful';
        $response['user'] = $user;
        
        // Add redirect based on role
        if ($user['role'] === 'student') {
            $response['redirect'] = '../student/dashboard.html';
        } else {
            $response['redirect'] = '../instructor/dashboard.html';
        }
    } else {
        $response['message'] = 'Invalid email or password';
    }
} catch (Exception $e) {
    $response['message'] = 'Login failed: ' . $e->getMessage();
}

echo json_encode($response);
?>