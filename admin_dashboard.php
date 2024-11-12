<?php
session_start();

// Database connection
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

// Check if the user is logged in and an admin
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect if user is not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Assuming the user is logged in

// Fetch user role from the database
$stmt = $conn->prepare("SELECT role FROM users WHERE Id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Set session variable based on role
$_SESSION['is_admin'] = ($role === 'admin');

// Redirect if not an admin
if (!$_SESSION['is_admin']) {
    header('Location: index.php'); // Redirect if not an admin
    exit();
}

// Fetch users and posts for display
$users_result = $conn->query("SELECT * FROM users");
$posts_result = $conn->query("SELECT * FROM posts");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // เก็บค่าเดิมไว้เปรียบเทียบ
    $old_values = [
        'username' => $user['username'],
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    $username = $conn->real_escape_string($_POST['username']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);

    // เก็บค่าที่เปลี่ยนแปลง
    $changes = [];
    if ($username !== $old_values['username']) $changes[] = "Username";
    if ($firstName !== $old_values['firstName']) $changes[] = "First Name";
    if ($lastName !== $old_values['lastName']) $changes[] = "Last Name";
    if ($email !== $old_values['email']) $changes[] = "Email";
    if ($role !== $old_values['role']) $changes[] = "Role";

    $conn->query("UPDATE users SET username='$username', firstName='$firstName', lastName='$lastName', email='$email', role='$role' WHERE id = $user_id");
    
    // เก็บข้อความแจ้งเตือนใน session
    $_SESSION['update_message'] = [
        'type' => 'success',
        'changes' => $changes,
        'time' => date('H:i:s')
    ];
    
    header('Location: edit_user.php?id=' . $user_id);
    exit();
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

<?php if (isset($_GET['delete_success']) && $_GET['delete_success'] === 'true'): ?>
    <script>
        alert("User deleted successfully.");
    </script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="button.css" rel="stylesheet">
</head>

<body>
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
    </header>

    <div class="container">
        <!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="updateToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['update_message'])): ?>
        const toast = new bootstrap.Toast(document.getElementById('updateToast'));
        const toastMessage = document.getElementById('toastMessage');
        
        let message = '<?php echo $_SESSION['update_message']['message']; ?><br>';
        <?php if (!empty($_SESSION['update_message']['changes'])): ?>
            message += '<ul class="mb-0 mt-2">';
            <?php foreach ($_SESSION['update_message']['changes'] as $change): ?>
                message += '<li><?php echo $change; ?></li>';
            <?php endforeach; ?>
            message += '</ul>';
        <?php endif; ?>
        
        toastMessage.innerHTML = message;
        toast.show();
        
        <?php unset($_SESSION['update_message']); ?>
    <?php endif; ?>
});
</script>

<style>
.toast {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.toast.showing {
    opacity: 1;
}

.toast-container {
    z-index: 9999;
}

.toast {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
    border-radius: 10px !important;
    min-width: 350px;
}

.toast-body {
    color: white;
    padding: 1rem;
}

.toast-body ul {
    list-style: none;
    padding-left: 1rem;
}

.toast-body li {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.toast-body li::before {
    content: '•';
    color: white;
    font-weight: bold;
    display: inline-block;
    width: 1em;
    margin-left: -1em;
}

/* Animation */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideInRight 0.3s ease forwards;
}
</style>
        <h1 class="my-4">Admin Dashboard</h1>
        <!-- Check if the delete operation was successful -->
        <?php if (isset($_GET['delete_success']) && $_GET['delete_success'] === 'true'): ?>
            <script>
                alert("Delete Post Successfully");
            </script>
        <?php endif; ?>
        <!-- Manage Users Section -->
        <h2 class="my-4">Manage Users</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['Id']); ?></td>
                        <td><?php echo htmlspecialchars($user['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($user['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="action-buttons">
    <a href="edit_user.php?id=<?php echo htmlspecialchars($user['Id']); ?>" 
       class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> Edit
    </a>
    <button type="button" 
        class="btn btn-danger btn-sm" 
        onclick="confirmDeleteUser(<?php echo htmlspecialchars($user['Id']); ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
    <i class="fas fa-trash"></i> Delete
</button>
</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Manage Post Section -->
        <h2 class="my-4">Manage Post</h2>
        <table class="table table-striped">
            <thead>
                <!-- Dropdown to select categories with a placeholder -->
                <select name="category" id="category" class="category-select">
                    <option value="" disabled selected>View Category</option>
                    <option value="1">ทอด = 1</option>
                    <option value="2">ผัด = 2</option>
                    <option value="3">ต้ม = 3</option>
                    <option value="4">นึ่ง = 4</option>
                    <option value="5">ตุ๋น = 5</option>
                    <option value="6">ย่าง = 6</option>
                    <option value="7">อบ = 7</option>
                    <option value="8">ยำ = 8</option>
                    <option value="9">แกง = 9</option>
                </select>

                <tr>
                    <th>ID</th>
                    <th>Post</th>
                    <th>Post Image</th>
                    <th>Post Time</th>
                    <th>Category</th>
                </tr>

            </thead>
            <tbody>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['id']); ?></td>
                        <td><?php echo htmlspecialchars($post['post_content']); ?></td>
                        <td><?php echo htmlspecialchars($post['post_image']); ?></td>
                        <td><?php echo htmlspecialchars($post['post_time']); ?></td>
                        <td><?php echo htmlspecialchars($post['category_id']); ?></td>
                        </td>
                        <td>
                            <!-- Add edit, delete, or other action buttons here if necessary -->
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

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
            /* Global Styles */
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

            /* Category Select Styles */
            .category-select {
                width: 200px;
                padding: 10px 15px;
                font-size: 14px;
                border: 2px solid var(--border-color);
                border-radius: 8px;
                background-color: white;
                margin: 20px auto;
                transition: all 0.3s ease;
                cursor: pointer;
                appearance: none;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 10px center;
                background-size: 15px;
                padding-right: 30px;
            }

            .category-select:hover {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
            }

            .category-select:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .container {
                    padding: 10px;
                }

                .table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }

                .btn {
                    padding: 6px 12px;
                    font-size: 14px;
                }

                h1.my-4 {
                    font-size: 1.8rem;
                }

                h2.my-4 {
                    font-size: 1.3rem;
                }
            }

            /* Alert Styles */
            .alert {
                border-radius: 8px;
                padding: 15px 20px;
                margin-bottom: 20px;
                border: none;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
    background: #C85C5C;
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
    color: #fff !important; /* สีตัวอักษรเมื่อนำเมาส์ชี้ */
}

/* เส้นใต้เมนูที่จะปรากฏเมื่อนำเมาส์ชี้ */
header .nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -2px;
    left: 50%;
    background-color: #fff;
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

    /* Modal Styles */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    border-bottom: none;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    border-top: none;
    padding: 1rem 2rem 2rem;
}

/* Button Styles */
.btn {
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.btn-danger {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    border: none;
}

.btn-secondary {
    background: #6c757d;
    border: none;
}

/* Toast Styles */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    z-index: 1050;
}

.toast.bg-success {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
}

.toast.bg-danger {
    background: linear-gradient(45deg, #dc3545, #ff4b2b) !important;
}

.toast-body {
    color: white;
    padding: 1rem;
    font-weight: 500;
}

/* Animation */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideIn 0.3s ease forwards;
}
}
        </style>

<script>
function confirmDeleteUser(userId, username) {
    // สร้าง Modal สำหรับยืนยันการลบ
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                        </div>
                        <p class="text-center fs-5">Are you sure you want to delete this user?</p>
                        <p class="text-center text-muted">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <form action="delete_user.php" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="${userId}">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
    
    // ลบ Modal เมื่อปิด
    document.getElementById('deleteConfirmModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

// Toast notification handler
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['delete_message'])): ?>
    const toastLiveExample = document.getElementById('deleteToast');
    const toast = new bootstrap.Toast(toastLiveExample);
    
    // Set toast content
    document.querySelector('#deleteToast .toast-body').innerHTML = 
        '<?php echo $_SESSION["delete_message"]["text"]; ?>';
    document.getElementById('deleteToast').classList.add(
        '<?php echo $_SESSION["delete_message"]["type"] === "success" ? "bg-success" : "bg-danger"; ?>'
    );
    
    toast.show();
    <?php unset($_SESSION['delete_message']); ?>
    <?php endif; ?>
});
</script>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>