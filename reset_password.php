<?php
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['token'])) {
   $token = $_GET['token'];
   $checkToken = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires > NOW()");
   $checkToken->bind_param('s', $token);
   $checkToken->execute();
   $result = $checkToken->get_result();

   if ($result->num_rows > 0) {
       if (isset($_POST['reset_password'])) {
           $password = $_POST['password'];
           
           if (strlen($password) < 8) {
               $error_message = "Password must be at least 8 characters long.";
           } else {
               $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
               $resetData = $result->fetch_assoc();
               $email = $resetData['email'];

               $updatePassword = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
               $updatePassword->bind_param('ss', $hashedPassword, $email);

               if ($updatePassword->execute()) {
                   $deleteToken = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                   $deleteToken->bind_param('s', $token);
                   $deleteToken->execute();
                   
                   // Show success message
                   ?>
                   <!DOCTYPE html>
                   <html>
                   <head>
                       <style>
                           .success-popup {
                               position: fixed;
                               top: 50%;
                               left: 50%;
                               transform: translate(-50%, -50%);
                               background: white;
                               padding: 30px;
                               border-radius: 15px;
                               box-shadow: 0 0 30px rgba(0,0,0,0.2);
                               text-align: center;
                               z-index: 1000;
                           }

                           .loading-spinner {
                               width: 50px;
                               height: 50px;
                               border: 5px solid #f3f3f3;
                               border-top: 5px solid #2ecc71;
                               border-radius: 50%;
                               animation: spin 1s linear infinite;
                               margin: 0 auto 20px;
                           }

                           .success-icon {
                               display: none;
                               color: #2ecc71;
                               font-size: 50px;
                               margin-bottom: 20px;
                           }

                           .overlay {
                               position: fixed;
                               top: 0;
                               left: 0;
                               width: 100%;
                               height: 100%;
                               background: rgba(0,0,0,0.5);
                               z-index: 999;
                           }

                           @keyframes spin {
                               0% { transform: rotate(0deg); }
                               100% { transform: rotate(360deg); }
                           }
                       </style>
                   </head>
                   <body>
                       <div class="overlay"></div>
                       <div class="success-popup">
                           <div class="loading-spinner"></div>
                           <div class="success-icon">✓</div>
                           <h3>Password Reset Successful!</h3>
                           <p>Redirecting to login page...</p>
                       </div>

                       <script>
                           setTimeout(() => {
                               document.querySelector('.loading-spinner').style.display = 'none';
                               document.querySelector('.success-icon').style.display = 'block';
                               setTimeout(() => {
                                   window.location.href = 'index.php';
                               }, 1500);
                           }, 2000);
                       </script>
                   </body>
                   </html>
                   <?php
                   exit();
               } else {
                   $error_message = "Error updating password.";
               }
           }
       }
   } else {
       $error_message = "Invalid or expired token.";
   }
} else {
   $error_message = "Token is missing.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reset Password</title>
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

       .error-message {
           background: #ffe5e5;
           color: #ff4757;
           padding: 15px;
           border-radius: 10px;
           margin-bottom: 20px;
           text-align: center;
           font-size: 14px;
           animation: shake 0.5s linear;
       }

       @keyframes shake {
           0%, 100% { transform: translateX(0); }
           25% { transform: translateX(-5px); }
           75% { transform: translateX(5px); }
       }

       /* Password strength indicator */
       .password-strength {
           margin-top: 10px;
           height: 5px;
           background: #e0e0e0;
           border-radius: 3px;
           overflow: hidden;
       }

       .strength-meter {
           height: 100%;
           width: 0;
           transition: width 0.3s ease;
       }

       .weak { background: #ff4757; width: 33%; }
       .medium { background: #ffa502; width: 66%; }
       .strong { background: #2ed573; width: 100%; }
   </style>
</head>
<body>
   <div class="container">
       <h1 class="form-title">Reset Password</h1>
       <?php if (isset($error_message)): ?>
           <div class="error-message"><?php echo $error_message; ?></div>
       <?php endif; ?>
       
       <form method="post" action="">
           <div class="input-group">
               <i class="fas fa-lock"></i>
               <input type="password" name="password" id="password" 
                      placeholder="New Password" required 
                      oninput="checkPasswordStrength(this.value)">
           </div>
           <div class="password-strength">
               <div class="strength-meter"></div>
           </div>
           <input type="submit" class="btn" value="Reset Password" name="reset_password">
       </form>
   </div>

   <script>
       function checkPasswordStrength(password) {
           const meter = document.querySelector('.strength-meter');
           let strength = 0;

           if (password.length >= 8) strength++;
           if (password.match(/[A-Z]/)) strength++;
           if (password.match(/[0-9]/)) strength++;

           meter.className = 'strength-meter';
           if (strength === 1) meter.classList.add('weak');
           else if (strength === 2) meter.classList.add('medium');
           else if (strength === 3) meter.classList.add('strong');
       }
   </script>
</body>
</html>