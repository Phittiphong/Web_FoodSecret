<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev"; // ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch posts
$sql = "SELECT id, username, post_content, post_image, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Posts</h1>
        <a href="Home.php" class="btn btn-primary mb-3">Home</a>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="post border rounded-3 p-3 mb-3 bg-light">
                    <!-- Profile Section -->
                    <div class="d-flex mb-3">
                        <img src="photo/user.png" alt="User Profile" class="rounded-circle" width="50" height="50">
                        <div class="ms-3">
                            <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                            <p class="text-muted mb-0"><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></p>
                        </div>
                    </div>
                    <!-- Post Content -->
                    <p class="mb-3"><?php echo nl2br(htmlspecialchars($row['post_content'])); ?></p>
                    <?php if (!empty($row['post_image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['post_image']); ?>" alt="Post Image" class="img-fluid mb-3 rounded-3">
                    <?php endif; ?>
                    <!-- Post Actions -->
                    <div class="d-flex justify-content-between">
                        <form method="POST" action="delete_post.php" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                        </form>
                        <button class="btn btn-outline-primary">Like</button>
                        <button class="btn btn-outline-secondary">Comment</button>
                        <button class="btn btn-outline-info">Share</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>
</body>
</html>
