<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$method = $data['method'] ?? '';

if ($method === 'update') {
    $enrollment_id = $data['enrollment_id'] ?? '';
    $status = $data['status'] ?? '';
    
    $valid_statuses = ['Active', 'Completed', 'Dropped'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false]);
        exit();
    }
    
    $sql = "UPDATE Enrollments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $enrollment_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();

} elseif ($method === 'delete') {
    $enrollment_id = $data['enrollment_id'] ?? '';

    $sql = "DELETE FROM Enrollments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}

$conn->close();
?>
