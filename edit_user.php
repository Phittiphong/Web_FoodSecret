<?php
session_start();
include 'connect.php'; // เพิ่มการเชื่อมต่อฐานข้อมูล

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบ ID ที่ส่งมา
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    die("Invalid user ID");
}

// ดึงข้อมูลผู้ใช้ก่อนที่จะมีการ POST
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    die("User not found");
}

$user = $result->fetch_assoc();

// จัดการกับการ POST ข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // เก็บค่าเดิมไว้เปรียบเทียบ
    $old_values = [
        'username' => $user['username'],
        'firstName' => $user['firstName'],
        'lastName' => $user['lastName'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    // รับค่าใหม่จากฟอร์ม
    $username = $conn->real_escape_string($_POST['username']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);

    // เก็บการเปลี่ยนแปลง
    $changes = [];
    if ($username !== $old_values['username']) $changes[] = "Username ($old_values[username] → $username)";
    if ($firstName !== $old_values['firstName']) $changes[] = "First Name ($old_values[firstName] → $firstName)";
    if ($lastName !== $old_values['lastName']) $changes[] = "Last Name ($old_values[lastName] → $lastName)";
    if ($email !== $old_values['email']) $changes[] = "Email ($old_values[email] → $email)";
    if ($role !== $old_values['role']) $changes[] = "Role ($old_values[role] → $role)";

    // อัพเดทข้อมูล
    $update_stmt = $conn->prepare("UPDATE users SET username=?, firstName=?, lastName=?, email=?, role=? WHERE id=?");
    $update_stmt->bind_param("sssssi", $username, $firstName, $lastName, $email, $role, $user_id);

    if ($update_stmt->execute()) {
        if (!empty($changes)) {
            $_SESSION['update_message'] = [
                'type' => 'success',
                'changes' => $changes,
                'message' => 'User updated successfully'
            ];
        }
        header('Location: admin_dashboard.php?update=success');
        exit();
    } else {
        $_SESSION['update_message'] = [
            'type' => 'error',
            'message' => 'Error updating user information'
        ];
        header('Location: admin_dashboard.php?update=error');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">

        <!-- เพิ่ม Toast notification -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="updateToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <p class="mb-0">Updated successfully!</p>
                    <ul id="changesList" class="mb-0 ps-3">
                        <!-- Changes will be inserted here -->
                    </ul>
                </div>
            </div>
        </div>
        <h1>Edit User Profile</h1>

        <div class="form-card">
            <form method="post">
                <!-- Username Field - ใช้ @ -->
                <div class="field-group">
                    <label for="username" class="form-label">Username</label>
                    <i class="fas fa-at field-icon"></i> <!-- เปลี่ยนเป็น fa-at แทน fa-user -->
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <!-- First Name Field - ใช้รูปคน -->
                <div class="field-group">
                    <label for="firstName" class="form-label">First Name</label>
                    <i class="fas fa-id-card field-icon"></i> <!-- เปลี่ยนเป็น fa-id-card แทน fa-user -->
                    <input type="text" class="form-control" id="firstName" name="firstName"
                        value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
                </div>

                <!-- Last Name Field - ใช้รูปคน -->
                <div class="field-group">
                    <label for="lastName" class="form-label">Last Name</label>
                    <i class="fas fa-id-card field-icon"></i> <!-- เปลี่ยนเป็น fa-id-card แทน fa-user -->
                    <input type="text" class="form-control" id="lastName" name="lastName"
                        value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
                </div>

                <!-- Email Field - ใช้รูป envelope -->
                <div class="field-group">
                    <label for="email" class="form-label">Email</label>
                    <i class="fas fa-envelope field-icon"></i> <!-- คงเดิม เพราะถูกต้องแล้ว -->
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <!-- Role Field - ใช้รูป shield -->
                <div class="field-group">
                    <label for="role" class="form-label">Role</label>
                    <i class="fas fa-shield-alt field-icon"></i> <!-- เปลี่ยนเป็น fa-shield-alt แทน fa-user-shield -->
                    <select id="role" name="role" class="form-select" required>
                        <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="button-container">
                    <a href="admin_dashboard.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-update">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- เพิ่ม script สำหรับแสดง Toast -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['update_message'])): ?>
                const toast = new bootstrap.Toast(document.getElementById('updateToast'));
                const changesList = document.getElementById('changesList');

                // Add changes to the list
                <?php foreach ($_SESSION['update_message']['changes'] as $change): ?>
                    const li = document.createElement('li');
                    li.textContent = <?php echo json_encode($change); ?>;
                    changesList.appendChild(li);
                <?php endforeach; ?>

                toast.show();
                <?php unset($_SESSION['update_message']); ?>
            <?php endif; ?>
        });
    </script>

    <style>
        /* Toast Styling */
        .toast {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 300px;
        }

        .toast-header {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
        }

        .toast-body {
            padding: 1rem;
        }

        .toast-body ul {
            margin-top: 0.5rem;
        }

        .toast-body li {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        /* Animation */
        .toast.showing {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }
    </style>

    <style>
        /* หน้าหลัก */
        body {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        /* คอนเทนเนอร์หลัก */
        .container {
            max-width: 800px;
        }

        /* หัวข้อหลัก */
        h1 {
            color: #2b2d42;
            font-weight: 700;
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
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

        /* การ์ดฟอร์ม */
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
            transition: transform 0.3s ease;
        }

        .form-card:hover {
            transform: translateY(-5px);
        }

        /* ป้ายกำกับฟอร์ม */
        .form-label {
            font-weight: 600;
            color: #2b2d42;
            margin-bottom: 0.8rem;
        }

        /* อินพุตฟิลด์ */
        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #C85C5C;
            box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.15);
            background-color: white;
        }

        /* เลือก Role */
        .form-select {
            cursor: pointer;
        }

        .form-select option {
            padding: 10px;
        }

        /* คอนเทนเนอร์ปุ่ม */
        .button-container {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        /* ปุ่ม Update */
        .btn-update {
            background: #C85C5C;
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-update:hover {
            background: #b94545;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
        }

        /* ปุ่ม Back */
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        /* Animation สำหรับข้อความแจ้งเตือน */
        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-card {
                padding: 1.5rem;
            }

            .button-container {
                flex-direction: column;
            }

            .btn-update,
            .btn-back {
                width: 100%;
            }
        }

        /* Field Groups */
        .field-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .field-group .form-control,
        .field-group .form-select {
            padding-left: 2.5rem;
        }

        .field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #C85C5C;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>