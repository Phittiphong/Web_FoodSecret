<?php
// Database connection details
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "webdev"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch member details
$sql = "SELECT firstName, lastName, email FROM campaign";
$result = $conn->query($sql);

$members = array();

if ($result->num_rows > 0) {
    // Fetch the data and store it in an array
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($members);

$conn->close();
?>
