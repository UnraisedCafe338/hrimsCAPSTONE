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
            min-height: 100vh;
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
            width: 100%;
            max-width: 700px; /* Increased width for rectangular shape */
            height: auto;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin: 20px;
        }

        .login-container img {
            width: 120px;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .login-container h2 {
            font-size: 28px;
            color: #1a2a6c;
            margin-bottom: 10px;
        }

        .login-container p {
            color: #555;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .info-section {
            background: rgba(26, 42, 108, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-section p {
            margin: 0;
            color: #444;
            font-size: 15px;
        }

        .info-btn {
            background: #1a2a6c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .info-btn:hover {
            background: #2a4b8d;
            transform: scale(1.1);
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
            padding: 15px 20px;
            margin-bottom: 25px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #1a2a6c;
            box-shadow: 0 0 0 2px rgba(26, 42, 108, 0.2);
        }

        .input-group i {
            color: #1a2a6c;
            margin-right: 15px;
            font-size: 20px;
        }

        .input-group input {
            border: none;
            padding: 10px 0;
            width: 100%;
            background: transparent;
            outline: none;
            font-size: 16px;
            color: #333;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #444;
            font-weight: 500;
            font-size: 16px;
        }

        .login-container .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(to right, #1a2a6c, #2a4b8d);
            color: white;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(26, 42, 108, 0.3);
        }

        .login-container button:hover {
            background: linear-gradient(to right, #2a4b8d, #3a6cb0);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 42, 108, 0.4);
        }

        .footer-text {
            color: #666;
            font-size: 15px;
            margin-top: 25px;
        }

        .admin-link {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 600;
        }

        .admin-link:hover {
            text-decoration: underline;
        }

        .test-mode {
            text-align: left;
            margin-bottom: 20px;
        }

        .test-mode label {
            font-weight: normal;
            display: flex;
            align-items: center;
            font-size: 15px;
            color: #555;
        }

        .test-mode input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: modalopen 0.4s;
        }

        @keyframes modalopen {
            from {opacity: 0; transform: translateY(-60px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close:hover,
        .close:focus {
            color: #1a2a6c;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
            color: #1a2a6c;
            font-size: 24px;
        }

        .modal-body ul {
            text-align: left;
            padding-left: 25px;
            margin: 20px 0;
        }

        .modal-body li {
            margin-bottom: 12px;
            font-size: 16px;
            color: #555;
        }

        .modal-body p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-container {
                padding: 30px 20px;
                margin: 15px;
                max-width: 90%;
            }
            
            .login-container h2 {
                font-size: 24px;
            }
            
            .input-group {
                padding: 12px 15px;
            }
            
            .input-group i {
                margin-right: 10px;
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 25px 15px;
                margin: 10px;
            }
            
            .login-container h2 {
                font-size: 22px;
            }
            
            .login-container p {
                font-size: 14px;
            }
            
            .input-group {
                padding: 10px 12px;
            }
            
            .input-group input {
                font-size: 14px;
            }
            
            .modal-content {
                padding: 20px;
                margin: 20% auto;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../../images/exact logo.png" alt="Logo">
        <h2>Employee Portal</h2>
        <!-- <p>Dean Evaluation System</p> -->
        
        <div class="info-section">
            <p>Login with your institutional email and OTP code</p>
            <button class="info-btn" id="infoBtn" title="View accessible roles">i</button>
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
            
            <div class="test-mode">
                <label>
                    <input type="checkbox" id="testMode" name="test_mode" value="1">
                    Test Mode (Skip OTP verification)
                </label>
            </div>
            
            <?php if (!empty($error_message)) { echo "<div class='error-message' id='errorMessage'>$error_message</div>"; } ?>
            
            <button class="login-button" type="submit">Sign In</button>
        </form>
        
        <p class="footer-text">
            <a href="../../index.php" class="admin-link">Admin Login</a> | 
            <a href="#" class="admin-link">Need Help?</a>
        </p>
    </div>

    <!-- Modal for accessible roles -->
    <div id="rolesModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h3>Accessible Roles</h3>
            </div>
            <div class="modal-body">
                <p>The following roles can access this system:</p>
                <ul>
                    <li><strong>Employees</strong> - Regular staff members</li>
                    <li><strong>Department Heads</strong> - Administrative leads</li>
                    <li><strong>Faculty Members</strong> - Teaching staff</li>
                    <li><strong>Staff</strong> - Support personnel</li>
                    <!-- <li><strong>Applicants</strong> - Job candidates</li> -->
                </ul>
                <p>Each role has specific permissions and access levels within the system.</p>
            </div>
        </div>
    </div>

    <script>
        // Get modal elements
        const modal = document.getElementById("rolesModal");
        const btn = document.getElementById("infoBtn");
        const span = document.getElementsByClassName("close")[0];

        // Open modal when info button is clicked
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close modal when X is clicked
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

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