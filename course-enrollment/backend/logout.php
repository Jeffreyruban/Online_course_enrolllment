<?php
session_start();
header('Content-Type: application/json');

// Destroy session
session_destroy();

// Redirect to login
echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully',
    'redirect_url' => 'login.html'
]);
?>
