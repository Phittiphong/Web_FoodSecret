<?php
session_start();
include 'connect.php'; // Ensure this file securely handles database connections

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to edit comments']);
    exit;
}

if (isset($_POST['comment_id']) && isset($_POST['content'])) {
    $comment_id = $_POST['comment_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Check if the comment belongs to the logged-in user
    $query = "SELECT * FROM comments WHERE id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $comment_id, $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // The comment belongs to the user, so proceed to update it
                $updateQuery = "UPDATE comments SET content = ? WHERE id = ?";
                if ($updateStmt = $conn->prepare($updateQuery)) {
                    $updateStmt->bind_param("si", $content, $comment_id);
                    if ($updateStmt->execute()) {
                        echo json_encode(['status' => 'success']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error updating comment']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error preparing update query']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'You are not authorized to edit this comment']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error executing query']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing query']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Comment ID or content not provided']);
}
?>
