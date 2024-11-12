<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reportTitle = $_POST['report_title'];
    $reportDescription = $_POST['report_description'];
    $postId = $_POST['post_id'];  // Get the post_id from the form

    // Insert the report into the reports table
    $stmt = $conn->prepare("INSERT INTO reports (post_id, report_title, report_description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $postId, $reportTitle, $reportDescription);

    if ($stmt->execute()) {
        echo "Report submitted successfully!";
        // Optionally, you can redirect to a success page
        header("Location: home.php?report=success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
