<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false]);
    exit();
}

// Get dashboard stats
$sql = "SELECT 
    (SELECT COUNT(*) FROM Users WHERE role = 'Student') AS total_students,
    (SELECT COUNT(*) FROM Courses) AS total_courses,
    (SELECT COUNT(*) FROM Enrollments) AS total_enrollments,
    (SELECT COUNT(DISTINCT user_id) FROM Enrollments) AS unique_enrolled_students";

$result = $conn->query($sql);
$stats = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stats[] = $row;
    }
}

// Get course statistics
$sql = "SELECT c.id, c.course_name, u.full_name AS instructor, 
        COUNT(e.id) AS total_enrollments,
        SUM(CASE WHEN e.status = 'Active' THEN 1 ELSE 0 END) AS active_students,
        SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) AS completed_students
        FROM Courses c
        LEFT JOIN Users u ON c.instructor_id = u.id
        LEFT JOIN Enrollments e ON c.id = e.course_id
        GROUP BY c.id, c.course_name, u.full_name
        ORDER BY total_enrollments DESC";

$result = $conn->query($sql);
$course_stats = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $course_stats[] = $row;
    }
}

// Get all student progress
$sql = "SELECT 
    u.full_name AS student_name,
    u.email,
    c.course_name,
    ROUND(AVG(p.score), 2) AS avg_score,
    SUM(CASE WHEN p.status = 'Completed' THEN 1 ELSE 0 END) AS completed_modules,
    COUNT(m.id) AS total_modules,
    e.status AS enrollment_status,
    e.date_enrolled
FROM Users u
JOIN Enrollments e ON u.id = e.user_id
JOIN Courses c ON e.course_id = c.id
JOIN Modules m ON c.id = m.course_id
LEFT JOIN Progress p ON e.id = p.enrollment_id AND m.id = p.module_id
WHERE u.role = 'Student'
GROUP BY u.id, e.id, c.id, u.full_name, u.email, c.course_name, e.status, e.date_enrolled
ORDER BY u.full_name, c.course_name";

$result = $conn->query($sql);
$student_progress = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $student_progress[] = $row;
    }
}

$conn->close();

echo json_encode([
    'success' => true, 
    'stats' => $stats, 
    'course_stats' => $course_stats,
    'student_progress' => $student_progress
]);
?>
