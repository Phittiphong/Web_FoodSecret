<?php
session_start();
include 'connect.php'; // Your database connection file

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// Check if the user has already liked this post
$query = "SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the user has already liked the post, remove the like
    $delete = "DELETE FROM post_likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();

    // Return the updated like count
    $like_count = $conn->query("SELECT COUNT(*) AS like_count FROM post_likes WHERE post_id = $post_id")->fetch_assoc()['like_count'];
    echo json_encode(['status' => 'unliked', 'like_count' => $like_count]);
} else {
    // Add a like if not already liked
    $insert = "INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();

    // Return the updated like count
    $like_count = $conn->query("SELECT COUNT(*) AS like_count FROM post_likes WHERE post_id = $post_id")->fetch_assoc()['like_count'];
    echo json_encode(['status' => 'liked', 'like_count' => $like_count]);
}
?>
