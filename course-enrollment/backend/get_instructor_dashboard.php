<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$instructor_id = $_SESSION['user_id'];

// Get instructor's courses with stats
$sql = "SELECT 
    c.id,
    c.course_name,
    c.description,
    c.duration_weeks,
    COUNT(DISTINCT e.user_id) AS total_students,
    SUM(CASE WHEN e.status = 'Active' THEN 1 ELSE 0 END) AS active_students,
    SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) AS completed_students
FROM Courses c
LEFT JOIN Enrollments e ON c.id = e.course_id
WHERE c.instructor_id = ?
GROUP BY c.id, c.course_name, c.description, c.duration_weeks
ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = [];

while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

// Get student progress - separate query with proper JOINs
$sql = "SELECT 
    u.full_name AS student_name,
    c.course_name,
    ROUND(AVG(p.score), 2) AS avg_score,
    SUM(CASE WHEN p.status = 'Completed' THEN 1 ELSE 0 END) AS completed_modules,
    COUNT(m.id) AS total_modules
FROM Courses c
INNER JOIN Enrollments e ON c.id = e.course_id
INNER JOIN Users u ON e.user_id = u.id
INNER JOIN Modules m ON c.id = m.course_id
LEFT JOIN Progress p ON e.id = p.enrollment_id AND m.id = p.module_id
WHERE c.instructor_id = ?
GROUP BY u.id, c.id, u.full_name, c.course_name
ORDER BY c.course_name, u.full_name";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$student_progress = [];

while ($row = $result->fetch_assoc()) {
    $student_progress[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'courses' => $courses,
    'student_progress' => $student_progress
]);
?>
