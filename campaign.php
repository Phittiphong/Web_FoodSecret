<?php
session_start();

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
    <title>Campaign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .campaign-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .campaign-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .campaign-content {
            padding: 15px;
            position: relative;
        }

        .campaign-button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .campaign-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 16px;
            text-decoration: none;
        }

        .campaign-button:hover {
            background-color: #45a049;
        }

        .subtitle {
            color: #999;
            font-size: 12px;
        }
    </style>
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
    </header> <br>

    <div class="container mt-4"> 
    <a href="javascript:history.back()" class="campaign-button" style="background-color: #C85C5C;">Back</a>
        <h1 class="text-center">Activity and Campaign</h1>

        <!-- First Campaign Item -->
<div class="campaign-card">
    <img src="photo/campaign.png" alt="Campaign Image">
    <div class="campaign-content">
        <?php
        // เชื่อมต่อฐานข้อมูล
        $conn = new mysqli("localhost", "root", "", "webdev");

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // คำสั่ง SQL เพื่อนับจำนวนผู้เข้าร่วมทั้งหมด
        $sql = "SELECT COUNT(*) as total FROM campaign";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $totalUsers = $row['total'];

        // แสดงจำนวนผู้เข้าร่วม
        echo "<p>Participants " . number_format($totalUsers) . " person</p>";

        // ปิดการเชื่อมต่อ
        $conn->close();
        ?>
        <h5>Activities to join as an official account</h5> <br>
        <a href="campaigncomfirm.php" class="campaign-button">
        Get started</a>
    </div>
</div>
    </div>
<br><br><br>
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

        /* ส่วน Header เหมือนหน้าอื่นๆ */


/* ส่วนเนื้อหาหลัก */
.container {
    max-width: 1200px;
    margin: 0 auto;
}

        /* ส่วน Header */
header {
    background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
}

header .nav-link {
    font-weight: 500;
    color: #2b2d42 !important;
    transition: color 0.3s ease;
    position: relative;
}

header .nav-link:hover {
    color: #8d99ae !important;
}

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

/* ปุ่ม Back */
.back-button-container {
    margin-bottom: 2rem;
}

.campaign-button {
    padding: 12px 30px;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.campaign-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
    color: white;
}

/* หัวข้อหลัก */
h1 {
    color: #2b2d42;
    font-weight: 700;
    position: relative;
    padding-bottom: 1.5rem;
    margin-bottom: 3rem;
    text-align: center;
}

h1::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(to right, #FBD148, #C85C5C);
    border-radius: 2px;
}

/* การ์ดแคมเปญ */
.campaign-card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 4rem;
    background: white;
    transition: transform 0.3s ease;
}

.campaign-card:hover {
    transform: translateY(-5px);
}

.campaign-card img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.campaign-card:hover img {
    transform: scale(1.05);
}

.campaign-content {
    padding: 2rem;
    background: white;
}

.campaign-content p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.campaign-content h5 {
    color: #2b2d42;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 2rem;
}

.campaign-button-container {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.campaign-button {
    background: #C85C5C;
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 30px;
    font-size: 1.1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.campaign-button:hover {
    background: #b94545;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
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

/* ส่วน Footer */
footer {
    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
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

/* จัดการ container ใน footer */
footer .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

footer h5 {
    font-weight: 600;
    margin-bottom: 1.5rem;
}

footer .nav-link {
    color: rgba(255, 255, 255, 0.8) !important;
    transition: color 0.3s ease;
    padding: 0.3rem 0;
}

footer .nav-link:hover {
    color: white !important;
}

/* ส่วนสมัครรับข่าวสาร */
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

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    .campaign-card img {
        height: 200px;
    }

    .campaign-content {
        padding: 1.5rem;
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

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>