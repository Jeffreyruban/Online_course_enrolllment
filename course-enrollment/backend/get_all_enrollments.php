<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false]);
    exit();
}

$sql = "SELECT e.id as enrollment_id, u.full_name AS student_name, c.course_name, e.date_enrolled, e.status
        FROM Enrollments e
        JOIN Users u ON e.user_id = u.id
        JOIN Courses c ON e.course_id = c.id
        ORDER BY e.date_enrolled DESC";

$result = $conn->query($sql);
$enrollments = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}
$conn->close();

echo json_encode(['success' => true, 'enrollments' => $enrollments]);
?>
