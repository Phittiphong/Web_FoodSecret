<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .logout-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .logout-popup {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.3s ease-out;
        }

        .logout-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #C85C5C;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .logout-message {
            font-size: 1.2rem;
            color: #2b2d42;
            margin: 15px 0;
        }

        .logout-icon {
            font-size: 40px;
            color: #C85C5C;
            margin-bottom: 15px;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

        .progress-bar {
            width: 200px;
            height: 4px;
            background: #f3f3f3;
            border-radius: 2px;
            margin: 15px auto;
            overflow: hidden;
        }

        .progress {
            width: 0%;
            height: 100%;
            background: #C85C5C;
            animation: progress 2s ease-in-out forwards;
        }

        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="logout-overlay">
        <div class="logout-popup">
            <div class="logout-spinner"></div>
            <div class="logout-icon">👋</div>
            <div class="logout-message">กำลังออกจากระบบ...</div>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.querySelector('.logout-spinner').style.display = 'none';
            document.querySelector('.logout-icon').style.display = 'block';
            document.querySelector('.logout-message').textContent = 'ออกจากระบบสำเร็จ!';
            
            setTimeout(() => {
                <?php
                session_destroy();
                ?>
                window.location.href = 'index.php';
            }, 1000);
        }, 2000);
    </script>
</body>
</html>