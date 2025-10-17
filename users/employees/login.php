<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include('../../handlers/connection.php');

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = $_POST['otp'] ?? '';

    // Find the employee user by email (any employee role)
    $user = $usersCollection->findOne([
        "email" => $email,
        "role" => ['$in' => ['employee', 'department_head', 'faculty', 'staff', 'applicant']]
    ]);

    if ($user) {
        // Check if we're in test mode (bypass OTP)
        $isTestMode = isset($_POST['test_mode']) && $_POST['test_mode'] == '1';
        
        if ($isTestMode) {
            // In test mode, bypass OTP verification
            $isValid = true;
        } else {
            // Validate the OTP using GoogleAuthenticator
            $g = new GoogleAuthenticator();
            $isValid = $g->checkCode($user['otp_secret'], $otp);
        }

        if ($isValid) {
            // Start the session and store user information
            $_SESSION['username'] = $email;
            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['department'] = $user['department'] ?? ''; // Store department if available

            // Redirect based on user role
            switch($user['role']) {
                case 'department_head':
                    header("Location: ../department_heads/dashboard.php");
                    break;
                case 'faculty':
                    header("Location: ../faculty/dashboard.php");
                    break;
                case 'staff':
                    header("Location: ../staff/dashboard.php");
                    break;
                case 'applicant':
                    header("Location: ../applicants/dashboard.php");
                    break;
                default: // employee
                    header("Location: dashboard.php");
                    break;
            }
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
    <link rel="icon" href="../../images/system-logo.png">
    <title>HRIMS | Employee Login</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a2a6c, #2a4b8d, #3a6cb0);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 420px;
            height: auto;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-container img {
            width: 120px;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .login-container h2 {
            font-size: 26px;
            color: #1a2a6c;
            margin-bottom: 10px;
        }

        .login-container p {
            color: #555;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 10px;
            padding: 12px;
            border: 1px solid rgba(231, 76, 60, 0.3);
            background: rgba(231, 76, 60, 0.1);
            border-radius: 8px;
            opacity: 1;
            transition: opacity 3s ease-in-out;
        }

        .input-group {
            display: flex;
            align-items: center;
            background: rgba(240, 240, 240, 0.8);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #1a2a6c;
            box-shadow: 0 0 0 2px rgba(26, 42, 108, 0.2);
        }

        .input-group i {
            color: #1a2a6c;
            margin-right: 12px;
            font-size: 18px;
        }

        .input-group input {
            border: none;
            padding: 8px;
            width: 100%;
            background: transparent;
            outline: none;
            font-size: 16px;
            color: #333;
        }

        .login-container button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #1a2a6c, #2a4b8d);
            color: white;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(26, 42, 108, 0.3);
        }

        .login-container button:hover {
            background: linear-gradient(to right, #2a4b8d, #3a6cb0);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(26, 42, 108, 0.4);
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-weight: 500;
        }

        .footer-text {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }

        .admin-link {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 600;
        }

        .admin-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../../images/exact logo.png" alt="Logo">
        <h2>Employee Portal</h2>
        <p>Dean Evaluation System</p>
        <div style="background: rgba(26, 42, 108, 0.1); padding: 15px; border-radius: 8px; margin: 15px 0; font-size: 14px;">
            <p><strong>Accessible Roles:</strong></p>
            <ul style="text-align: left; margin: 5px 0; padding-left: 20px;">
                <li>Employees</li>
                <li>Department Heads</li>
                <li>Faculty Members</li>
                <li>Staff</li>
                <li>Applicants</li>
            </ul>
            <p style="margin: 5px 0;">Login with your institutional email and OTP code (optional in test mode).</p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email" class="input-label">Work Email</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="your.email@institution.edu" required>
                </div>
            </div>

            <div class="form-group">
                <label for="otp" class="input-label">Authentication Code</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="text" id="otp" name="otp" pattern="\d{6}" title="Enter 6-digit OTP" maxlength="6" placeholder="123456">
                </div>
            </div>
            
            <div class="form-group" style="text-align: left; margin-bottom: 15px;">
                <label style="font-weight: normal; display: flex; align-items: center;">
                    <input type="checkbox" id="testMode" name="test_mode" value="1" style="margin-right: 8px;">
                    Test Mode (Skip OTP verification)
                </label>
            </div>
            
            <?php if (!empty($error_message)) { echo "<div class='error-message' id='errorMessage'>$error_message</div>"; } ?>
            
            <button type="submit">Sign In</button>
        </form>
        
        <p class="footer-text">
            <a href="../../index.php" class="admin-link">Admin Login</a> | 
            <a href="#" class="admin-link">Need Help?</a>
        </p>
    </div>

    <script>
        setTimeout(function() {
            var errorMessage = document.getElementById("errorMessage");
            if (errorMessage) {
                errorMessage.style.opacity = "0";
            }
        }, 5000);
        
        // Make OTP required only when not in test mode
        document.getElementById('testMode').addEventListener('change', function() {
            var otpField = document.getElementById('otp');
            if (this.checked) {
                otpField.removeAttribute('required');
            } else {
                otpField.setAttribute('required', 'required');
            }
        });
    </script>
</body>
</html>