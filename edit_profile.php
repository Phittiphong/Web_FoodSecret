<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Check if user is logged in and fetch the role
if (isset($_SESSION['user_id'])) {
    include 'connect.php';
    $user_id = $_SESSION['user_id'];

    // Fetch user role from database
    $sql = "SELECT role FROM users WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $role = $user['role'];
    } else {
        $role = 'guest'; // Default role for non-logged-in users
    }
} else {
    $role = 'guest'; // Default role for non-logged-in users
}


// Fetch the user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT firstName, lastName, profile_picture, bio, username FROM users WHERE Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $bio = $_POST['bio'];

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
    $updateQuery = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, profile_picture = ?, bio = ?, username = ? WHERE Id = ?");
    $updateQuery->bind_param("sssssi", $firstName, $lastName, $profilePicture, $bio, $username, $user_id);

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

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
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
                    <div class="card-header">
                        <h4>Edit Profile</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail mt-3" width="150">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                            </div>
                            <button type="button" class="btn btn-danger" onclick="window.location.href='profile.php';">Back to Profile</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
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
                    Your profile has been updated successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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

/* การ์ดหลัก */
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

.card-header h4 {
    margin: 0;
    font-weight: 600;
    font-size: 1.5rem;
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
    display: block;
    margin: 1rem auto;
}

.img-thumbnail:hover {
    transform: scale(1.05);
}

/* ฟอร์มอินพุต */
.form-label {
    font-weight: 500;
    color: #2b2d42;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 0.8rem 1.2rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #C85C5C;
    box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.25);
}

/* ปรับแต่ง input file */
input[type="file"].form-control {
    padding: 0.5rem;
    background: #f8f9fa;
}

input[type="file"].form-control::file-selector-button {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    background: #C85C5C;
    color: white;
    margin-right: 1rem;
    transition: all 0.3s ease;
}

input[type="file"].form-control::file-selector-button:hover {
    background: #b94545;
}

/* ส่วนปุ่ม */
.btn-container {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    justify-content: flex-end;
}

.btn {
    padding: 0.8rem 2rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-danger {
    background-color: #ff4757;
    border: none;
}

.btn-danger:hover {
    background-color: #ff6b81;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 71, 87, 0.2);
}

.btn-primary {
    background: #80FF00;
    color: black;
    border: none;
}

.btn-primary:hover {
    background: #73e600;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(128, 255, 0, 0.3);
}

/* Modal Success */
.modal-content {
    border-radius: 20px;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
    color: white;
    border: none;
}

.modal-footer {
    border: none;
    padding: 1rem 2rem;
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


/* Animation สำหรับข้อความแจ้งเตือน */
@keyframes slideIn {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert {
    animation: slideIn 0.3s ease;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card {
        margin: 1rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .btn-container {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
