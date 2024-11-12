<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Fetch the user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT firstName, lastName, profile_picture, bio, role FROM users WHERE Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $role = $user['role'];  // Fetch the role from the users table
} else {
    echo "User not found.";
    exit;
}

// Handle like action
if (isset($_POST['like'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $checkLikeQuery = "SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?";
    $checkLikeStmt = $conn->prepare($checkLikeQuery);
    $checkLikeStmt->bind_param("ii", $post_id, $user_id);
    $checkLikeStmt->execute();
    $likeExists = $checkLikeStmt->get_result()->num_rows > 0;

    if ($likeExists) {
        // Unlike the post
        $deleteLikeQuery = "DELETE FROM post_likes WHERE post_id = ? AND user_id = ?";
        $deleteLikeStmt = $conn->prepare($deleteLikeQuery);
        $deleteLikeStmt->bind_param("ii", $post_id, $user_id);
        $deleteLikeStmt->execute();
    } else {
        // Like the post
        $insertLikeQuery = "INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)";
        $insertLikeStmt = $conn->prepare($insertLikeQuery);
        $insertLikeStmt->bind_param("ii", $post_id, $user_id);
        $insertLikeStmt->execute();
    }
}

// Fetch followers, following, and likes counts
$followers_sql = "SELECT COUNT(*) AS count FROM followers WHERE user_id = ?";
$following_sql = "SELECT COUNT(*) AS count FROM following WHERE user_id = ?";
$likes_sql = "SELECT COUNT(*) AS count FROM likes WHERE user_id = ?";

$followers_stmt = $conn->prepare($followers_sql);
$followers_stmt->bind_param("i", $user_id);
$followers_stmt->execute();
$followers_count = $followers_stmt->get_result()->fetch_assoc()['count'];

$following_stmt = $conn->prepare($following_sql);
$following_stmt->bind_param("i", $user_id);
$following_stmt->execute();
$following_count = $following_stmt->get_result()->fetch_assoc()['count'];

// Fetch likes count for the user's posts
$likes_sql = "SELECT COUNT(*) AS like_count 
              FROM post_likes 
              INNER JOIN posts ON post_likes.post_id = posts.id 
              WHERE posts.user_id = ?";
$likes_stmt = $conn->prepare($likes_sql);
$likes_stmt->bind_param("i", $user_id);
$likes_stmt->execute();
$likes_count = $likes_stmt->get_result()->fetch_assoc()['like_count'];


// Fetch posts liked by the user
$liked_posts_sql = "
    SELECT posts.id, posts.post_content, posts.post_image, 
           (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id) AS like_count 
    FROM post_likes 
    JOIN posts ON post_likes.post_id = posts.id 
    WHERE post_likes.user_id = ?
";

$liked_posts_stmt = $conn->prepare($liked_posts_sql);
$liked_posts_stmt->bind_param("i", $user_id);
$liked_posts_stmt->execute();
$liked_posts_result = $liked_posts_stmt->get_result();

// Fetch posts created by the user with like count
$user_posts_sql = "
    SELECT posts.id, posts.post_content, posts.post_image, 
           (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id) AS like_count
    FROM posts 
    WHERE posts.user_id = ?
";

$user_posts_stmt = $conn->prepare($user_posts_sql);
$user_posts_stmt->bind_param("i", $user_id);
$user_posts_stmt->execute();
$user_posts_result = $user_posts_stmt->get_result();

// Fetch post count for the user
$post_count_sql = "SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?";
$post_count_stmt = $conn->prepare($post_count_sql);
$post_count_stmt->bind_param("i", $user_id);
$post_count_stmt->execute();
$post_count = $post_count_stmt->get_result()->fetch_assoc()['post_count'];



// Handle delete action
if (isset($_POST['delete'])) {
    $post_id = $_POST['post_id'];

    // Delete the post from the database
    $deletePostQuery = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $deletePostStmt = $conn->prepare($deletePostQuery);
    $deletePostStmt->bind_param("ii", $post_id, $user_id);
    if ($deletePostStmt->execute()) {
        echo "Post deleted successfully.";
    } else {
        echo "Error deleting post: " . $deletePostStmt->error;
    }
}

// Handle edit action
if (isset($_POST['edit'])) {
    $post_id = $_POST['post_id'];
    $post_content = $_POST['post_content'];

    // Update the post content in the database
    $editPostQuery = "UPDATE posts SET post_content = ? WHERE id = ? AND user_id = ?";
    $editPostStmt = $conn->prepare($editPostQuery);
    $editPostStmt->bind_param("sii", $post_content, $post_id, $user_id);
    if ($editPostStmt->execute()) {
        echo "Post updated successfully.";
    } else {
        echo "Error updating post: " . $editPostStmt->error;
    }
}

// Handle post update (edit action)
if (isset($_POST['update_post'])) {
    $post_id = $_POST['post_id'];
    $updated_content = $_POST['post_content'];

    // Update the post in the database
    $updatePostQuery = "UPDATE posts SET post_content = ? WHERE id = ? AND user_id = ?";
    $updatePostStmt = $conn->prepare($updatePostQuery);
    $updatePostStmt->bind_param("sii", $updated_content, $post_id, $user_id);

    if ($updatePostStmt->execute()) {
        echo '<script type="text/javascript">
            setTimeout(function() {
                window.location.href = "profile.php";
            }, 2000); // Redirect after 2 seconds
        </script>';
    } else {
        echo "Error updating post: " . $updatePostStmt->error;
    }

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);
        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                $profilePicture = basename($_FILES['profile_picture']['name']);
            } else {
                echo "Error uploading file.";
                $profilePicture = $user['profile_picture'];
            }
        } else {
            echo "Invalid file type.";
            $profilePicture = $user['profile_picture'];
        }
    } else {
        $profilePicture = $user['profile_picture'];
    }

    // Update user information
    $updateQuery = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, profile_picture = ?, bio = ? WHERE Id = ?");
    $updateQuery->bind_param("ssssi", $firstName, $lastName, $profilePicture, $bio, $user_id);

    if ($updateQuery->execute()) {
        echo '<script type="text/javascript">window.onload = function() { new bootstrap.Modal(document.getElementById("successModal")).show(); }</script>';
    } else {
        echo "Error updating profile: " . $updateQuery->error;
    }
}

// Fetch the logged-in user's details
if ($user_id) {
    $stmt = $conn->prepare("SELECT username, firstName, lastName, profile_picture FROM users WHERE Id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $firstname, $lastname, $profile_picture);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- For icons -->

    
</head>

<body>
    <header class="p-3 mb-3 border-bottom">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <img src="photo/Logo.png" alt="Logo" width="150" height="150">
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="Home.php" class="nav-link px-2 link-body-emphasis">Home</a></li>
                    <li><a href="contact.php" class="nav-link px-2 link-body-emphasis">Contact</a></li>
                    <li><a href="about.php" class="nav-link px-2 link-body-emphasis">About</a></li>
                    <li><a href="campaign.php" class="nav-link px-2 link-body-emphasis">Campaign</a></li>
                </ul>

                <!-- Dropdown -->
                <div class="dropdown text-end">
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $profile_picture ? 'uploads/'.$profile_picture : 'photo/user.png'; ?>" 
                    alt="User" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <?php if ($role === 'admin'): ?>
                            <li><a class="dropdown-item" href="admin_dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center" style="background-color: #C85C5C;">
                        <h4 style="color: white;">Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" width="150">
                        </div>
                        <h5 class="text-center"><?php echo htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']); ?></h5>
                        <p class="text-center"><?php echo htmlspecialchars($user['bio']); ?></p>
                        <div class="text-center mb-3">
                            <span class="d-block">Posts: <span id="profile-post-count"><?php echo $post_count; ?></span></span>
                            <span class="d-block">Likes: <span id="profile-likes-count"><?php echo $likes_count; ?></span></span>

                        </div>

                        <div class="text-center">
                            <a href="edit_profile.php" class="btn btn-primary" style="background-color: #80FF00; color: black;">Edit Profile</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="nav-icons">
                <span id="userPostsIcon" class="icon active">&#x25A9; <!-- Grid icon --></span>
                <span id="likedPostsIcon" class="icon">&#x2764; <!-- Heart icon --></span>
            </div>

            <!-- User's Posts Section -->
            <div id="userPosts" class="post-grid active">
                <h4>Your Posts</h4>
                <div class="row justify-content-center mt-4">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header text-center">
                                <h4>Your Posts</h4>
                            </div>
                            <div class="card-body">
                                <?php if ($user_posts_result->num_rows > 0): ?>
                                    <div class="user-posts-grid">
                                        <?php while ($user_post = $user_posts_result->fetch_assoc()): ?>
                                            <div class="user-post-item">
                                            <?php 
// เช็คประเภทของไฟล์
$file_extension = pathinfo($user_post['post_image'], PATHINFO_EXTENSION);
if (!empty($user_post['post_image'])):
    if (in_array(strtolower($file_extension), ['mp4'])): // ถ้าเป็นไฟล์วิดีโอ
?>
    <video class="post-thumbnail" controls>
        <source src="uploads/<?php echo htmlspecialchars($user_post['post_image']); ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
<?php 
    elseif (in_array(strtolower($file_extension), ['gif', 'jpg', 'jpeg', 'png'])): // ถ้าเป็นรูปภาพ
?>
    <img src="uploads/<?php echo htmlspecialchars($user_post['post_image']); ?>" 
         alt="Post Image" 
         class="post-thumbnail">
<?php 
    endif;
else: 
?>
    <img src="uploads/default-image.jpg" alt="Default Image" class="post-thumbnail">
<?php endif; ?>
                                                <div class="post-info">
                                                    <p class="post-content"><?php echo htmlspecialchars($user_post['post_content']); ?></p>
                                                    <span class="post-likes"><i class="fas fa-heart"></i> <?php echo $user_post['like_count']; ?> Likes</span>
                                                </div>

                                                <!-- Edit Button -->
                                                <button class="btn btn-primary btn-sm mt-2" style="background-color: yellow; color: black;" onclick="toggleEditForm(<?php echo $user_post['id']; ?>)">Edit</button>

                                                <!-- Edit Form (Initially hidden) -->
                                                <div id="editForm_<?php echo $user_post['id']; ?>" style="display: none;" class="edit-form mt-2">
                                                    <form method="post">
                                                        <input type="hidden" name="post_id" value="<?php echo $user_post['id']; ?>">
                                                        <div class="form-group">
                                                            <textarea name="post_content" class="form-control" rows="2"><?php echo htmlspecialchars($user_post['post_content']); ?></textarea>
                                                        </div>
                                                        <button type="submit" name="update_post" style="background-color: yellow; color: black;" class="btn btn-success btn-sm mt-2">Update</button>
                                                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="toggleEditForm(<?php echo $user_post['id']; ?>)">Cancel</button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p>You haven't made any posts yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="successModalLabel">Success</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Your post has been updated successfully!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JavaScript to toggle the edit form and confirm delete -->
            <script>
                function toggleEditForm(postId) {
                    var form = document.getElementById('editForm_' + postId);
                    if (form.style.display === 'none' || form.style.display === '') {
                        form.style.display = 'block'; // Show the edit form
                    } else {
                        form.style.display = 'none'; // Hide the edit form
                    }
                }

                function confirmDelete(postId) {
                    if (confirm('Are you sure you want to delete this post?')) {
                        // Send the delete request using post_id via POST
                        fetch('delete_post.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'post_id=' + encodeURIComponent(postId)
                            })
                            .then(response => response.text())
                            .then(responseText => {
                                alert(responseText); // Display the success message
                                document.getElementById(`post-${postId}`).remove(); // Remove the post from UI
                            })
                            .catch(error => {
                                console.error('Error deleting post:', error);
                                alert("An error occurred while deleting the post.");
                            });
                    }
                }

                // Trigger the success modal after an update
                <?php if (isset($_POST['update_post']) && $updatePostStmt->execute()) : ?>
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'), {});
                    successModal.show();
                <?php endif; ?>
            </script>

            <!-- Liked Posts Section -->
            <div id="likedPosts" class="post-grid">
                <!-- Your code to display liked posts -->
                <h4>Posts You've Liked</h4>
                <!-- Liked Posts Section -->
                <div class="row justify-content-center mt-4">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header text-center">
                                <h4>Posts You've Liked</h4>
                            </div>
                            <div class="card-body">
                                <?php if ($liked_posts_result->num_rows > 0): ?>
                                    <div class="liked-posts-grid">
                                        <?php while ($liked_post = $liked_posts_result->fetch_assoc()): ?>
                                            <div class="liked-post-item">
                                                <?php if (!empty($liked_post['post_image'])): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($liked_post['post_image']); ?>" alt="Post Image" class="post-thumbnail">
                                                <?php else: ?>
                                                    <img src="uploads/default-image.jpg" alt="Default Image" class="post-thumbnail"> <!-- Use a default image if not available -->
                                                <?php endif; ?>
                                                <div class="post-info">
                                                    <p class="post-content"><?php echo htmlspecialchars($liked_post['post_content']); ?></p>
                                                    <span class="post-likes"><i class="fas fa-heart"></i> <?php echo $liked_post['like_count']; ?> Likes</span> <!-- Display actual like count -->
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Add this CSS to center the images -->
                            <style>
                                /* Liked posts grid layout */
                                .liked-posts-grid {
                                    display: grid;
                                    grid-template-columns: repeat(3, 1fr);
                                    gap: 20px;
                                    padding: 20px;
                                }

                                .liked-post-item {
                                    position: relative;
                                    border: 1px solid #ddd;
                                    border-radius: 8px;
                                    overflow: hidden;
                                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                    background-color: #f9f9f9;
                                    text-align: center;
                                    /* Center the image horizontally */
                                }

                                .post-thumbnail {
                                    display: block;
                                    margin: 0 auto;
                                    /* Center the image horizontally */
                                    width: 100px;
                                    height: 100px;
                                    object-fit: cover;
                                    /* Maintain aspect ratio, crop if needed */
                                }

                                .post-info {
                                    padding: 10px;
                                    text-align: center;
                                }

                                .post-likes {
                                    font-size: 14px;
                                    color: #999;
                                }

                                .post-likes i {
                                    margin-right: 5px;
                                    color: #f44336;
                                }

                                
                            </style>

                        </div>
                    </div>
                </div>

            </div>
            <!-- Add your actual liked posts here -->

        </div>

        <footer>
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-2 mb-3">
                    <h5>Support</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Suranaree University</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">1101401@gmail.com</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">+88015-88888-9999</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <h5>Account</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">My Account</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Cart</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Wishlist</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Shop</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2 mb-3">
                    <h5>Quick link</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Privacy Policy</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Terms Of Use</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">FAQ</a></li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-5 offset-md-1 mb-3">
                    <form>
                        <div class="d-flex flex-column w-100 gap-2">
                            <div class="subscribe-section">
                                <h5>Subscribe</h5>
                                <input type="email" class="form-control" placeholder="Enter your email">
                                <button class="btn btn-primary">Subscribe</button>
                            </div>

                            <p class="mt-2">Get 10% off your first order</p>
                        </div>
                    </form>
                </div>
            </div>
            <p>© 2024 Company, Inc. All rights reserved.</p>
        </div>
    </footer>

        <script>
            document.getElementById('userPostsIcon').addEventListener('click', function() {
                // Show user posts, hide liked posts
                document.getElementById('userPosts').classList.add('active');
                document.getElementById('likedPosts').classList.remove('active');

                // Update active icon
                this.classList.add('active');
                document.getElementById('likedPostsIcon').classList.remove('active');
            });

            document.getElementById('likedPostsIcon').addEventListener('click', function() {
                // Show liked posts, hide user posts
                document.getElementById('likedPosts').classList.add('active');
                document.getElementById('userPosts').classList.remove('active');

                // Update active icon
                this.classList.add('active');
                document.getElementById('userPostsIcon').classList.remove('active');
            });
        </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.user-post-item, .liked-post-item').forEach(post => {
        const content = post.querySelector('.post-content');
        const postInfo = post.querySelector('.post-info');
        
        if (content && content.scrollHeight > 100) {
            const readMoreBtn = document.createElement('button');
            readMoreBtn.className = 'read-more-btn';
            readMoreBtn.textContent = 'ดูเพิ่มเติม';
            
            if (postInfo) {
                // แทรกปุ่มหลังจาก post-content
                content.parentNode.insertBefore(readMoreBtn, content.nextSibling);
                
                readMoreBtn.addEventListener('click', () => {
                    content.classList.toggle('expanded');
                    readMoreBtn.textContent = content.classList.contains('expanded') ? 'แสดงน้อยลง' : 'ดูเพิ่มเติม';
                });
            }
        }
    });
});
</script>

        <style>

:root {
                --primary-color: #C85C5C;
                --secondary-color: #f5f6fa;
                --success-color: #2ecc71;
                --warning-color: #f1c40f;
                --danger-color: #e74c3c;
                --text-color: #2c3e50;
                --border-color: #dcdde1;
                --rounded-color: White;
            }

            /* Dropdown Menu */
            .dropdown-menu {
                border: none;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }

            .dropdown-item {
                padding: 10px 20px;
                transition: background-color 0.3s ease;
            }

            .dropdown-item:hover {
                background-color: var(--secondary-color);
                color: var(--primary-color);
            }

            /* User Profile Image */
            .rounded-circle {
                border: 2px solid var(--rounded-color);
                transition: transform 0.3s ease;
            }

            .rounded-circle:hover {
                transform: scale(1.1);
            }

            /* Logo Styles */
            header img[alt="Logo"] {
                transition: transform 0.3s ease;
            }

            header img[alt="Logo"]:hover {
                transform: scale(1.05);
            }

.post-grid {
            display: none;
            margin-top: 20px;
        }

        .post-grid.active {
            display: block;
        }

        .post-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .liked-post-item {
            margin-bottom: 20px;
        }

        /* รูปแบบมาตรฐานของส่วน Header */
header {
    background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%); /* ไล่สีจากเหลืองอ่อนไปเหลืองเข้ม */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* เงาใต้ header */
    padding: 0.5rem 0;
}

header .nav-link {
    font-weight: 500; /* ความหนาของตัวอักษร */
    color: #2b2d42 !important; /* สีตัวอักษร */
    transition: color 0.3s ease; /* การเปลี่ยนสีแบบค่อยๆ เปลี่ยน */
    position: relative;
}

header .nav-link:hover {
    color: #8d99ae !important; /* สีตัวอักษรเมื่อนำเมาส์ชี้ */
}

/* เส้นใต้เมนูที่จะปรากฏเมื่อนำเมาส์ชี้ */
header .nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -2px;
    left: 50%;
    background-color: #2b2d42;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

header .nav-link:hover::after {
    width: 100%;
}

/* รูปแบบมาตรฐานของส่วน Footer */
footer {
    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%); /* ไล่สีจากแดงอ่อนไปแดงเข้ม */
    color: white;
    padding: 3rem 0;
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: 6rem;
    margin-bottom: 0;
}

footer .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

footer h5 {
    font-weight: 600;
    margin-bottom: 1.5rem;
}

/* ลิงก์ใน footer */
footer .nav-link {
    color: rgba(255, 255, 255, 0.8) !important; /* สีขาวแบบโปร่งใส */
    transition: color 0.3s ease;
    padding: 0.3rem 0;
}

footer .nav-link:hover {
    color: white !important;
}

/* ส่วนฟอร์มสมัครรับข้อมูล */
footer .subscribe-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

footer .subscribe-section input {
    border-radius: 25px;
    padding: 0.8rem 1.5rem;
    border: none;
}

footer .subscribe-section button {
    background: #FBD148; /* สีปุ่มเหลือง */
    color: #2b2d42;
    border: none;
    border-radius: 25px;
    padding: 0.8rem 1.5rem;
    transition: all 0.3s ease;
}

footer .subscribe-section button:hover {
    background: #ffba08;
    transform: translateY(-2px);
}

/* การปรับแต่งสำหรับหน้าจอขนาดเล็ก */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    footer {
        padding: 2rem 0;
        margin-top: 4rem;
    }

    footer .row {
        text-align: center;
    }

    footer .subscribe-section {
        align-items: center;
    }

    footer .subscribe-section input,
    footer .subscribe-section button {
        width: 100%;
        max-width: 300px;
    }
}
        

/* การ์ดโปรไฟล์ */
.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-header {
    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
    color: white;
    padding: 1.5rem;
    border: none;
}

.card-body {
    padding: 2rem;
}

/* รูปโปรไฟล์ */
.img-thumbnail {
    border: 5px solid white;
    border-radius: 50%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    width: 150px;
    height: 150px;
    object-fit: cover;
}

.img-thumbnail:hover {
    transform: scale(1.05);
}

/* สถิติผู้ใช้ */
.profile-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin: 1.5rem 0;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 600;
    color: #C85C5C;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

/* แท็บการนำทาง */
.nav-icons {
    background: white;
    padding: 1rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    margin: 2rem 0;
}

.icon {
    padding: 1rem 2rem;
    font-size: 1.5rem;
    color: #666;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.icon:hover {
    background: #f8f9fa;
    color: #C85C5C;
}

.icon.active {
    color: #C85C5C;
    border-bottom: 3px solid #C85C5C;
    font-weight: 600;
}

/* กริดโพสต์ */
.user-posts-grid, .liked-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1.5rem;
}

.user-post-item:hover, .liked-post-item:hover {
    transform: translateY(-5px);
}

.post-thumbnail:hover {
    transform: scale(1.05);
}


/* ปรับส่วนของเนื้อหาโพสต์ */
.post-content {
    max-height: 100px; /* ความสูงเริ่มต้น */
    overflow: hidden;
    position: relative;
    margin-bottom: 1rem;
    transition: max-height 0.5s ease; /* เพิ่ม transition */
    line-height: 1.5;
}

/* เมื่อขยาย */
.post-content.expanded {
    max-height: 1000px; /* ให้ค่าสูงพอที่จะแสดงเนื้อหาทั้งหมด */
}

/* ปรับส่วนของปุ่มดูเพิ่มเติม */
.read-more-btn {
    background: none;
    border: 2px solid #C85C5C;
    color: #C85C5C;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 20px;
    cursor: pointer;
    display: block; /* เปลี่ยนเป็น block */
    margin: 10px auto; /* จัดกึ่งกลาง */
    transition: all 0.3s ease;
    position: relative;
    z-index: 2; /* ให้อยู่ด้านบนของ gradient */
}

.read-more-btn:hover {
    background: #C85C5C;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(200, 92, 92, 0.2);
}

/* เอฟเฟกต์ไล่สี */
.post-content:not(.expanded)::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 50px;
    background: linear-gradient(transparent, white);
    pointer-events: none;
    z-index: 1;
}

/* JavaScript ที่ต้องแก้ไข */

/* ไอคอนลูกศร */
.read-more-btn::after {
    content: '▼';
    font-size: 0.8rem;
    transition: transform 0.3s ease;
}

.read-more-btn.expanded::after {
    transform: rotate(180deg);
}

.post-item:hover {
    transform: translateY(-5px);
}

/* ปุ่มในโพสต์ */
.btn-sm {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-danger {
    background: #ff4757;
    border: none;
}

.btn-danger:hover {
    background: #ff6b81;
    transform: translateY(-2px);
}

.edit-form textarea:focus {
    border-color: #C85C5C;
    box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.25);
}

/* Modal */
.modal-content {
    border-radius: 20px;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
    color: white;
    border: none;
}

/* ส่วนของปุ่ม Edit */
.btn-primary.btn-sm {
    background: #ffd32a;
    color: #333;
    padding: 12px 30px;
    font-size: 1rem;
    border-radius: 25px;
    margin: 15px auto;
    display: block; /* ทำให้ปุ่มอยู่ตรงกลาง */
    width: fit-content;
    min-width: 150px;
    transition: all 0.3s ease;
    border: none;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 4px 15px rgba(255, 211, 42, 0.2);
}

.btn-primary.btn-sm:hover {
    background: #ffc107;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 211, 42, 0.3);
}

/* ส่วนของไลค์ */
.post-likes {
    display: flex;
    align-items: center;
    justify-content: center; /* จัดให้อยู่ตรงกลาง */
    gap: 0.5rem;
    color: #666;
    margin: 15px 0;
    padding: 10px;
    font-size: 1.1rem; /* เพิ่มขนาดตัวอักษร */
    font-weight: 500;
}

.post-likes i {
    color: #ff4757;
    font-size: 1.3rem; /* เพิ่มขนาดไอคอน */
}

/* ส่วนของการ์ดโพสต์ */
.user-post-item, .liked-post-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

/* ส่วนของรูปภาพ */
.post-thumbnail {
    width: 200px; /* เพิ่มขนาดรูป */
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
}

/* ส่วนของเนื้อหา */
.post-info {
    width: 100%;
    text-align: center;
}

.post-content {
    font-size: 1rem;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.6;
}

/* ส่วนของฟอร์มแก้ไข */
.edit-form {
    width: 100%;
    max-width: 500px;
    margin: 15px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.edit-form textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #eee;
    border-radius: 10px;
    font-size: 1rem;
    resize: vertical;
    margin-bottom: 15px;
}

.edit-form .btn {
    padding: 12px 30px;
    font-size: 1rem;
    border-radius: 25px;
    margin: 5px;
}

/* ปุ่ม Update และ Cancel ในฟอร์มแก้ไข */
.edit-form button[name="update_post"] {
    background: #ffd32a;
    color: #333;
    border: none;
}

.edit-form .btn-secondary {
    background: #e0e0e0;
    color: #333;
    border: none;
}

.edit-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .btn-primary.btn-sm {
        width: 80%;
        max-width: 300px;
    }

    .post-likes {
        font-size: 1rem;
    }

    .post-likes i {
        font-size: 1.2rem;
    }

    .edit-form {
        padding: 15px;
        max-width: 100%;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .user-posts-grid, .liked-posts-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    /* รูปภาพในโพสต์ */
.post-thumbnail {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

    .profile-stats {
        flex-direction: column;
        gap: 1rem;
    }

    .nav-icons {
        flex-wrap: wrap;
    }

    .icon {
        padding: 0.8rem 1.5rem;
    }

    /* คอนเทนเนอร์สำหรับปุ่มทั้งหมด */
.post-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    margin-top: 0.5rem;
    border-top: 1px solid #eee;
}

/* สไตล์พื้นฐานสำหรับปุ่มทั้งหมด */
.post-actions button,
.post-actions a {
    padding: 0.5rem 1.2rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    cursor: pointer;
}

/* สไตล์ปุ่ม Like */
.like-btn {
    background-color: #fff;
    color: #ff4757;
    border: 2px solid #ff4757 !important;
}

.like-btn:hover {
    background-color: #ff4757;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 71, 87, 0.2);
}

.like-btn.liked {
    background-color: #ff4757;
    color: white;
}

/* จำนวน Likes */
.like-count {
    font-size: 0.9rem;
    font-weight: 600;
    color: #ff4757;
    margin: 0 0.5rem;
}

/* ไอคอนในปุ่ม */
.post-actions i {
    font-size: 1.1rem;
}

/* Animation เมื่อกดปุ่ม */
.post-actions button:active {
    transform: scale(0.95);
}

/* Responsive Design */
@media (max-width: 768px) {
    .post-actions {
        flex-wrap: wrap;
    }
    
    .post-actions button,
    .post-actions a {
        font-size: 0.8rem;
        padding: 0.4rem 1rem;
    }
}

}

        </style>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>