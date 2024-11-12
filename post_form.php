<?php
session_start();
include 'connect.php'; // Ensure this file includes your database connection setup

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to create a post.";
    exit;
}

// Fetch the logged-in user's details
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, firstName, lastName FROM users WHERE Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $username = '@' . $user['username'];
    $firstName = $user['firstName'];
    $lastName = $user['lastName'];
} else {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="readonly.css" rel="stylesheet">

</head>
<body>
    <div class="container mt-5">
        <h1>Create a New Post</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
            </div>
            <!-- First Name -->
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" readonly>
            </div>
            <!-- Last Name -->
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" readonly>
            </div>
            <!-- Post Content -->
            <div class="mb-3">
                <label for="post_content" class="form-label">Post Content</label>
                <textarea class="form-control" id="post_content" name="post_content" rows="3" required></textarea>
            </div>
            <!-- Category -->
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category_id" required>
                    <option value="1">ทอด</option>
                    <option value="2">ผัด</option>
                    <option value="3">ต้ม</option>
                    <option value="4">นึ่ง</option>
                    <option value="5">ตุ๋น</option>
                    <option value="6">ย่าง</option>
                    <option value="7">อบ</option>
                    <option value="8">ยำ</option>
                    <option value="9">แกง</option>
                </select>
            </div>
            <!-- Post Image -->
            <div class="mb-3">
                <label for="post_image" class="form-label">Post Image (optional)</label>
                <input class="form-control" type="file" id="post_image" name="post_image" multiple>
            </div>
            <button type="button" class="btn btn-danger" onclick="window.location.href='home.php';">Back</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
