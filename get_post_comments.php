<?php
include 'connect.php';

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Get the post content and comments
    $postQuery = $conn->prepare("SELECT post_content FROM posts WHERE id = ?");
    $postQuery->bind_param("i", $post_id);
    $postQuery->execute();
    $postResult = $postQuery->get_result()->fetch_assoc();

    $commentQuery = $conn->prepare("SELECT comments.id, users.username AS user, comments.comment_text AS text, comments.user_id FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?");
    $commentQuery->bind_param("i", $post_id);
    $commentQuery->execute();
    $comments = $commentQuery->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'post_content' => $postResult['post_content'],
        'comments' => $comments
    ]);
}
?>
