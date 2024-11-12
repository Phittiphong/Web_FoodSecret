<?php
session_start();
include("connect.php");

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

if ($post_id <= 0) {
    echo "Invalid post ID.";
    exit;
}

// Fetch post details
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $post = $result->fetch_assoc();
} else {
    echo "Post not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?php echo htmlspecialchars($post['post_content']); ?></h1>
        <img src="uploads/<?php echo htmlspecialchars($post['post_image']); ?>" alt="Post Image" class="img-fluid mb-4">
        <p><?php echo nl2br(htmlspecialchars($post['post_description'])); ?></p>
        <p><strong>Likes: </strong><?php echo $post['like_count']; ?></p>
        <a href="home.php" class="btn btn-primary">Back to Home</a>
    </div>
</body>
</html>
