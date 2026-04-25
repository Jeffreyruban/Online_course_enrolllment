<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false]);
    exit();
}

$sql = "SELECT c.id, c.course_name, u.full_name AS instructor, c.duration_weeks, c.description, c.created_at
        FROM Courses c
        JOIN Users u ON c.instructor_id = u.id
        ORDER BY c.created_at DESC";

$result = $conn->query($sql);
$courses = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
$conn->close();

echo json_encode(['success' => true, 'courses' => $courses]);
?>
