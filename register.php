<?php
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Handle Registration
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param('s', $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                .error-popup {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.2);
                    text-align: center;
                    animation: fadeIn 0.5s;
                    z-index: 1000;
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

                .error-icon {
                    color: #ff4757;
                    font-size: 40px;
                    margin-bottom: 15px;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translate(-50%, -60%); }
                    to { opacity: 1; transform: translate(-50%, -50%); }
                }
            </style>
        </head>
        <body>
            <div class="overlay"></div>
            <div class="error-popup">
                <div class="error-icon">❌</div>
                <h3>Registration Failed</h3>
                <p>Email address already exists!</p>
                <button onclick="window.history.back()" style="
                    padding: 10px 20px;
                    background: #ff4757;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-top: 10px;
                ">Try Again</button>
            </div>
        </body>
        </html>
        <?php
    } else {
        $insertQuery = $conn->prepare("INSERT INTO users (username, firstName, lastName, email, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
        $insertQuery->bind_param('sssss', $username, $firstName, $lastName, $email, $password);

        if ($insertQuery->execute()) {
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
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0,0,0,0.2);
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
                    <h3>Registration Successful!</h3>
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
        } else {
            // Show error message with nice styling
            ?>
            <!DOCTYPE html>
            <html>
            <head>
            </head>
            <body>
                <div class="overlay"></div>
                <div class="error-popup">
                    <div class="error-icon">❌</div>
                    <h3>Registration Failed</h3>
                    <p>Error: <?php echo $insertQuery->error; ?></p>
                    <button onclick="window.history.back()" style="
                        padding: 10px 20px;
                        background: #ff4757;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        margin-top: 10px;
                    ">Try Again</button>
                </div>
            </body>
            </html>
            <?php
        }
    }
}

// Handle Login
if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT Id, password, role FROM users WHERE email = ?");
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['Id'];
            $_SESSION['role'] = $row['role'];
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
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0,0,0,0.2);
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
                    <h3>Login Successful!</h3>
                    <p>Welcome back!</p>
                </div>

                <script>
                    setTimeout(() => {
                        document.querySelector('.loading-spinner').style.display = 'none';
                        document.querySelector('.success-icon').style.display = 'block';
                        setTimeout(() => {
                            window.location.href = 'homepage.php';
                        }, 1500);
                    }, 2000);
                </script>
            </body>
            </html>
            <?php
        } else {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    .error-popup {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        background: white;
                        padding: 20px;
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0,0,0,0.2);
                        text-align: center;
                        animation: fadeIn 0.5s;
                        z-index: 1000;
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

                    .error-icon {
                        color: #ff4757;
                        font-size: 40px;
                        margin-bottom: 15px;
                    }

                    @keyframes fadeIn {
                        from { opacity: 0; transform: translate(-50%, -60%); }
                        to { opacity: 1; transform: translate(-50%, -50%); }
                    }
                </style>
            </head>
            <body>
                <div class="overlay"></div>
                <div class="error-popup">
                    <div class="error-icon">❌</div>
                    <h3>Login Failed</h3>
                    <p>Incorrect email or password</p>
                    <button onclick="window.history.back()" style="
                        padding: 10px 20px;
                        background: #ff4757;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        margin-top: 10px;
                    ">Try Again</button>
                </div>
            </body>
            </html>
            <?php
        }
    } else {
        // Show same error popup for non-existent email
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                /* Same error popup styling as above */
            </style>
        </head>
        <body>
            <div class="overlay"></div>
            <div class="error-popup">
                <div class="error-icon">❌</div>
                <h3>Login Failed</h3>
                <p>Incorrect email or password</p>
                <button onclick="window.history.back()">Try Again</button>
            </div>
        </body>
        </html>
        <?php
    }
}
?>