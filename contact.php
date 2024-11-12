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
    <title>Contact Us</title>
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
        <h2 class="mb-4">Contact Us</h2>
        <div class="row">
            <div class="col-md-6">
                <form action="send_message.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <div class="col-md-6">
                <h5>Our Location</h5>
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12969.991783836611!2d102.01305039494532!3d14.881187413047568!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311eada1f2cc53f1%3A0x10533e4b3f07a09f!2z4Lih4Lir4Liy4Lin4Li04LiX4Lii4Liy4Lil4Lix4Lii4LmA4LiX4LiE4LmC4LiZ4LmC4Lil4Lii4Li14Liq4Li44Lij4LiZ4Liy4Lij4Li1!5e0!3m2!1sth!2sth!4v1724852093646!5m2!1sth!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div> <br><br><br><br>

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


/* ส่วน Contact Form */
.contact-section {
    padding: 3rem 0;
    background-color: #f8f9fa;
    border-radius: 15px;
    margin: 2rem 0;
}

.contact-section h2 {
    color: #2b2d42;
    margin-bottom: 2rem;
    font-weight: 600;
    text-align: center;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.contact-form .form-label {
    font-weight: 500;
    color: #2b2d42;
}

.contact-form .form-control {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 0.8rem;
    transition: all 0.3s ease;
}

.contact-form .form-control:focus {
    border-color: #C85C5C;
    box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.25);
}

.contact-form .btn-primary {
    background: #C85C5C;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    width: 100%;
}

.contact-form .btn-primary:hover {
    background: #b94545;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(200, 92, 92, 0.3);
}

/* ส่วนแผนที่ */
.map-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.map-section h5 {
    color: #2b2d42;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.map-section iframe {
    border-radius: 10px;
    width: 100%;
    height: 400px;
}

/* ส่วนของฟอร์ม */
.form-label {
    font-weight: 600;
    color: #2b2d42;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 12px 16px;
    transition: all 0.3s ease;
    font-size: 1rem;
    color: #2b2d42;
    background-color: #fff;
}

.form-control:focus {
    border-color: #FBD148;
    box-shadow: 0 0 0 0.2rem rgba(251, 209, 72, 0.25);
    outline: none;
}

.form-control::placeholder {
    color: #a0a0a0;
    font-size: 0.9rem;
}

/* สไตล์เฉพาะสำหรับ input text และ email */
input.form-control {
    height: 48px;
}

/* สไตล์เฉพาะสำหรับ textarea */
textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

/* ปรับแต่งปุ่ม Submit */
.btn-primary {
    background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #b94545 0%, #a63d3d 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(200, 92, 92, 0.4);
}

/* เพิ่ม animation เมื่อ focus */
.form-control:focus {
    transform: translateY(-2px);
}

/* ใส่ไอคอนสำหรับ field ต่างๆ */
.form-group {
    position: relative;
    margin-bottom: 1.5rem;
}

/* ทำให้ฟอร์มดูโดดเด่น */
.col-md-6:first-child {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

/* เพิ่ม hover effect ให้กับ form fields */
.form-control:hover {
    border-color: #FBD148;
}

/* ปรับแต่งสีพื้นหลังเมื่อ autofill */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px white inset;
    -webkit-text-fill-color: #2b2d42;
    transition: background-color 5000s ease-in-out 0s;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-control {
        font-size: 16px; /* ป้องกันการ zoom บน iOS */
    }
    
    .col-md-6:first-child {
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .btn-primary {
        width: 100%;
    }
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

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</html>