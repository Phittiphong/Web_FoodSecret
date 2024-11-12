<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM posts ORDER BY post_time DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h3>" . $row["username"] . "</h3>";
        echo "<p>" . $row["post_content"] . "</p>";
        if (!empty($row["post_image"])) {
            echo "<img src='uploads/" . $row["post_image"] . "' alt='Post Image' class='img-fluid'>";
        }
        echo "<p class='text-muted'>" . $row["post_time"] . "</p>";
        echo "</div>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>
