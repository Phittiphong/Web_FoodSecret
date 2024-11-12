<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch posts liked by the user
$sql = "SELECT p.id, p.post_content as content, p.title
        FROM posts p
        JOIN post_likes pl ON p.id = pl.post_id
        WHERE pl.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$liked_posts = [];
while ($row = $result->fetch_assoc()) {
    $liked_posts[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'content' => $row['content'],
    ];
}

echo json_encode(['liked_posts' => $liked_posts]);
