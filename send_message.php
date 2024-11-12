<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $name = $_POST['name'];
   $email = $_POST['email'];
   $message = $_POST['message'];

   // Prepare the SQL statement to prevent SQL injection
   $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
   $stmt->bind_param("sss", $name, $email, $message);

   // Execute the statement
   if ($stmt->execute()) {
       ?>
       <!DOCTYPE html>
       <html lang="en">
       <head>
           <meta charset="UTF-8">
           <meta name="viewport" content="width=device-width, initial-scale=1.0">
           <title>Sending Message...</title>
           <style>
               body {
                   margin: 0;
                   height: 100vh;
                   display: flex;
                   flex-direction: column;
                   justify-content: center;
                   align-items: center;
                   background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
                   font-family: 'Arial', sans-serif;
               }

               .loading-container {
                   text-align: center;
                   background: white;
                   padding: 2rem;
                   border-radius: 20px;
                   box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                   max-width: 90%;
                   width: 400px;
               }

               .spinner {
                   width: 70px;
                   height: 70px;
                   margin: 20px auto;
                   border: 8px solid #f3f3f3;
                   border-top: 8px solid #C85C5C;
                   border-radius: 50%;
                   animation: spin 1s linear infinite;
               }

               .success-icon {
                   display: none;
                   color: #2ecc71;
                   font-size: 60px;
                   margin: 20px 0;
               }

               .message {
                   color: #2b2d42;
                   font-size: 1.2rem;
                   margin: 15px 0;
                   opacity: 0;
                   transform: translateY(20px);
                   animation: fadeInUp 0.5s forwards 0.5s;
               }

               .redirect-text {
                   color: #666;
                   font-size: 0.9rem;
                   margin-top: 10px;
               }

               @keyframes spin {
                   0% { transform: rotate(0deg); }
                   100% { transform: rotate(360deg); }
               }

               @keyframes fadeInUp {
                   to {
                       opacity: 1;
                       transform: translateY(0);
                   }
               }

               .checkmark {
                   width: 70px;
                   height: 70px;
                   border-radius: 50%;
                   display: block;
                   stroke-width: 4;
                   stroke: #2ecc71;
                   stroke-miterlimit: 10;
                   margin: 10% auto;
                   box-shadow: inset 0px 0px 0px #2ecc71;
                   animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
                   display: none;
               }

               .checkmark__circle {
                   stroke-dasharray: 166;
                   stroke-dashoffset: 166;
                   stroke-width: 4;
                   stroke-miterlimit: 10;
                   stroke: #2ecc71;
                   fill: none;
                   animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
               }

               .checkmark__check {
                   transform-origin: 50% 50%;
                   stroke-dasharray: 48;
                   stroke-dashoffset: 48;
                   animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
               }

               @keyframes stroke {
                   100% {
                       stroke-dashoffset: 0;
                   }
               }

               @keyframes scale {
                   0%, 100% {
                       transform: none;
                   }
                   50% {
                       transform: scale3d(1.1, 1.1, 1);
                   }
               }

               @keyframes fill {
                   100% {
                       box-shadow: inset 0px 0px 0px 30px #2ecc71;
                   }
               }

               /* เพิ่มสไตล์สำหรับปุ่ม Back */
               .back-button {
                   position: absolute;
                   top: 20px;
                   left: 20px;
                   padding: 10px 20px;
                   background: white;
                   color: #2b2d42;
                   border: none;
                   border-radius: 25px;
                   cursor: pointer;
                   font-weight: 500;
                   box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                   transition: all 0.3s ease;
               }

               .back-button:hover {
                   transform: translateY(-2px);
                   box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
               }
           </style>
       </head>
       <body>
           <!-- เพิ่มปุ่ม Back -->
           <button onclick="goBack()" class="back-button">← Back</button>

           <div class="loading-container">
               <div class="spinner"></div>
               <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                   <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                   <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
               </svg>
               <div class="message">กำลังส่งข้อความของคุณ...</div>
               <div class="redirect-text">คุณจะถูกนำกลับไปยังหน้าติดต่อในอีกไม่กี่วินาที...</div>
           </div>

           <script>
               function goBack() {
                   window.location.href = 'contact.php';
               }

               window.onload = function() {
                   setTimeout(function() {
                       document.querySelector('.spinner').style.display = 'none';
                       document.querySelector('.checkmark').style.display = 'block';
                       document.querySelector('.message').textContent = 'ส่งข้อความสำเร็จ!';
                   }, 2000);

                   setTimeout(function() {
                       window.location.href = 'contact.php';
                   }, 3000);
               };
           </script>
       </body>
       </html>
       <?php
       exit();
   } else {
       ?>
       <!DOCTYPE html>
       <html lang="en">
       <head>
           <meta charset="UTF-8">
           <meta name="viewport" content="width=device-width, initial-scale=1.0">
           <title>Error</title>
           <style>
               body {
                   margin: 0;
                   height: 100vh;
                   display: flex;
                   flex-direction: column;
                   justify-content: center;
                   align-items: center;
                   background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
                   font-family: 'Arial', sans-serif;
               }

               .error-container {
                   text-align: center;
                   background: white;
                   padding: 2rem;
                   border-radius: 20px;
                   box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                   max-width: 90%;
                   width: 400px;
               }

               .error-icon {
                   color: #e74c3c;
                   font-size: 50px;
                   margin-bottom: 20px;
               }

               .error-message {
                   color: #2b2d42;
                   font-size: 1.2rem;
                   margin: 15px 0;
               }

               .back-button {
                   margin-top: 20px;
                   padding: 10px 30px;
                   background: #C85C5C;
                   color: white;
                   border: none;
                   border-radius: 25px;
                   cursor: pointer;
                   font-weight: 500;
                   transition: all 0.3s ease;
               }

               .back-button:hover {
                   background: #b94545;
                   transform: translateY(-2px);
                   box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
               }
           </style>
       </head>
       <body>
           <div class="error-container">
               <div class="error-icon">❌</div>
               <div class="error-message">เกิดข้อผิดพลาด: <?php echo $stmt->error; ?></div>
               <button onclick="goBack()" class="back-button">กลับไปที่หน้าติดต่อ</button>
           </div>

           <script>
               function goBack() {
                   window.location.href = 'contact.php';
               }
           </script>
       </body>
       </html>
       <?php
   }

   $stmt->close();
   $conn->close();
}
?>