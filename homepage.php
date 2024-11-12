<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Food Sharing</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* สไตล์พื้นฐาน */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        /* คอนเทนเนอร์หลัก */
        .loading-container {
            text-align: center;
            color: #2b2d42;
        }

        /* Animation สำหรับโลโก้ */
        .logo {
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15));
            margin-bottom: 20px;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        /* ข้อความต้อนรับ */
        .welcome-text {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 20px 0;
            opacity: 0;
            animation: fadeIn 1s ease forwards;
            color: #C85C5C;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #C85C5C;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Progress Bar */
        .progress-container {
            width: 200px;
            height: 6px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            margin: 20px auto;
            overflow: hidden;
        }

        .progress-bar {
            width: 0%;
            height: 100%;
            background: #C85C5C;
            border-radius: 10px;
            animation: progress 2s ease forwards;
        }

        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* Particles Effect */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: particleFloat 20s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) scale(1);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div class="particles">
        <?php for($i = 0; $i < 20; $i++) { 
            $left = rand(0, 100);
            $delay = rand(0, 20);
            echo "<div class='particle' style='left: {$left}%; animation-delay: {$delay}s;'></div>";
        } ?>
    </div>

    <div class="loading-container">
        <!-- โลโก้ -->
        <img src="photo/Logo.png" alt="Logo" width="200" height="200" class="logo">
        
        <!-- ข้อความต้อนรับ -->
        <div class="welcome-text">
            Welcome, 
            <?php
            if(isset($_SESSION['email'])){
                $email=$_SESSION['email'];
                $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
                while($row=mysqli_fetch_array($query)){
                    echo $row['firstName'].' '.$row['lastName'];       
                }
            }
            ?>!
        </div>
        
        <!-- Loading Spinner -->
        <div class="loading-spinner"></div>
        
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
    </div>

    <script>
        // สร้าง Particles
        function createParticles() {
            const particles = document.querySelector('.particles');
            for(let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particles.appendChild(particle);
            }
        }

        // Redirect หลังจาก animation เสร็จ
        setTimeout(function() {
            window.location.href = 'Home.php';
        }, 2000);

        // เรียกใช้ฟังก์ชัน
        createParticles();
    </script>
</body>
</html>
