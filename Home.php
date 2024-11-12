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

// Get the search query and category from the GET request
$search_query = isset($_GET['query']) ? $_GET['query'] : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

// Get the user ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Fetch the user's role from the database
$role = 'user'; // Default role
if ($user_id) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE Id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
}

// Get the current page or default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Query to fetch posts with limit and offset
$sql = "SELECT posts.*, users.firstName, users.lastName, users.username,
        (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id AND post_likes.user_id = $user_id) AS user_liked
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE 1=1";

// Add filtering by search query if needed
if ($search_query) {
    $search_query = $conn->real_escape_string($search_query);
    $sql .= " AND (posts.post_content LIKE '%$search_query%' OR users.username LIKE '%$search_query%')"; // Specify users.username
}

// Add filtering by category if needed
if ($category_id) {
    $category_id = $conn->real_escape_string($category_id);
    $sql .= " AND posts.category_id = '$category_id'"; // Specify posts.category_id if needed
}

// Order by like count and limit results
$sql .= " ORDER BY like_count DESC, posts.created_at DESC"; // Specify posts.created_at

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
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

$sql = "SELECT id, post_content, post_image, ... FROM posts WHERE 1=1";

// HTML Output
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="button.css" rel="stylesheet">

    <style>
        /* ส่วน Header */
        header {
            background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }

        /* ลิงก์ในเมนู */
        header .nav-link {
            font-weight: 500;
            color: #2b2d42 !important;
            transition: color 0.3s ease;
            position: relative;
        }

        header .nav-link:hover {
            color: #8d99ae !important;
        }

        /* เอฟเฟกต์ขีดเส้นใต้เมนู */
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

        /* ส่วนแสดงโพสต์แบบกริด */
        .post-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        /* แต่ละโพสต์ */
        .post-item {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .post-item:hover {
            transform: translateY(-5px);
        }

        /* รูปภาพและวิดีโอในโพสต์ */
        .post-item img,
        .post-item video {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .post-item .post-title {
            font-weight: bold;
            font-size: 1rem;
            padding: 10px;
        }

        .view-all-btn {
            display: block;
            width: 150px;
            margin: 20px auto;
            background-color: #80FF00;
            color: black;
        }

        .top-bar {
            background-color: #FBD148;
            height: 50px;
            display: flex;
            align-items: center;
        }
    </style>

</head>

<body class="<?php echo htmlspecialchars($theme); ?>-mode">

    <header class="p-3 mb-3 border-bottom">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <img src="photo/Logo.png" alt="Logo" width="150" height="150">
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="home.php" class="nav-link px-2 link-body-emphasis">Home</a></li>
                    <li><a href="contact.php" class="nav-link px-2 link-body-emphasis">Contact</a></li>
                    <li><a href="about.php" class="nav-link px-2 link-body-emphasis">About</a></li>
                    <li><a href="campaign.php" class="nav-link px-2 link-body-emphasis">Campaign</a></li>
                </ul>

                <form id="searchForm" method="GET" action="home.php" role="search">
                    <div class="search-container">
                        <input type="search"
                            name="query"
                            class="form-control"
                            placeholder="Search Food Recipes..."
                            aria-label="Search"
                            value="<?php echo htmlspecialchars($search_query); ?>">

                        <select name="category_id" class="form-select">
                        <option value="" disabled selected>Choose Catagory...</option>
                                    <option value="1">🍗 Fried</option>
                                    <option value="2">🥘 Stir-Fry</option>
                                    <option value="3">🍲 Boil</option>
                                    <option value="4">🥟 Steamed</option>
                                    <option value="5">🥣 Stew</option>
                                    <option value="6">🍖 Grill</option>
                                    <option value="7">🥧 Bake</option>
                                    <option value="8">🥗 Salad</option>
                                    <option value="9">🍛 Curry</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="resetSearch()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>

                <script>
                    function resetSearch() {
                        // Reload the page, clearing the search query
                        window.location.href = 'home.php';
                    }
                </script>

                <!-- Dropdown -->
                <div class="dropdown text-end">
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $profile_picture ? 'uploads/' . $profile_picture : 'photo/user.png'; ?>"
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
        <!-- Button to trigger the modal -->
        <div class="text-center mb-4">
            <h5>Create a New Post</h5>
            <button type="button" class="btn btn-primary" style="background-color: #C85C5C;" data-bs-toggle="modal" data-bs-target="#createPostModal">Create Post</button>
        </div>

        <!-- Create Post Modal -->
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <!-- ปรับส่วน Create Post Modal -->
            <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createPostModalLabel">📝 Create New Post</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?php echo $profile_picture ? 'uploads/' . $profile_picture : 'photo/user.png'; ?>"
                                    alt="User" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($firstname); ?> <?php echo htmlspecialchars($lastname); ?></div>
                                    <div class="text-muted">@<?php echo htmlspecialchars($username); ?></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">📋 Post Detail</label>
                                <textarea class="form-control custom-textarea" name="post_content"
                                    placeholder="Share your recipes..." rows="4" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">🍳 Food Category</label>
                                <select class="form-select custom-select" id="category" name="category_id" required>
                                    <option value="" disabled selected>Choose Catagory...</option>
                                    <option value="1">🍗 Fried</option>
                                    <option value="2">🥘 Stir-Fry</option>
                                    <option value="3">🍲 Boil</option>
                                    <option value="4">🥟 Steamed</option>
                                    <option value="5">🥣 Stew</option>
                                    <option value="6">🍖 Grill</option>
                                    <option value="7">🥧 Bake</option>
                                    <option value="8">🥗 Salad</option>
                                    <option value="9">🍛 Curry</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">📸 Add Photo / Video</label>
                                <div class="custom-file-upload">
                                    <input type="file" class="form-control" id="postImage" name="post_images[]" multiple>
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                </div>
                                <small class="text-muted">*Can upload multiple files</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                ❌ Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                ✅ Post
                            </button>
                        </div>
                    </div>
                </div>
            </div>



            <!-- เพิ่ม Style สำหรับ Modal -->
            <style>
                /* Modal Styles */
                .modal-content {
                    border: none;
                    border-radius: 15px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                }

                .modal-header {
                    background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
                    color: #2b2d42;
                    border-bottom: none;
                    border-radius: 15px 15px 0 0;
                    padding: 1.5rem;
                }

                .modal-title {
                    font-size: 1.25rem;
                    font-weight: 600;
                }

                .modal-body {
                    padding: 2rem;
                }

                /* Custom Textarea */
                .custom-textarea {
                    border: 2px solid #e9ecef;
                    border-radius: 12px;
                    padding: 1rem;
                    transition: all 0.3s ease;
                    resize: none;
                }

                .custom-textarea:focus {
                    border-color: #FBD148;
                    box-shadow: 0 0 0 0.2rem rgba(251, 209, 72, 0.25);
                }

                /* Custom Select */
                .custom-select {
                    border: 2px solid #e9ecef;
                    border-radius: 12px;
                    padding: 0.75rem 1rem;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    appearance: none;
                    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
                    background-repeat: no-repeat;
                    background-position: right 1rem center;
                    background-size: 1em;
                }

                .custom-select:focus {
                    border-color: #FBD148;
                    box-shadow: 0 0 0 0.2rem rgba(251, 209, 72, 0.25);
                }

                /* Custom File Upload */
                .custom-file-upload {
                    position: relative;
                    border: 2px dashed #e9ecef;
                    border-radius: 12px;
                    padding: 2rem;
                    text-align: center;
                    transition: all 0.3s ease;
                    cursor: pointer;
                }

                .custom-file-upload:hover {
                    border-color: #FBD148;
                    background-color: rgba(251, 209, 72, 0.05);
                }

                .upload-icon {
                    font-size: 2rem;
                    color: #6c757d;
                    margin-bottom: 0.5rem;
                }

                /* Buttons */
                .modal-footer {
                    border-top: none;
                    padding: 1.5rem;
                }

                .btn {
                    padding: 0.75rem 1.5rem;
                    border-radius: 25px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }

                .btn-primary {
                    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
                    border: none;
                }

                .btn-primary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 10px rgba(200, 92, 92, 0.3);
                }

                .btn-secondary {
                    background: #6c757d;
                    border: none;
                }

                .btn-secondary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3);
                }

                /* Form Labels */
                .form-label {
                    font-weight: 600;
                    color: #2b2d42;
                    margin-bottom: 0.75rem;
                }

                /* User Profile Section */
                .rounded-circle {
                    border: 2px solid #FBD148;
                    padding: 2px;
                }

                /* Hover Effects */
                .custom-textarea:hover,
                .custom-select:hover {
                    border-color: #FBD148;
                }

                /* Small Text */
                .text-muted {
                    font-size: 0.875rem;
                }

                /* Animation */
                @keyframes modalFadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .modal.show .modal-dialog {
                    animation: modalFadeIn 0.3s ease-out;
                }
            </style>
        </form>



        <!-- Post Grid Container -->
        <div class="post-grid" id="postGrid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="post-item">
                        <?php
                        $file_extension = pathinfo($row['post_image'], PATHINFO_EXTENSION);
                        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                            <!-- Display image if the file is an image -->
                            <img src="uploads/<?php echo htmlspecialchars($row['post_image']); ?>"
                                alt="Post Image"
                                class="post-image-click img-fluid"
                                data-post-id="<?php echo $row['id']; ?>"
                                data-post-image="uploads/<?php echo htmlspecialchars($row['post_image']); ?>"
                                data-post-username="<?php echo htmlspecialchars($row['username']); ?>"
                                data-post-fullname="<?php echo htmlspecialchars($row['firstName']) . ' ' . htmlspecialchars($row['lastName']); ?>"
                                data-post-content="<?php echo htmlspecialchars($row['post_content']); ?>"
                                data-post-time="<?php echo date('F j, Y, g:i a', strtotime($row['post_time'])); ?>"
                                style="cursor: pointer;">
                        <?php elseif (in_array($file_extension, ['mp4'])): ?>
                            <!-- Display video if the file is a video -->
                            <video class="post-video img-fluid" controls
                                data-post-id="<?php echo $row['id']; ?>"
                                data-post-image="uploads/<?php echo htmlspecialchars($row['post_image']); ?>"
                                data-post-username="<?php echo htmlspecialchars($row['username']); ?>"
                                data-post-fullname="<?php echo htmlspecialchars($row['firstName']) . ' ' . htmlspecialchars($row['lastName']); ?>"
                                data-post-content="<?php echo htmlspecialchars($row['post_content']); ?>"
                                data-post-time="<?php echo date('F j, Y, g:i a', strtotime($row['post_time'])); ?>"
                                style="cursor: pointer;">
                                <source src="uploads/<?php echo htmlspecialchars($row['post_image']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>

                        <div class="post-item-footer">
                            <button class="btn btn-outline-primary like-btn" data-post-id="<?php echo $row['id']; ?>">❤️</button>
                            <span class="like-count" id="like-count-<?php echo $row['id']; ?>"><?php echo $row['like_count']; ?></span>
                            <button class="btn btn-danger report-btn" onclick="window.location.href='reportForm.php?post_id=<?php echo $row['id']; ?>'">🚨 Report</button>
                            <!-- Delete Button (only visible to post owner) -->
                            <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                                <button class="btn btn-delete" onclick="showDeleteConfirmation(<?php echo $row['id']; ?>)">
                                    🗑️ Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts available.</p>
            <?php endif; ?>
        </div>




        <style>
            /* ปรับแต่งฟอร์มค้นหา */
            #searchForm {
                display: flex;
                align-items: center;
                gap: 10px;
                background: white;
                padding: 8px 15px;
                border-radius: 30px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                position: relative;
                left: -50px;
            }

            /* ปรับแต่ง input search */
            #searchForm .form-control {
                border: 2px solid #f0f0f0;
                border-radius: 20px;
                padding: 10px 20px;
                font-size: 14px;
                transition: all 0.3s ease;
                min-width: 200px;
            }

            #searchForm .form-control:focus {
                border-color: #C85C5C;
                box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.15);
                outline: none;
            }

            /* ปรับแต่ง select */
            #searchForm .form-select {
                border: 2px solid #f0f0f0;
                border-radius: 20px;
                padding: 10px 35px 10px 20px;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
                background-position: right 15px center;
                min-width: 150px;
            }

            #searchForm .form-select:focus {
                border-color: #C85C5C;
                box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.15);
            }

            /* ปรับแต่งปุ่ม Search */
            #searchForm .btn-primary {
                background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
                border: none;
                padding: 10px 25px;
                border-radius: 20px;
                font-weight: 500;
                color: white;
                transition: all 0.3s ease;
            }

            #searchForm .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
            }

            /* ปรับแต่งปุ่ม Reset */
            #searchForm .btn-outline-danger {
                border: 2px solid #C85C5C;
                color: #C85C5C;
                padding: 10px 25px;
                border-radius: 20px;
                font-weight: 500;
                transition: all 0.3s ease;
                background: transparent;
            }

            #searchForm .btn-outline-danger:hover {
                background: #C85C5C;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
            }

            /* Hover effects */
            #searchForm input:hover,
            #searchForm select:hover {
                border-color: #C85C5C;
            }

            /* Active state */
            #searchForm .btn:active {
                transform: scale(0.95);
            }

            /* Responsive */
            @media (max-width: 768px) {
                #searchForm {
                    flex-wrap: wrap;
                    padding: 10px;
                }

                #searchForm .form-control,
                #searchForm .form-select {
                    width: 100%;
                    margin-bottom: 10px;
                }

                #searchForm .btn {
                    width: calc(50% - 5px);
                    padding: 8px 15px;
                }
            }

            /* เพิ่ม Animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            #searchForm {
                animation: fadeIn 0.3s ease-out;
            }

            .post-image img {
                width: 300px;
                /* Set fixed width */
                height: 300px;
                /* Set fixed height */
                object-fit: cover;
                /* Ensures the image maintains its aspect ratio while covering the space */
                border-radius: 10px;
                /* Optional: Adds rounded corners to the image */
                display: block;
                margin: 0 auto;
            }

            /* Ensure all buttons (Like, Report, Delete) are the same size */
            .post-item .btn {
                flex: 1;
                /* Make each button take equal space */
                padding: 10px 15px;
                /* Consistent padding for all buttons */
                font-size: 1rem;
                border-radius: 25px;
                text-align: center;
                margin: 0 5px;
                transition: all 0.3s ease-in-out;
                min-width: 100px;
                /* Set a minimum width for buttons */
            }

            /* Like button */
            .post-item .btn-outline-primary {
                color: #007bff;
                border-color: #007bff;
            }

            .post-item .btn-outline-primary:hover {
                background-color: #007bff;
                color: white;
            }

            /* Report button */
            .post-item .btn-danger {
                background-color: #dc3545;
                color: white;
                border-color: #dc3545;
            }

            .post-item .btn-danger:hover {
                background-color: #c82333;
            }

            /* Delete button */
            .post-item .btn-delete {
                background-color: #6c757d;
                color: white;
                border-color: #6c757d;
            }

            .post-item .btn-delete:hover {
                background-color: #5a6268;
            }

            /* Like count styling */
            #like-count-<?php echo $row['id']; ?> {
                margin-left: 10px;
                font-weight: bold;
            }

            /* ส่วนปุ่มกดไลค์และแสดงความคิดเห็น */
            .post-item-footer {
                padding: 1rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8f9fa;
            }

            /* จำนวนไลค์ */
            .like-count {
                font-size: 1.1rem;
                color: #C85C5C;
                font-weight: 600;
                margin: 0 1rem;
            }

            /* ปุ่มทั่วไป */
            .btn {
                padding: 0.8rem 1.5rem;
                border-radius: 25px;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            /* ปุ่มหลัก */
            .btn-primary {
                background: #C85C5C;
                border: none;
            }

            .btn-primary:hover {
                background: #b94545;
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(200, 92, 92, 0.3);
            }

            /* ปุ่มไลค์ */
            .like-btn {
                background: transparent;
                border: 2px solid #C85C5C;
                color: #C85C5C;
                padding: 0.5rem 1rem;
                font-size: 1.2rem;
            }

            .like-btn:hover {
                background: #C85C5C;
                color: white;
            }

            /* ส่วนหน้าต่างป๊อปอัพ */
            .modal-content {
                border-radius: 15px;
                overflow: hidden;
            }

            .modal-header {
                background: #FBD148;
                color: #2b2d42;
                border-bottom: none;
            }

            .modal-body {
                padding: 2rem;
            }

            .btn-danger {
                background-color: #dc3545;
                border: none;
            }

            .btn-delete {
                background-color: #6c757d;
                color: white;
                border: none;
            }

            .btn-delete:hover {
                background-color: #5a6268;
            }


            /* General hover effect for all buttons */
            .post-item .btn:hover {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
        </style>

        <!-- Post Modal -->
        <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="postModalLabel">📝 Post Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body with 70% Post Details and 30% Comments -->
                    <div class="modal-body d-flex">
                        <!-- Post Details Section (70%) -->
                        <div class="post-details-section me-4">
                            <div id="postDetails" class="post-content-wrapper">
                                <!-- User Info -->
                                <div class="user-info-section mb-4">
                                    <div class="d-flex align-items-center">
                                        <img src="photo/user.png" alt="Profile" class="profile-image">
                                        <div class="user-details">
                                            <h4 id="postModalFullName" class="user-name mb-0"></h4>
                                            <span id="postModalUsername" class="username"></span>
                                            <span id="postModalTime" class="post-time"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Post Image -->
                                <div class="post-image-container mb-4">
                                    <img src="" id="postModalImage" alt="Post Image" class="post-image">
                                </div>

                                <!-- Post Content -->
                                <div class="post-content">
                                    <p id="postModalContent" class="content-text"></p>
                                </div>


                            </div>
                        </div>

                        <!-- Comments Section (30%) -->
                        <div class="comments-section">
                            <div class="comments-header">
                                <h6>💭 Comments</h6>
                            </div>

                            <!-- Comments List -->
                            <div class="comments-list-container">
                                <ul id="commentsList" class="comments-list"></ul>
                            </div>

                            <!-- Add New Comment -->
                            <div class="add-comment-section">
                                <div class="comment-input-wrapper">
                                    <img src="<?php echo $profile_picture ? 'uploads/' . $profile_picture : 'photo/user.png'; ?>"
                                        alt="User" class="commenter-avatar">
                                    <div class="comment-input-container">
                                        <textarea id="commentText" class="comment-input"
                                            placeholder="Add your comment..." rows="3"></textarea>
                                        <button type="button" class="btn btn-primary btn-comment-submit" id="submitComment">
                                            Post Comments
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Modal Styles */
            .modal-content {
                border: none;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .modal-header {
                background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
                padding: 1.5rem;
                border: none;
            }

            .modal-title {
                color: #2b2d42;
                font-weight: 600;
                font-size: 1.25rem;
            }

            .modal-body {
                padding: 0;
                height: 80vh;
                overflow: hidden;
            }

            /* Post Details Section */
            .post-details-section {
                flex: 0 0 70%;
                padding: 2rem;
                overflow-y: auto;
                border-right: 1px solid #eee;
            }

            .post-content-wrapper {
                max-width: 800px;
                margin: 0 auto;
            }

            /* User Info Section */
            .user-info-section {
                padding-bottom: 1rem;
                border-bottom: 1px solid #eee;
            }

            .profile-image {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                margin-right: 1rem;
                border: 2px solid #FBD148;
                padding: 2px;
            }

            .user-details {
                flex-grow: 1;
            }

            .user-name {
                font-size: 1.1rem;
                font-weight: 600;
                color: #2b2d42;
            }

            .username {
                color: #6c757d;
                font-size: 0.9rem;
                display: block;
            }

            .post-time {
                font-size: 0.8rem;
                color: #adb5bd;
                display: block;
            }

            /* Post Image Container */
            .post-image-container {
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .post-image {
                width: 100%;
                height: auto;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .post-image:hover {
                transform: scale(1.02);
            }

            /* Post Content */
            .content-text {
                font-size: 1.1rem;
                line-height: 1.6;
                color: #2b2d42;
                padding: 1.5rem 0;
            }

            /* Interaction Buttons */
            .interaction-buttons {
                display: flex;
                gap: 1rem;
                padding: 1rem 0;
                border-top: 1px solid #eee;
            }

            .btn-interaction {
                flex: 1;
                padding: 0.75rem;
                border-radius: 25px;
                border: none;
                background: #f8f9fa;
                color: #6c757d;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .btn-interaction:hover {
                background: #e9ecef;
                transform: translateY(-2px);
            }

            .btn-interaction i {
                font-size: 1.1rem;
            }

            /* Comments Section */
            .comments-section {
                flex: 0 0 30%;
                background: #f8f9fa;
                padding: 2rem;
                margin-right: 2rem;
                /* เพิ่มระยะห่างจากขอบขวา */
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            }

            .comments-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
            }

            .comments-header h6 {
                font-weight: 600;
                color: #2b2d42;
                margin: 0;
            }

            .comments-count {
                color: #6c757d;
                font-size: 0.9rem;
            }

            .comments-list-container {
                flex: 1;
                overflow-y: auto;
                margin-bottom: 1rem;
            }

            .comments-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .comments-list li {
                padding: 1rem;
                background: white;
                border-radius: 12px;
                margin-bottom: 0.5rem;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            /* Add Comment Section */
            .add-comment-section {
                margin-top: auto;
                padding-top: 1rem;
                border-top: 1px solid #dee2e6;
            }

            .comment-input-wrapper {
                display: flex;
                gap: 1rem;
            }

            .commenter-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }

            .comment-input-container {
                flex: 1;
            }

            .comment-input {
                width: 100%;
                border: 1px solid #dee2e6;
                border-radius: 12px;
                padding: 0.75rem;
                resize: none;
                margin-bottom: 0.5rem;
                font-size: 0.95rem;
            }

            .comment-input:focus {
                outline: none;
                border-color: #FBD148;
                box-shadow: 0 0 0 0.2rem rgba(251, 209, 72, 0.25);
            }

            .btn-comment-submit {
                background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
                border: none;
                border-radius: 20px;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
                float: right;
            }

            .btn-comment-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(200, 92, 92, 0.3);
            }

            /* Scrollbar Styling */
            ::-webkit-scrollbar {
                width: 6px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb {
                background: #C85C5C;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #b94545;
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .modal.show .modal-content {
                animation: fadeIn 0.3s ease-out;
            }

            /* Responsive Adjustments */
            @media (max-width: 992px) {
                .modal-body {
                    flex-direction: column;
                    height: auto;
                }

                .post-details-section,
                .comments-section {
                    flex: 0 0 100%;
                    border-right: none;
                }

                .post-details-section {
                    border-bottom: 1px solid #eee;
                }
            }
        </style>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const reportButtons = document.querySelectorAll('.report-btn');
                const reportPostIdInput = document.getElementById('reportPostId');

                reportButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const postId = button.getAttribute('data-post-id');
                        reportPostIdInput.value = postId;
                    });
                });
            });


            function openReportModal(postId) {
                // Set the post ID in the hidden input field of the report modal
                document.getElementById('reportPostId').value = postId;

                // Create a Bootstrap modal instance and show it
                var reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
                reportModal.show();
            }
        </script>

        <script>
            document.getElementById('reportForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                const reportTitle = document.getElementById('reportTitle').value;
                const reportDescription = document.getElementById('reportDescription').value;
                const reportPostId = document.getElementById('reportPostId').value;

                // Prepare form data
                const formData = new FormData();
                formData.append('report_title', reportTitle);
                formData.append('report_description', reportDescription);
                formData.append('reportPostId', reportPostId);

                // Send the data using fetch
                fetch('submit_report.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(result => {
                        alert('Report submitted successfully!');
                        // Optionally close the modal after submission
                        var reportModal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
                        reportModal.hide();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('There was an error submitting your report.');
                    });
            });
        </script>



        <script>
            document.querySelectorAll('.post-image-click').forEach(function(image) {
                image.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');

                    // Set the postId in the modal's data attribute so it knows which post to comment on
                    document.getElementById('postModal').setAttribute('data-post-id', postId);

                    // Load post content and comments (AJAX)
                    fetch(`get_post_comments.php?post_id=${postId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Populate modal with post details
                            document.getElementById('postModalContent').textContent = data.post_content;

                            const commentsList = document.getElementById('commentsList');
                            commentsList.innerHTML = ''; // Clear existing comments

                            // Populate comments
                            data.comments.forEach(comment => {
                                const commentElement = document.createElement('li');
                                commentElement.innerHTML = `<strong>${comment.user}</strong>: ${comment.text}`;
                                commentsList.appendChild(commentElement);
                            });

                            // Show the modal
                            const postModal = new bootstrap.Modal(document.getElementById('postModal'));
                            postModal.show();
                        });
                });
            });

            // Handle comment submission via AJAX, but first remove any existing event listener
            document.getElementById('submitComment').removeEventListener('click', submitCommentHandler); // Remove old event listener if it exists

            document.getElementById('submitComment').addEventListener('click', submitCommentHandler); // Attach the event handler

            function submitCommentHandler() {
                const commentText = document.getElementById('commentText').value;
                const postId = document.getElementById('postModal').getAttribute('data-post-id'); // Get the post ID from the modal

                // Make sure comment isn't empty
                if (!commentText.trim()) {
                    alert('Please write a comment');
                    return;
                }

                // AJAX request to add the comment
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'add_comment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            // Add new comment to the list
                            const commentsList = document.getElementById('commentsList');
                            const newComment = document.createElement('li');
                            newComment.innerHTML = `<strong>You</strong>: ${response.comment}`;
                            commentsList.appendChild(newComment);

                            // Clear the comment input field
                            document.getElementById('commentText').value = '';
                        } else {
                            alert('Error posting comment');
                        }
                    }
                };

                // Send the comment data
                xhr.send(`post_id=${postId}&comment=${encodeURIComponent(commentText)}`);
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle image click to show modal
                document.querySelectorAll('.post-image-click').forEach(function(image) {
                    image.addEventListener('click', function() {
                        // Get data attributes from the clicked image element
                        const postId = this.getAttribute('data-post-id');
                        const postImage = this.getAttribute('data-post-image');
                        const postUsername = this.getAttribute('data-post-username');
                        const postFullName = this.getAttribute('data-post-fullname');
                        const postContent = this.getAttribute('data-post-content');
                        const postTime = this.getAttribute('data-post-time');

                        // Set modal content with the retrieved data
                        document.getElementById('postModalImage').src = postImage;
                        document.getElementById('postModalFullName').textContent = postFullName;
                        document.getElementById('postModalUsername').textContent = '@' + postUsername;
                        document.getElementById('postModalContent').textContent = postContent;
                        document.getElementById('postModalTime').textContent = 'Posted on: ' + postTime;

                        // Open the modal
                        const postModal = new bootstrap.Modal(document.getElementById('postModal'));
                        postModal.show();

                        // Attach new event listeners for the buttons (avoiding duplication)
                        const likeBtn = document.getElementById('likeBtn');
                        const commentBtn = document.getElementById('commentBtn');
                        const deleteBtn = document.getElementById('deleteBtn');

                        // Clear any existing event listeners using the cloneNode method
                        if (likeBtn) {
                            likeBtn.replaceWith(likeBtn.cloneNode(true));
                            document.getElementById('likeBtn').addEventListener('click', function() {
                                handleLike(postId); // Pass the post ID to the like handler
                            });
                        }

                        if (commentBtn) {
                            commentBtn.replaceWith(commentBtn.cloneNode(true));
                            document.getElementById('commentBtn').addEventListener('click', function() {
                                handleComment(postId); // Pass the post ID to the comment handler
                            });
                        }

                        if (deleteBtn) {
                            deleteBtn.replaceWith(deleteBtn.cloneNode(true));
                            document.getElementById('deleteBtn').addEventListener('click', function() {
                                confirmDelete(postId); // Pass the post ID to the delete handler
                            });
                        }
                    });
                });
            });
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentPage = 1; // Start with page 1

                // Event listener for the See More button
                document.getElementById('seeMoreBtn').addEventListener('click', function() {
                    currentPage++; // Increment the page number

                    // Send AJAX request to fetch more posts
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', `load_more_posts.php?page=${currentPage}`, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = xhr.responseText;
                            const postGrid = document.getElementById('postGrid');

                            // Append the new posts to the grid
                            postGrid.insertAdjacentHTML('beforeend', response);

                            // If no more posts are returned, hide the "See More" button
                            if (response.trim() === '') {
                                document.getElementById('seeMoreBtn').style.display = 'none';
                            }
                        }
                    };
                    xhr.send();
                });
            });
        </script>
        <script>
            // Confirm delete post functionality
            document.querySelectorAll('.delete-post-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent immediate navigation
                    const postId = this.getAttribute('data-post-id'); // Get post ID from data attribute
                    // Show confirmation dialog
                    if (confirm('Are you sure you want to delete this post?')) {
                        window.location.href = 'delete_post.php?post_id=' + postId;
                    }
                });
            });
        </script>


        <!-- Comment Modal -->
        <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel">Post Comments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Post content will be loaded here dynamically -->
                        <div id="postContent"></div>

                        <!-- Comments Section -->
                        <div id="commentsSection">
                            <h6>Comments</h6>
                            <ul id="commentsList" class="list-unstyled"></ul>
                        </div>

                        <!-- Add New Comment -->
                        <div class="mt-3">
                            <form id="commentForm">
                                <textarea class="form-control" id="commentText" rows="3" placeholder="Write your comment..."></textarea>
                                <button type="button" class="btn btn-primary mt-2" id="submitComment">Post Comment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> <br><br><br>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Bootstrap modal instance
                var postModalElement = document.getElementById('postModal');
                var postModalInstance = bootstrap.Modal.getInstance(postModalElement);

                // Add event listener for modal close button
                document.querySelector('.btn-close').addEventListener('click', function() {
                    if (postModalInstance) {
                        postModalInstance.hide(); // Hide the modal
                    }

                    // Remove the grey overlay after the modal is closed
                    postModalElement.addEventListener('hidden.bs.modal', function() {
                        document.body.classList.remove('modal-open');
                        document.querySelector('.modal-backdrop').remove(); // Remove modal backdrop
                    });
                });
            });
        </script>

        <script>
            // Function to load and refresh comments
            function loadComments(postId) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_post_comments.php?post_id=' + postId, true);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        var commentsList = document.getElementById('commentsList');
                        commentsList.innerHTML = ''; // Clear existing comments

                        // Loop through the comments and append them to the list
                        response.comments.forEach(function(comment) {
                            var deleteBtn = (comment.user_id == <?php echo $_SESSION['user_id']; ?>) ?
                                `<button class="btn btn-danger btn-sm delete-comment" data-comment-id="${comment.id}">Delete</button>` : '';

                            commentsList.innerHTML += `
                    <li>
                        <strong>${comment.user}</strong>: ${comment.text}
                        ${deleteBtn}
                    </li>
                `;
                        });

                        // Attach event listeners to the delete buttons
                        document.querySelectorAll('.delete-comment').forEach(function(button) {
                            button.addEventListener('click', function() {
                                var commentId = this.getAttribute('data-comment-id');
                                deleteComment(commentId, postId);
                            });
                        });
                    }
                };

                xhr.send();
            }

            // Function to delete a comment
            function deleteComment(commentId, postId) {
                if (confirm('Are you sure you want to delete this comment?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete_comment.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                alert('Comment deleted successfully.');
                                loadComments(postId);
                            } else {
                                alert(response.message);
                            }
                        }
                    };

                    xhr.send('comment_id=' + commentId);
                }
            }

            // Initial call to load comments when the page loads
            loadComments(<?php echo $post_id; ?>);
        </script>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmModalLabel">ยืนยันการลบโพสต์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>คุณแน่ใจหรือไม่ที่จะลบโพสต์นี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">ลบโพสต์</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-content">
                <div class="loading-spinner" id="loadingSpinner"></div>
                <div class="success-checkmark" id="successCheckmark" style="display: none;">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                    </div>
                </div>
                <div class="loading-text" id="loadingText">กำลังลบโพสต์...</div>
            </div>
        </div>

        <style>
            /* Loading Overlay */
            .loading-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.95);
                z-index: 9999;
                justify-content: center;
                align-items: center;
            }

            .loading-content {
                text-align: center;
            }

            .loading-spinner {
                width: 60px;
                height: 60px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #C85C5C;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }

            .loading-text {
                color: #333;
                font-size: 1.2rem;
                margin-top: 15px;
            }

            /* Success Checkmark */
            .success-checkmark {
                width: 80px;
                height: 80px;
                margin: 0 auto 20px;
            }

            .check-icon {
                width: 80px;
                height: 80px;
                position: relative;
                border-radius: 50%;
                box-sizing: content-box;
                border: 4px solid #4CAF50;
            }

            .check-icon::before {
                top: 3px;
                left: -2px;
                width: 30px;
                transform-origin: 100% 50%;
                border-radius: 100px 0 0 100px;
            }

            .check-icon::after {
                top: 0;
                left: 30px;
                width: 60px;
                transform-origin: 0 50%;
                border-radius: 0 100px 100px 0;
                animation: rotate-circle 4.25s ease-in;
            }

            .icon-line {
                height: 5px;
                background-color: #4CAF50;
                display: block;
                border-radius: 2px;
                position: absolute;
                z-index: 10;
            }

            .icon-line.line-tip {
                top: 46px;
                left: 14px;
                width: 25px;
                transform: rotate(45deg);
                animation: icon-line-tip 0.75s;
            }

            .icon-line.line-long {
                top: 38px;
                right: 8px;
                width: 47px;
                transform: rotate(-45deg);
                animation: icon-line-long 0.75s;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            @keyframes icon-line-tip {
                0% {
                    width: 0;
                    left: 1px;
                    top: 19px;
                }

                54% {
                    width: 0;
                    left: 1px;
                    top: 19px;
                }

                70% {
                    width: 50px;
                    left: -8px;
                    top: 37px;
                }

                84% {
                    width: 17px;
                    left: 21px;
                    top: 48px;
                }

                100% {
                    width: 25px;
                    left: 14px;
                    top: 46px;
                }
            }

            @keyframes icon-line-long {
                0% {
                    width: 0;
                    right: 46px;
                    top: 54px;
                }

                65% {
                    width: 0;
                    right: 46px;
                    top: 54px;
                }

                84% {
                    width: 55px;
                    right: 0px;
                    top: 35px;
                }

                100% {
                    width: 47px;
                    right: 8px;
                    top: 38px;
                }
            }
        </style>

        <script>
            let postIdToDelete = null;

            function showDeleteConfirmation(postId) {
                postIdToDelete = postId;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                deleteModal.show();
            }

            // Add event listener for confirmation button
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (postIdToDelete) {
                    // Hide the confirmation modal
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                    deleteModal.hide();

                    // Show loading overlay with loading spinner
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    const loadingSpinner = document.getElementById('loadingSpinner');
                    const successCheckmark = document.getElementById('successCheckmark');
                    const loadingText = document.getElementById('loadingText');

                    loadingOverlay.style.display = 'flex';
                    loadingSpinner.style.display = 'block';
                    successCheckmark.style.display = 'none';
                    loadingText.textContent = 'กำลังลบโพสต์...';

                    // Call delete_post.php using fetch
                    fetch(`delete_post.php?post_id=${postIdToDelete}`)
                        .then(response => {
                            if (response.ok) {
                                // Show success message
                                loadingSpinner.style.display = 'none';
                                successCheckmark.style.display = 'block';
                                loadingText.textContent = 'ลบโพสต์เรียบร้อยแล้ว';

                                // Redirect after showing success message
                                setTimeout(() => {
                                    window.location.href = 'Home.php';
                                }, 1500);
                            } else {
                                // Show error message
                                loadingSpinner.style.display = 'none';
                                loadingText.textContent = 'เกิดข้อผิดพลาดในการลบโพสต์';
                                loadingText.style.color = '#dc3545';

                                // Hide overlay after error message
                                setTimeout(() => {
                                    loadingOverlay.style.display = 'none';
                                }, 1500);
                            }
                        })
                        .catch(error => {
                            // Handle network errors
                            loadingSpinner.style.display = 'none';
                            loadingText.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                            loadingText.style.color = '#dc3545';

                            setTimeout(() => {
                                loadingOverlay.style.display = 'none';
                            }, 1500);
                        });
                }
            });
        </script>

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

        <!-- Additional CSS -->
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

            html,
            body {
                margin: 0;
                padding: 0;
                height: 100%;
            }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            main {
                flex: 1;
                /* This makes the main content area take up the available space */
            }

            /* รูปแบบมาตรฐานของส่วน Header */
            header {
                background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
                /* ไล่สีจากเหลืองอ่อนไปเหลืองเข้ม */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                /* เงาใต้ header */
                padding: 0.5rem 0;
            }

            header .nav-link {
                font-weight: 500;
                /* ความหนาของตัวอักษร */
                color: #2b2d42 !important;
                /* สีตัวอักษร */
                transition: color 0.3s ease;
                /* การเปลี่ยนสีแบบค่อยๆ เปลี่ยน */
                position: relative;
            }

            header .nav-link:hover {
                color: #8d99ae !important;
                /* สีตัวอักษรเมื่อนำเมาส์ชี้ */
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

            /* รูปแบบมาตรฐานของส่วน Footer */
            footer {
                background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
                /* ไล่สีจากแดงอ่อนไปแดงเข้ม */
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
                color: rgba(255, 255, 255, 0.8) !important;
                /* สีขาวแบบโปร่งใส */
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
                background: #FBD148;
                /* สีปุ่มเหลือง */
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
        </style>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

        <script>
            document.querySelectorAll('.like-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    const likeButton = this;
                    const likeCountSpan = document.getElementById(`like-count-${postId}`);

                    // Send an AJAX request to like_post.php
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'like_post.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'liked') {
                                likeButton.textContent = '❤️';
                                likeButton.classList.replace('btn-outline-primary', 'btn-primary');
                            } else if (response.status === 'unliked') {
                                likeButton.textContent = '🤍';
                                likeButton.classList.replace('btn-primary', 'btn-outline-primary');
                            }
                            // Update the like count
                            likeCountSpan.textContent = response.like_count;

                            // เพิ่มการ refresh หน้าเว็บหลังจากอัพเดทไลค์
                            setTimeout(() => {
                                location.reload();
                            }, 50); // รอ 500ms ก่อน refresh เพื่อให้เห็น animation การกดไลค์
                        }
                    };
                    xhr.send(`post_id=${encodeURIComponent(postId)}`);
                });

                // Handle comment button functionality
                document.querySelectorAll('.comment-btn').forEach(function(commentButton) {
                    commentButton.addEventListener('click', function() {
                        var postId = this.getAttribute('data-post-id'); // Line: 376

                        // Send AJAX request to fetch the post's comments
                        fetch(`get_post_comments.php?post_id=${postId}`) // Line: 379
                            .then(response => response.json()) // Line: 380
                            .then(data => {
                                // Show the post content
                                document.getElementById('postContent').innerHTML = `<p>${data.post_content}</p>`; // Line: 383

                                const commentsList = document.getElementById('commentsList'); // Line: 386
                                commentsList.innerHTML = ''; // Clear existing comments

                                // Inside the code that handles displaying comments
                                data.comments.forEach(comment => { // Line: 390
                                    commentsList.innerHTML += `
                        <li>
                            <strong>${comment.user}</strong>: ${comment.text}
                            ${isCommentOwner ? `<span class="delete-comment-btn" data-comment-id="${comment.id}" style="color:red; cursor:pointer;"> ❌</span>` : ''}
                        </li>
                    `; // Line: 395-400
                                });

                            });
                    });
                });
            });
        </script>
</body>

</html>