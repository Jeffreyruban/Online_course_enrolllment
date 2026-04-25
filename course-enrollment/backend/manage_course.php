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

if ($method === 'add') {
    $course_name = $data['course_name'] ?? '';
    $description = $data['description'] ?? '';
    $duration_weeks = $data['duration_weeks'] ?? null;
    $instructor_id = 2; // Default instructor

    $sql = "INSERT INTO Courses (course_name, description, instructor_id, duration_weeks) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $course_name, $description, $instructor_id, $duration_weeks);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();

} elseif ($method === 'update') {
    $course_id = $data['course_id'] ?? '';
    $course_name = $data['course_name'] ?? '';
    $description = $data['description'] ?? '';
    $duration_weeks = $data['duration_weeks'] ?? null;

    $sql = "UPDATE Courses SET course_name = ?, description = ?, duration_weeks = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $course_name, $description, $duration_weeks, $course_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();

} elseif ($method === 'delete') {
    $course_id = $data['course_id'] ?? '';

    $sql = "DELETE FROM Courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}

$conn->close();
?>
