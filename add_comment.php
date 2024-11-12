<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$post_id = $_POST['post_id'];
$comment = $_POST['comment'];
$user_id = $_SESSION['user_id'];

if (empty($comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty.']);
    exit;
}

// Insert the new comment into the database
$insertComment = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
$insertComment->bind_param("iis", $post_id, $user_id, $comment);

if ($insertComment->execute()) {
    echo json_encode(['status' => 'success', 'comment' => htmlspecialchars($comment)]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add comment.']);
}
?>
