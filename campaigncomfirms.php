<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdev";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO campaign (firstName, lastName, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $first_name, $last_name, $email);

    if ($stmt->execute()) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ลงทะเบียนแคมเปญ</title>
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

                .success-container {
                    text-align: center;
                    background: white;
                    padding: 3rem;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                    max-width: 90%;
                    width: 450px;
                }

                .loading-animation {
                    position: relative;
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 20px;
                }

                .circle {
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #C85C5C;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                .checkmark {
                    display: none;
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 20px;
                }

                .checkmark-circle {
                    stroke-dasharray: 166;
                    stroke-dashoffset: 166;
                    stroke-width: 3;
                    stroke-miterlimit: 10;
                    stroke: #2ecc71;
                    fill: none;
                    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
                }

                .checkmark-check {
                    transform-origin: 50% 50%;
                    stroke-dasharray: 48;
                    stroke-dashoffset: 48;
                    stroke: #2ecc71;
                    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
                }

                .message {
                    color: #2b2d42;
                    font-size: 1.4rem;
                    margin: 20px 0;
                    font-weight: 600;
                }

                .sub-message {
                    color: #666;
                    font-size: 1rem;
                    margin-bottom: 30px;
                }

                .campaign-details {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 12px;
                    margin: 20px 0;
                    text-align: left;
                }

                .detail-item {
                    margin: 10px 0;
                    color: #2b2d42;
                }

                .detail-label {
                    font-weight: 600;
                    margin-right: 10px;
                }

                .back-button {
                    background: #C85C5C;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 25px;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-block;
                    margin-top: 20px;
                }

                .back-button:hover {
                    background: #b94545;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 15px rgba(200, 92, 92, 0.3);
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
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

                .confetti {
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background-color: #FBD148;
                    animation: confetti-fall 3s linear infinite;
                }

                @keyframes confetti-fall {
                    0% {
                        transform: translateY(-100vh) rotate(0deg);
                    }
                    100% {
                        transform: translateY(100vh) rotate(360deg);
                    }
                }
            </style>
        </head>
        <body>
            <div class="success-container">
                <div class="loading-animation">
                    <div class="circle"></div>
                </div>
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                <div class="message">กำลังลงทะเบียน...</div>
                <div class="sub-message">กรุณารอสักครู่</div>
                <div class="campaign-details" style="display: none;">
                    <div class="detail-item">
                        <span class="detail-label">ชื่อ:</span>
                        <span><?php echo htmlspecialchars($first_name); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">นามสกุล:</span>
                        <span><?php echo htmlspecialchars($last_name); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">อีเมล:</span>
                        <span><?php echo htmlspecialchars($email); ?></span>
                    </div>
                </div>
                <a href="campaign.php" class="back-button" style="display: none;">กลับไปหน้าแคมเปญ</a>
            </div>

            <script>
                // สร้าง confetti
                function createConfetti() {
                    for(let i = 0; i < 50; i++) {
                        const confetti = document.createElement('div');
                        confetti.className = 'confetti';
                        confetti.style.left = Math.random() * 100 + 'vw';
                        confetti.style.animationDelay = Math.random() * 3 + 's';
                        confetti.style.backgroundColor = ['#FBD148', '#C85C5C', '#2ecc71'][Math.floor(Math.random() * 3)];
                        document.body.appendChild(confetti);
                        
                        // ลบ confetti หลังจากอนิเมชันจบ
                        setTimeout(() => {
                            confetti.remove();
                        }, 3000);
                    }
                }

                // แสดงผลการลงทะเบียนสำเร็จ
                setTimeout(() => {
                    document.querySelector('.loading-animation').style.display = 'none';
                    document.querySelector('.checkmark').style.display = 'block';
                    document.querySelector('.message').textContent = 'ลงทะเบียนสำเร็จ!';
                    document.querySelector('.sub-message').textContent = 'ขอบคุณที่เข้าร่วมแคมเปญกับเรา';
                    document.querySelector('.campaign-details').style.display = 'block';
                    document.querySelector('.back-button').style.display = 'inline-block';
                    createConfetti();
                }, 2000);
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
            <title>เกิดข้อผิดพลาด</title>
            <style>
                body {
                    margin: 0;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: linear-gradient(135deg, #FBD148 0%, #ffba08 100%);
                    font-family: 'Arial', sans-serif;
                }

                .error-container {
                    background: white;
                    padding: 2rem;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 90%;
                    width: 400px;
                }

                .error-icon {
                    font-size: 50px;
                    color: #e74c3c;
                    margin-bottom: 20px;
                }

                .error-message {
                    color: #2b2d42;
                    font-size: 1.2rem;
                    margin-bottom: 20px;
                }

                .back-button {
                    background: #C85C5C;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 25px;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
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
                <a href="campaign.php" class="back-button">ลองใหม่อีกครั้ง</a>
            </div>
        </body>
        </html>
        <?php
    }

    $stmt->close();
}
$conn->close();
?>