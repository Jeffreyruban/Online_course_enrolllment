<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id, c.course_name, c.description, u.full_name AS instructor, c.duration_weeks,
        EXISTS(SELECT 1 FROM Enrollments WHERE user_id = ? AND course_id = c.id) AS is_enrolled
        FROM Courses c JOIN Users u ON c.instructor_id = u.id ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'courses' => $courses]);
?>
