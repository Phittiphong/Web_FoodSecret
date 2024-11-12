<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev"; // Your database name

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Get the comment from the POST request
$comment = $_POST['comment'];

// Insert the comment into the database
$sql = "INSERT INTO comments (post_id, comment_text, created_at) VALUES (:post_id, :comment_text, NOW())";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':post_id' => 1, // You should dynamically set this based on the post being commented on
    ':comment_text' => $comment
]);

echo "Comment submitted successfully.";
?>
