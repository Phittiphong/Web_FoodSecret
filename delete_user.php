<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = (int)$_POST['id'];
    
    // Get user details before deletion for logging
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    try {
        if ($stmt->execute()) {
            // Log successful deletion
            error_log("User deleted successfully - ID: $user_id, Username: {$user['username']}, Email: {$user['email']}");
            $_SESSION['delete_message'] = [
                'type' => 'success',
                'text' => "User {$user['username']} has been successfully deleted."
            ];
            header('Location: admin_dashboard.php');
            exit();
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error deleting user - ID: $user_id, Error: " . $e->getMessage());
        $_SESSION['delete_message'] = [
            'type' => 'error',
            'text' => "Error deleting user. Please try again."
        ];
        header('Location: admin_dashboard.php');
        exit();
    }
    
    $stmt->close();
}

$conn->close();
header('Location: admin_dashboard.php');
exit();
?>