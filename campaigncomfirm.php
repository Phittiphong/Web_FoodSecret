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
    <title>Campaign Activity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            text-align: center;
        }

        .main-image {
            width: 100%;
            max-width: 500px;
            display: block;
            margin: 0 auto;
            border-radius: 15px;
        }

        .center-title {
            font-weight: bold;
            margin: 20px 0;
        }

        .green-button {
            background-color: #8BC34A;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            font-size: 18px;
            display: inline-block;
            margin: 20px auto;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            text-align: left;
        }

        .section-content {
            text-align: left;
            margin-bottom: 20px;
        }

        .back-button {
            text-align: left;
            display: block;
            margin-bottom: 20px;
        }

        /* Button Styling */
        .campaign-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 16px;
            margin-top: 10px;
            text-decoration: none;
        }

        .campaign-button:hover {
            background-color: #45a049;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        /* Style the popup content */
        .popup-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
            position: relative;
            z-index: 2;
        }

        /* Close button inside the popup */
        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        /* Green button to open the popup */
        .green-button {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
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
        <!-- Back button -->
        <div class="d-flex justify-content-start">
            <a href="campaign.php" class="campaign-button" style="background-color: #C85C5C;">Back</a>
        </div>

        <!-- Title -->
        <h1 class="center-title">Activities to join as an official account</h1>

        <!-- Image -->
        <img src="photo/campaign.png" alt="Campaign Image" class="main-image">

        <h1>Join the Campaign</h1>

        <!-- Green Button to open the popup -->
        <button class="green-button" onclick="openPopup()">Join the Campaign</button>

        <!-- Button to open the member list popup -->
        <button class="green-button" onclick="openMemberListPopup()">View Participants</button>

        <!-- Popup Overlay for Member List -->
        <div id="memberListPopup" class="popup-overlay">
            <div class="popup-content">
                <button class="close-button" onclick="closeMemberListPopup()">X</button>
                <h2>Participants lists</h2>
                <div id="member-list">
                    <!-- Member list will be populated here from the database -->
                </div>
            </div>
        </div>

        <!-- Popup Overlay -->
        <div id="popup" class="popup-overlay">
            <div class="popup-content">
                <button class="close-button" onclick="closePopup()">X</button>
                <h2>Campaign Form</h2>

                <!-- Form inside the popup -->
                <form action="campaigncomfirms.php" method="POST">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required><br><br>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required><br><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>

                    <!-- Submit Button -->
                    <button type="submit" class="green-button">Submit</button>
                </form>

            </div>
        </div>

        <!-- Description Sections -->
        <div class="section-title">Basic information</div>
        <div class="section-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam a risus elit. Nunc laoreet orci eu dui pulvinar, ut malesuada eros venenatis. Nunc et ullamcorper odio.
        </div>

        <div class="section-title">Rule</div>
        <div class="section-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam a risus elit. Nunc laoreet orci eu dui pulvinar, ut malesuada eros venenatis. Nunc et ullamcorper odio.
        </div>
    </div> <br><br>




    <script>
        // Open the popup
        function openPopup() {
            document.getElementById("popup").style.display = "block";
        }

        // Close the popup
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        // Open the member list popup
    function openMemberListPopup() {
        document.getElementById("memberListPopup").style.display = "block";

        // Fetch the member list from the database
        fetch('fetch_members.php')
            .then(response => response.json())
            .then(data => {
                const memberListDiv = document.getElementById('member-list');
                memberListDiv.innerHTML = ''; // Clear the existing content

                // Check if data exists
                if (data.length > 0) {
                    data.forEach(member => {
                        const memberItem = document.createElement('div');
                        memberItem.textContent = `Name: ${member.firstName} ${member.lastName}, Email: ${member.email}`;
                        memberListDiv.appendChild(memberItem);
                    });
                } else {
                    memberListDiv.textContent = 'No members found.';
                }
            });
    }

    // Close the member list popup
    function closeMemberListPopup() {
        document.getElementById("memberListPopup").style.display = "none";
    }
    </script>

</body>

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

/* ส่วนเนื้อหาหลัก */
.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* หัวข้อหลัก */
.center-title {
    color: #2b2d42;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 2rem 0;
    position: relative;
    padding-bottom: 1rem;
    text-align: center;
}

.center-title::after {
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

/* รูปภาพหลัก */
.main-image {
    width: 100%;
    max-width: 700px;
    height: auto;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    margin: 3rem auto;
    display: block;
    transition: transform 0.3s ease;
}

.main-image:hover {
    transform: scale(1.02);
}

/* ปุ่มต่างๆ */
.campaign-button {
    padding: 12px 30px;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    margin: 1rem 0;
}

.green-button {
    background: #C85C5C;
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 30px;
    font-size: 1.1rem;
    font-weight: 500;
    margin: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.green-button:hover {
    background: #b94545;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
}

/* ส่วนเนื้อหา */
.section-title {
    color: #2b2d42;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 2rem 0 1rem 0;
    padding-left: 1rem;
    position: relative;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 24px;
    background: #FBD148;
    border-radius: 2px;
}

.section-content {
    color: #666;
    line-height: 1.8;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Popup Styles */
.popup-overlay {
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.popup-content {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    max-width: 500px;
    width: 90%;
    margin: 10vh auto;
}

.popup-content h2 {
    color: #2b2d42;
    margin-bottom: 2rem;
    text-align: center;
    font-weight: 600;
}

.popup-content form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.popup-content label {
    font-weight: 500;
    color: #2b2d42;
    margin-bottom: 0.5rem;
}

.popup-content input {
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.popup-content input:focus {
    border-color: #C85C5C;
    box-shadow: 0 0 0 2px rgba(200, 92, 92, 0.1);
    outline: none;
}

.close-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #ff4757;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-button:hover {
    background: #ff6b81;
    transform: scale(1.1);
}

/* Member List Popup */
#member-list {
    max-height: 400px;
    overflow-y: auto;
    padding: 1rem;
}

#member-list div {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

#member-list div:hover {
    background-color: #f8f9fa;
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

/* Responsive Design */
@media (max-width: 768px) {
    .center-title {
        font-size: 2rem;
    }

    .main-image {
        max-width: 100%;
    }

    .popup-content {
        width: 95%;
        margin: 5vh auto;
    }

    .green-button {
        width: 100%;
        margin: 0.5rem 0;
    }

}
</style>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>