<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.course_name, e.date_enrolled, e.status
        FROM Enrollments e
        JOIN Courses c ON e.course_id = c.id
        WHERE e.user_id = ?
        ORDER BY e.date_enrolled DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$enrollment_report = [];
while ($row = $result->fetch_assoc()) {
    $enrollment_report[] = $row;
}
$stmt->close();

$sql = "SELECT c.course_name, ROUND(AVG(p.score), 2) AS avg_score,
        MAX(p.score) AS highest_score, MIN(p.score) AS lowest_score
        FROM Enrollments e
        JOIN Courses c ON e.course_id = c.id
        JOIN Progress p ON e.id = p.enrollment_id
        WHERE e.user_id = ? AND p.score IS NOT NULL
        GROUP BY c.id, c.course_name
        ORDER BY c.course_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$performance_report = [];
while ($row = $result->fetch_assoc()) {
    $performance_report[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'enrollment_report' => $enrollment_report, 'performance_report' => $performance_report]);
?>
