<?php
session_start();
include 'connect.php';

error_log("POST Data: " . print_r($_POST, true)); // Log POST data to error log

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$post_id = $_POST['post_id'] ?? null; // Use null coalescing to avoid undefined error
$report_title = $_POST['report_title'] ?? null;
$report_description = $_POST['report_description'] ?? ''; // Optional field, so allow it to be empty
$user_id = $_SESSION['user_id'];

// Check if required fields are missing
if (!$post_id || !$report_title) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing.']);
    exit;
}

// Insert the report into the database
$insertReport = $conn->prepare("INSERT INTO report_tickets (post_id, user_id, report_reason, report_description, created_at) VALUES (?, ?, ?, ?, NOW())");
$insertReport->bind_param("iiss", $post_id, $user_id, $report_title, $report_description);

if ($insertReport->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Report submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit report.']);
}


exit;
?>
