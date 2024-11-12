<?php
session_start(); // Start the session to use session variables

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

// Get the user ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Fetch the logged-in user's details
if ($user_id) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE Id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "User not logged in.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_content = $_POST['post_content'];
    $category_id = $_POST['category_id'];

    // File upload handling
    $uploadDir = 'uploads/';
    $uploadedFiles = [];

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!empty($_FILES['post_images']['name'][0])) {
        foreach ($_FILES['post_images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['post_images']['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            // Ensure file type is valid
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4'];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $uploadedFiles[] = $fileName;
                } else {
                    echo "Failed to upload file: " . $fileName;
                    exit;
                }
            } else {
                echo "Invalid file type: " . $fileName;
                exit;
            }
        }
    }

    // Convert uploaded file names to a comma-separated string
    $post_image = !empty($uploadedFiles) ? implode(',', $uploadedFiles) : null;

    // Insert post into the database
    $stmt = $conn->prepare("INSERT INTO posts (username, post_content, post_image, category_id, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $username, $post_content, $post_image, $category_id, $user_id);

    if ($stmt->execute()) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>กำลังโพสต์...</title>
            <style>
                .loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                }
    
                .loading-popup {
                    background: white;
                    padding: 2rem;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    text-align: center;
                    width: 300px;
                    position: relative;
                    animation: popIn 0.3s ease-out;
                }
    
                .loading-spinner {
                    width: 50px;
                    height: 50px;
                    border: 5px solid #f3f3f3;
                    border-top: 5px solid #C85C5C;
                    border-radius: 50%;
                    margin: 0 auto 20px;
                    animation: spin 1s linear infinite;
                }
    
                .loading-message {
                    color: #2b2d42;
                    font-size: 1.2rem;
                    margin: 15px 0;
                    animation: fade 1.5s ease-in-out infinite;
                }
    
                .progress-bar {
                    width: 100%;
                    height: 4px;
                    background: #f3f3f3;
                    border-radius: 2px;
                    margin-top: 15px;
                    overflow: hidden;
                }
    
                .progress {
                    width: 0%;
                    height: 100%;
                    background: #C85C5C;
                    animation: progress 2s ease-in-out forwards;
                }
    
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
    
                @keyframes fade {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
    
                @keyframes progress {
                    0% { width: 0%; }
                    100% { width: 100%; }
                }
    
                @keyframes popIn {
                    0% {
                        transform: scale(0.8);
                        opacity: 0;
                    }
                    100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            </style>
        </head>
        <body>
            <div class="loading-overlay">
                <div class="loading-popup">
                    <div class="loading-spinner"></div>
                    <div class="loading-message">กำลังโพสต์...</div>
                    <div class="progress-bar">
                        <div class="progress"></div>
                    </div>
                </div>
            </div>
    
            <script>
                // รีไดเร็คไปยังหน้า home.php หลังจาก 2 วินาที
                setTimeout(() => {
                    window.location.href = 'home.php';
                }, 2000);
            </script>
        </body>
        </html>
        <?php
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();}
    ?>
