<?php
session_start(); // Start the session to use session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to be logged in to create a post.");
}

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

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Get the post data from the form
$post_content = isset($_POST['post_content']) ? $_POST['post_content'] : '';
$post_image = isset($_FILES['post_image']) ? $_FILES['post_image']['name'] : ''; // Example for image upload

// Handle file upload
if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($_FILES['post_image']['name']);
    
    if (move_uploaded_file($_FILES['post_image']['tmp_name'], $upload_file)) {
        $post_image = basename($_FILES['post_image']['name']);
    } else {
        echo "Error uploading file.";
        $post_image = ''; // Handle error appropriately
    }
}

// Prepare and execute the SQL statement to insert the new post
$sql = "INSERT INTO posts (post_content, post_image, user_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $post_content, $post_image, $user_id);

// Execute the statement
if ($stmt->execute()) {
    // Redirect or give a success message
    echo "Post created successfully.";
} else {
    echo "Error creating post: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
