<?php
session_start(); // Start the session to use session variables

$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light'; // Default theme is light

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the post ID from the URL
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id === 0) {
    die("Invalid post ID.");
}

// Fetch the post data
$sql = "SELECT posts.*, users.firstName, users.lastName, users.username
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}

// Fetch like count and check if the user liked the post
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$sql_likes = "SELECT 
                (SELECT COUNT(*) FROM post_likes WHERE post_id = ?) AS like_count,
                (SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND user_id = ?) AS user_liked";
$stmt_likes = $conn->prepare($sql_likes);
$stmt_likes->bind_param("iii", $post_id, $post_id, $user_id);
$stmt_likes->execute();
$stmt_likes->bind_result($like_count, $user_liked);
$stmt_likes->fetch();
$stmt_likes->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['username']); ?>'s Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="button.css" rel="stylesheet">
</head>

<body class="<?php echo htmlspecialchars($theme); ?>-mode">
    <div class="container mt-4">
        <!-- Display the post -->
        <div class="post-item border rounded-3 p-3 mb-3 bg-light">
            <div class="d-flex mb-3">
                <img src="photo/user.png" alt="User Profile" class="rounded-circle" width="50" height="50">
                <div class="ms-3 d-flex align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($post['firstName']) . ' ' . htmlspecialchars($post['lastName']); ?> (@<?php echo htmlspecialchars($post['username']); ?>)</strong>
                        <p class="text-muted mb-0"><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <p class="mb-3"><?php echo nl2br(htmlspecialchars($post['post_content'])); ?></p>

            <?php if (!empty($post['post_image'])): ?>
                <div class="d-flex justify-content-center">
                    <img src="uploads/<?php echo htmlspecialchars($post['post_image']); ?>" alt="Post Image" class="img-fluid mb-3 rounded-3">
                </div>
            <?php endif; ?>

            <style>
                .post-item img {
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                    max-width: 100%;
                    height: auto;
                }
            </style>

            <div class="d-flex justify-content-between align-items-center">


                <!-- Like Button -->
                <?php
                $like_text = $user_liked ? 'Unlike' : 'Like';
                $button_class = $user_liked ? 'btn-primary' : 'btn-outline-primary';
                ?>
                <button class="btn <?php echo $button_class; ?> like-btn" data-post-id="<?php echo $post_id; ?>">
                    ❤️ <?php echo $like_text; ?>
                </button>
                <span id="like-count"><?php echo $like_count; ?> Likes</span>

                <!-- Comment Button -->
                <button class="btn btn-outline-secondary comment-btn" data-bs-toggle="modal" data-bs-target="#commentModal">
                    💬 Comment
                </button>

                <!-- Report Button -->
                <button class="btn btn-outline-danger report-btn" data-bs-toggle="modal" data-bs-target="#reportModal">
                    🚨 Report
                </button>

                <!-- Delete Button (show only if user is post owner) -->
                <?php if ($user_id == $post['user_id']): ?>
                    <a href="delete_post.php?post_id=<?php echo $post_id; ?>" class="btn btn-outline-danger">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Post Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display comments -->
                    <ul id="commentsList" class="list-unstyled">
                        <!-- Comments will be loaded here -->
                    </ul>

                    <!-- Add a new comment -->
                    <textarea class="form-control" id="commentText" rows="3" placeholder="Write your comment..."></textarea>
                    <button type="button" class="btn btn-primary mt-2" id="submitComment">Post Comment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Report Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" method="POST" action="report_post.php">
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                        <div class="form-group">
                            <label for="report_title">Report Title</label>
                            <select name="report_title" class="form-control" required>
                                <option value="Spam">Spam</option>
                                <option value="Inappropriate Content">Inappropriate Content</option>
                                <option value="Harassment">Harassment</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label for="report_description">Description</label>
                            <textarea name="report_description" class="form-control" rows="3" placeholder="Describe the issue..." required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Like button click
            document.querySelector('.like-btn').addEventListener('click', function() {
                var postId = this.getAttribute('data-post-id');
                var likeButton = this;
                var likeCountSpan = document.getElementById('like-count');

                // Send AJAX request to like/unlike the post
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'like_post.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            // Update the like count and button text
                            likeCountSpan.textContent = response.like_count + ' Likes';
                            likeButton.textContent = response.user_liked ? '❤️ Unlike' : '❤️ Like';
                            likeButton.classList.toggle('btn-primary');
                            likeButton.classList.toggle('btn-outline-primary');
                        }
                    }
                };
                xhr.send('post_id=' + postId);
            });

            // Handle Comment submission
            document.getElementById('submitComment').addEventListener('click', function() {
                var commentText = document.getElementById('commentText').value;
                if (!commentText.trim()) {
                    alert('Please enter a comment.');
                    return;
                }

                // Send AJAX request to submit the comment
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'add_comment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Handle the response, reload comments if successful
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            document.getElementById('commentsList').innerHTML += '<li>' + response.comment + '</li>';
                            document.getElementById('commentText').value = ''; // Clear the comment input
                        }
                    }
                };
                xhr.send('post_id=<?php echo $post_id; ?>&comment=' + encodeURIComponent(commentText));
            });
        });
    </script>
</body>

</html>