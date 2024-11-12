<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's current settings, including role
$user_id = $_SESSION['user_id'];
$sql = "SELECT email, password, theme, role FROM users WHERE Id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_theme = $user['theme'] ?? 'light'; // Default to 'light' if not set
$role = $user['role'];  // Fetch the role from the database

// Initialize a variable for success message
$update_success = false;

// Handle theme change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    $new_theme = $_POST['theme'];
    $update_sql = "UPDATE users SET theme = ? WHERE Id = ?";
    $update_stmt = $conn->prepare($update_sql);

    if (!$update_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $update_stmt->bind_param("si", $new_theme, $user_id);

    if ($update_stmt->execute()) {
        $current_theme = $new_theme; // Update the theme variable to reflect the change
        $update_success = true;
    } else {
        echo "Error: " . $update_stmt->error;
    }
}

// Handle email and password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['currentPassword'])) {
    $new_email = $_POST['email'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    // Check if the current password is correct
    if ($currentPassword !== $user['password']) {
        echo "Current password is incorrect.";
    } elseif ($newPassword !== $confirmNewPassword) {
        echo "New passwords do not match.";
    } else {
        // Update email
        $update_email_sql = "UPDATE users SET email = ? WHERE Id = ?";
        $update_email_stmt = $conn->prepare($update_email_sql);

        if (!$update_email_stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $update_email_stmt->bind_param("si", $new_email, $user_id);

        if (!$update_email_stmt->execute()) {
            echo "Error updating email: " . $update_email_stmt->error;
        }

        // Update password if a new password is provided
        if ($newPassword) {
            $update_password_sql = "UPDATE users SET password = ? WHERE Id = ?";
            $update_password_stmt = $conn->prepare($update_password_sql);

            if (!$update_password_stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $update_password_stmt->bind_param("si", $newPassword, $user_id);

            if (!$update_password_stmt->execute()) {
                echo "Error updating password: " . $update_password_stmt->error;
            }
        }

        // Set the success flag to true
        $update_success = true;
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
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link id="theme-style" href="light-theme.css" rel="stylesheet">
</head>

<body class="<?php echo htmlspecialchars($current_theme); ?>">
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
                        <li><a class="dropdown-item" href="admin_dashboard.php">Admin Dashboard</a></li>
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header> <br>

    <div class="container mt-4">
        <h2>Account Settings</h2>

        <!-- Email and Password Change Form -->
        <form method="POST" action="settings.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="currentPassword" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
            </div>
            <div class="mb-3">
                <label for="newPassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword">
            </div>
            <div class="mb-3">
                <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword">
            </div>
            <button type="submit" class="btn btn-primary" style="background-color: yellow; color: black;">Save Changes</button>
        </form>
    </div> <br><br><br>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your changes have been saved successfully.
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        // Show the success modal if the update was successful
        <?php if ($update_success): ?>
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        <?php endif; ?>
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

        /* ส่วน Header เหมือนหน้าอื่นๆ */


/* ส่วนเนื้อหาหลัก */
.container {
    max-width: 1200px;
    margin: 0 auto;
}

            .nav-link {
                font-weight: 500;
                padding: 0.5rem 1rem;
                transition: color 0.3s ease;
            }

            .nav-link:hover {
                color: var(--rounded-color) !important;
            }

            /* Dashboard Title */
            h1.my-4 {
                color: var(--primary-color);
                font-weight: bold;
                border-bottom: 3px solid var(--primary-color);
                padding-bottom: 10px;
                margin-bottom: 30px;
            }

            h2.my-4 {
                color: var(--text-color);
                font-size: 1.5rem;
                margin: 30px 0 20px;
                padding-left: 10px;
                border-left: 4px solid var(--primary-color);
            }

            /* Table Styles */
            .table {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .table thead {
                background-color: var(--primary-color);
                color: white;
            }

            .table th {
                font-weight: 600;
                padding: 15px;
                border-bottom: none;
            }

            .table td {
                padding: 12px 15px;
                vertical-align: middle;
            }

            .table tbody tr:hover {
                background-color: #f8f9fa;
                transition: background-color 0.3s ease;
            }

            /* Button Styles */
            .btn {
                padding: 8px 15px;
                border-radius: 5px;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-warning {
                background-color: var(--warning-color);
                border: none;
                color: white;
            }

            .btn-danger {
                background-color: var(--danger-color);
                border: none;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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

        /* คอนเทนเนอร์หลัก */
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* หัวข้อหลัก */
        h2 {
            color: #2b2d42;
            font-weight: 700;
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, #FBD148, #C85C5C);
            border-radius: 2px;
        }

        /* การ์ดฟอร์ม */
        .settings-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .settings-card:hover {
            transform: translateY(-5px);
        }

        /* ฟอร์มอินพุต */
        .form-label {
            font-weight: 500;
            color: #2b2d42;
            margin-bottom: 0.8rem;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #C85C5C;
            box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.15);
            background-color: white;
        }

        /* ปุ่มบันทึก */
        .btn-save {
            background: #80FF00;
            color: #2b2d42;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-save:hover {
            background: #73e600;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(128, 255, 0, 0.3);
        }

        /* ไอคอนในปุ่ม */
        .btn-save i {
            font-size: 1.2rem;
        }

        /* Modal Success */
        .modal-content {
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
            color: white;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
            text-align: center;
        }


        /* Password Input Group */
        .password-input-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
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
    </style>


</body>

</html>