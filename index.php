<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gmail = $_POST['gmail'];
    $otp = $_POST['otp'];

    // Find the user by email
    $user = $usersCollection->findOne(["email" => $gmail]);

    if ($user) {
        // Validate the OTP using GoogleAuthenticator
        $g = new GoogleAuthenticator();
        $isValid = $g->checkCode($user['otp_secret'], $otp);

        if ($isValid) {
            // Start the session and store user information
            $_SESSION['username'] = $gmail;
            $_SESSION['user_id'] = (string)$user['_id'];  // Store user ID if needed for future use
            $_SESSION['user_email'] = $user['email'];    // Store email

            // Redirect to sidebar menu
            header("Location: users/admin/dashboard.php");
            exit();
        } else {
            $error_message = "Invalid OTP.";
        }
    } else {
        $error_message = "Email is incorrect or not found. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="images/system-logo.png">
    <title>Human Resources Information Management System | Login</title>
    <link rel="stylesheet" href="assets/css/all.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background-image: url('images/hrback.jpg');
            background-size: cover;
            background-position: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 1);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
            height: auto;
        }

        .login-container img {
            width: 100px;
            margin-bottom: 10px;
        }

        .login-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid rgba(255, 0, 0, 0.5);
            background: rgba(255, 0, 0, 0.1);
            border-radius: 5px;
            opacity: 1;
            transition: opacity 3s ease-in-out;
        }

        .input-group {
            display: flex;
            align-items: center;
            background: rgb(205, 205, 205);
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .input-group i {
            color: #002afc;
            margin-right: 10px;
        }

        .input-group input {
            border: none;
            padding: 10px;
            width: 100%;
            background: transparent;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background: rgb(10, 41, 214);
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 10px;
        }

        .login-container button:hover {
            background: #001f80;
        }

        .register-button {
            background-color: #4CAF50;
        }

        .register-button:hover {
            background-color: #388E3C;
        }

        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }

        .input-group input:focus {
            border-bottom: 2px solid #002afc;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="images/exact logo.png" alt="Logo">
        <h2>Human Resources Information Management System</h2>
        <p>Please enter your login details.</p>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="gmail" class="input-label">Gmail</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="gmail" name="gmail" required>
                </div>
            </div>

            <div class="form-group">
                <label for="otp" class="input-label">OTP</label>
                <div class="input-group">
                    <i class="fas fa-key"></i>
                    <input type="text" id="otp" name="otp" required pattern="\d{6}" title="Enter 6-digit OTP" maxlength="6">
                </div>
            </div>
            
            <?php if (!empty($error_message)) { echo "<div class='error-message' id='errorMessage'>$error_message</div><br>"; } ?>
            
            <button type="submit">Log In</button>
        </form>
        
        <p style="margin-top: 20px;">
            <a href="users/employees/login.php" style="color: #002afc; text-decoration: none; font-weight: bold;">
                Employee/Dean Login
            </a>
        </p>
        
        <!-- Admin Tools -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ccc;">
            <h4>Admin Tools</h4>
            <p>
                <a href="view_users.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">View Users</a>
                <a href="set_user_roles.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">Manage Roles</a>
                <a href="add_sample_users.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">Add Sample Users</a>
                <a href="add_test_users_no_otp.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">Add Test Users</a>
                <a href="update_dept_heads_faculty.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">Update Dept Heads & Faculty</a>
                <a href="view_all_users_with_roles.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">View All Users with Roles</a>
                <a href="check_user_data.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">Check User Data</a>
                <a href="test_mongodb.php" style="color: #002afc; text-decoration: none; margin-right: 15px;">Test MongoDB</a>
                <a href="init_mongodb.php" style="color: #002afc; text-decoration: none;">Initialize DB</a>
            </p>
        </div>
    </div>

    <script>
        setTimeout(function() {
            var errorMessage = document.getElementById("errorMessage");
            if (errorMessage) {
                errorMessage.style.opacity = "0";
            }
        }, 3000);
    </script>
</body>
</html>