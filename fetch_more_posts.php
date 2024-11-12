<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Query to fetch posts
$sql = "SELECT posts.*, users.firstName, users.lastName, users.username,
        (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id AND post_likes.user_id = $user_id) AS user_liked
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY like_count DESC, created_at DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$posts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($posts);
?>
