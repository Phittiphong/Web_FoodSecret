<?php
include 'connect.php';  // เชื่อมต่อฐานข้อมูล

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// โค้ด PHP ที่คุณให้มาทั้งหมด ใส่ตรงนี้

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            color: #2b2d42;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #C85C5C;
            font-size: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #C85C5C;
            box-shadow: 0 0 0 3px rgba(200, 92, 92, 0.1);
            outline: none;
        }

        .btn {
            background: linear-gradient(135deg, #C85C5C 0%, #b94545 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(200, 92, 92, 0.3);
        }

        .success-message, .error-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: fadeIn 0.5s ease-out;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #C85C5C;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #C85C5C;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-to-login a:hover {
            color: #b94545;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
        <h1 class="form-title">Forgot Password</h1>
        <form method="post" action="" id="resetForm">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn" name="reset_request">
                <span>Send Reset Link</span>
            </button>
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>Sending reset link...</p>
            </div>
        </form>
        <div class="back-to-login">
            <a href="index.php">Back to Login</a>
        </div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function() {
            document.querySelector('.btn').style.display = 'none';
            document.querySelector('.loading').style.display = 'block';
        });
    </script>
</body>
</html>