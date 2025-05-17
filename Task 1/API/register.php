<?php
include '../includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isStrongPassword($password) {
    return strlen($password) >= 8 
        && preg_match('/[A-Z]/', $password) 
        && preg_match('/[a-z]/', $password) 
        && preg_match('/[0-9]/', $password);
}

function isValidName($name) {
    return preg_match('/^[a-zA-Z\s\.\'-]{2,50}$/', $name);
}

// Initialize response array
$response = [
    'status' => 'error',
    'message' => '',
    'errors' => []
];

if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
    $response['message'] = 'All fields are required';
    echo json_encode($response);
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$role = $_POST['role'];

if (!isValidName($name)) {
    $response['errors'][] = 'Name should only contain letters, spaces, and basic punctuation (2-50 characters)';
}

if (!isValidEmail($email)) {
    $response['errors'][] = 'Please enter a valid email address';
}

if (!isStrongPassword($password)) {
    $response['errors'][] = 'Password must be at least 8 characters long and contain uppercase, lowercase, and numbers';
}

if (!in_array($role, ['student', 'instructor'])) {
    $response['errors'][] = 'Invalid role selected';
}

if (!empty($response['errors'])) {
    $response['message'] = 'Validation failed';
    echo json_encode($response);
    exit;
}

try {
    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['message'] = 'Email already registered';
        echo json_encode($response);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute insert statement
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Registration successful';
        $response['redirect'] = './login.html';
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
} catch (Exception $e) {
    $response['message'] = 'Registration failed: ' . $e->getMessage();
}

echo json_encode($response);
?>  