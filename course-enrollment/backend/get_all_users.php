<?php
session_start();
header('Content-Type: application/json');
require_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false]);
    exit();
}

// Get all students
$sql = "SELECT id, username, email, full_name FROM Users WHERE role = 'Student' ORDER BY full_name";
$result = $conn->query($sql);
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Get all instructors
$sql = "SELECT id, username, email, full_name FROM Users WHERE role = 'Instructor' ORDER BY full_name";
$result = $conn->query($sql);
$instructors = [];
while ($row = $result->fetch_assoc()) {
    $instructors[] = $row;
}

$conn->close();

echo json_encode([
    'success' => true,
    'students' => $students,
    'instructors' => $instructors
]);
?>
