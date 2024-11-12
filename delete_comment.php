<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$comment_id = $_POST['comment_id'];
$user_id = $_SESSION['user_id'];

// Ensure the comment belongs to the user before deleting
$checkQuery = $conn->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
$checkQuery->bind_param("ii", $comment_id, $user_id);
$checkQuery->execute();
$commentExists = $checkQuery->get_result()->num_rows > 0;

if ($commentExists) {
    $deleteQuery = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $deleteQuery->bind_param("i", $comment_id);
    if ($deleteQuery->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Comment deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete comment.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'You can only delete your own comments.']);
}
?>
