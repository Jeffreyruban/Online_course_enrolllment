<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$method = $data['method'] ?? '';

if ($method === 'add') {
    $full_name = $data['full_name'] ?? '';
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $email = $data['email'] ?? '';
    $role = $data['role'] ?? '';

    if (empty($full_name) || empty($username) || empty($password) || empty($email) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    if (!in_array($role, ['Student', 'Instructor'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit();
    }

    // Check if username already exists
    $sql = "SELECT id FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Insert new user
    $sql = "INSERT INTO Users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
    
    $stmt->bind_param("sssss", $username, $password, $email, $full_name, $role);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => $role . ' added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user']);
    }
    $stmt->close();

} elseif ($method === 'delete') {
    $user_id = $data['user_id'] ?? '';

    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        exit();
    }

    // Prevent deleting admin accounts
    $sql = "SELECT role FROM Users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['role'] === 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Cannot delete admin users']);
        exit();
    }

    // Delete user
    $sql = "DELETE FROM Users WHERE id = ? AND role IN ('Student', 'Instructor')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    $stmt->close();
}

$conn->close();
?>
