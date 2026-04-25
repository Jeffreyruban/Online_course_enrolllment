<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/../config.php');

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get student stats
$sql = "SELECT 
    COUNT(DISTINCT e.id) AS total_enrolled,
    SUM(CASE WHEN e.status = 'Active' THEN 1 ELSE 0 END) AS active_courses,
    SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) AS completed_courses,
    ROUND(AVG(p.score), 2) AS average_score
FROM Enrollments e
LEFT JOIN Progress p ON e.id = p.enrollment_id AND p.score IS NOT NULL
WHERE e.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$stmt->close();

// Get enrolled courses
$sql = "SELECT 
    e.id as enrollment_id,
    c.id as course_id,
    c.course_name,
    u.full_name AS instructor,
    e.status,
    ROUND(AVG(p.score), 2) AS avg_score,
    ROUND(SUM(CASE WHEN p.status = 'Completed' THEN 1 ELSE 0 END) / 
        (SELECT COUNT(*) FROM Modules WHERE course_id = c.id) * 100, 0) AS progress_percent
FROM Enrollments e
JOIN Courses c ON e.course_id = c.id
JOIN Users u ON c.instructor_id = u.id
LEFT JOIN Progress p ON e.id = p.enrollment_id
WHERE e.user_id = ?
GROUP BY e.id, c.id, c.course_name, u.full_name, e.status
ORDER BY e.date_enrolled DESC";

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

echo json_encode([
    'success' => true,
    'stats' => $stats,
    'courses' => $courses
]);
?>
