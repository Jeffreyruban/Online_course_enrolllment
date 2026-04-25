<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(['success' => false]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$course_id = intval($data['course_id']);

$sql = "INSERT INTO Enrollments (user_id, course_id, date_enrolled, status) 
        VALUES (?, ?, CURDATE(), 'Active')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);

if ($stmt->execute()) {
    $enrollment_id = $stmt->insert_id;
    $sql2 = "INSERT INTO Progress (enrollment_id, module_id, status)
            SELECT ?, id, 'Pending' FROM Modules WHERE course_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ii", $enrollment_id, $course_id);
    $stmt2->execute();
    $stmt2->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
$stmt->close();
$conn->close();
?>
