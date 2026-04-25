<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/../config.php');

// Get input data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$username = trim($data['username']);
$password = trim($data['password']);
$role = trim($data['role']);

// Validate input
if (empty($username) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Query database for user
$sql = "SELECT id, username, password, role, full_name FROM Users WHERE username = ? AND role = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Simple password comparison (in production, use password_hash and password_verify)
    if ($password === $user['password']) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        // Determine redirect URL based on role
        if ($role === 'Admin') {
            $redirect_url = 'admin_dashboard.html';
        } elseif ($role === 'Student') {
            $redirect_url = 'student_dashboard.html';
        } else {
            $redirect_url = 'instructor_dashboard.html';
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'redirect_url' => $redirect_url
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}

$stmt->close();
$conn->close();
?>
