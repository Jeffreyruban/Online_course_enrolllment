<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id as course_id, c.course_name, m.module_name, p.status, p.score, p.completion_date
        FROM Enrollments e
        JOIN Courses c ON e.course_id = c.id
        JOIN Modules m ON c.id = m.course_id
        LEFT JOIN Progress p ON e.id = p.enrollment_id AND m.id = p.module_id
        WHERE e.user_id = ?
        ORDER BY c.course_name, m.module_number";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$progress = [];
while ($row = $result->fetch_assoc()) {
    $progress[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'progress' => $progress]);
?>
