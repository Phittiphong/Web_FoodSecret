<?php
include 'connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo 'not_logged_in';
    exit();
}

if (isset($_POST['user_id'])) {
    $follow_user_id = intval($_POST['user_id']);
    $follower_id = $_SESSION['user_id'];

    // Check if already following
    $checkFollow = $conn->prepare("SELECT * FROM follows WHERE follower_id = ? AND following_id = ?");
    $checkFollow->bind_param('ii', $follower_id, $follow_user_id);
    $checkFollow->execute();
    $result = $checkFollow->get_result();

    if ($result->num_rows == 0) {
        // Insert follow relationship
        $followQuery = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
        $followQuery->bind_param('ii', $follower_id, $follow_user_id);

        if ($followQuery->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'already_following';
    }
}
?>


