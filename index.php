<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Registration Form -->
    <div class="container" id="signup" style="display:none;">
        <h1 class="form-title">Register</h1>
        <form method="post" action="register.php">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="fName" placeholder="First Name" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="lName" placeholder="Last Name" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signup" style="background-color: #80FF00;">
        </form>
        <p class="or">
            ----------or----------
        </p> <br>
        <div class="icons">
            <i class="fab fa-google"></i>
            <i class="fab fa-facebook"></i>
        </div>
        <div class="links">
            <p>Already Have an Account?</p>
            <button id="signInButton">Sign In</button>
    </div>
    </div>

    <!-- Sign In Form -->
    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <p class="recovery">
                <a href="reset_password_request.php " style="color: #C85C5C; text-decoration: none;">Forgot Password?</a>
            </p>
            <input type="submit" class="btn" value="Sign In" name="signIn" style="background-color: #80FF00;">
        </form>
        <p class="or">
            ----------or----------
        </p> <br>
        <div class="icons">
            <i class="fab fa-google"></i>
            <i class="fab fa-facebook"></i>
        </div>
        <div class="links">
            <p>Don't Have an Account Yet?</p>
            <button id="signUpButton">Sign Up</button>
        </div>
    </div>
    <script src="script.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('photo/background food.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        /* ปรับแต่ง container */
        .container {
            background: white;
            max-width: 400px;
            width: 90%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .form-title {
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
            /* ลดระยะห่างระหว่าง input groups */
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
            /* ปรับขนาด icon ให้พอดี */
            z-index: 1;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            /* ปรับ padding ให้พอดีกับ icon */
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            margin: 0;
            /* ลบ margin */
            height: 45px;
            /* กำหนดความสูงที่แน่นอน */
            line-height: 1;
            /* ปรับ line height */
        }

        /* ลบ <br> tags ใน HTML */
        .input-group br {
            display: none;
        }

        .input-group input:focus {
            border-color: #9b59b6;
            outline: none;
            box-shadow: 0 0 5px rgba(155, 89, 182, 0.3);
        }

        /* ปรับแต่งปุ่ม */
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px 0;
            /* ปรับ margin */
            height: 45px;
            /* กำหนดความสูงที่แน่นอน */
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }


        /* ปรับสไตล์สำหรับ Google และ Facebook buttons */
        .icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }

        .icons i {
            font-size: 20px;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .fa-google {
            color: #DB4437;
            background: white;
            border: 2px solid rgba(219, 68, 55, 0.2);
        }

        .fa-facebook {
            color: #4267B2;
            background: white;
            border: 2px solid rgba(66, 103, 178, 0.2);
        }

        .icons i:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* ปรับแต่งเส้น or */
        .or {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #666;
            font-size: 14px;
        }

        .or::before,
        .or::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
            margin: 0 15px;
        }

        /* ปรับแต่งส่วน links ใหม่ */
        .links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 0 10px;
        }

        .links p {
            margin: 0;
            color: #666;
        }

        /* สไตล์ใหม่สำหรับปุ่ม Sign In/Sign Up */
        .links button {
            background: transparent;
            border: 2px solid #C85C5C;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #C85C5C;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .links button:hover {
            background: #C85C5C;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(200, 92, 92, 0.2);
        }

        /* เพิ่ม icon ในปุ่ม */
        .links button::after {
            content: '→';
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .links button:hover::after {
            transform: translateX(5px);
        }

        /* ปรับ animation */
        .links button:active {
            transform: scale(0.95);
        }

        /* ปรับแต่งลิงก์ recovery */
        .recovery {
            text-align: right;
            margin: 5px 0 15px 0;
            /* ปรับ margin */
        }

        .recovery a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            .form-title {
                font-size: 24px;
            }

            .input-group input {
                font-size: 14px;
            }
        }
    </style>
</body>

</html>