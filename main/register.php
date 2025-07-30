<?php
session_start();
require '../connection.php'; // Assuming this is where the connection to your DB is established
require '../vendor/autoload.php'; // Make sure the autoloader is included

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

// Handle form submission for new user registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gmail = $_POST['gmail']; // Get the email entered by user
    $password = $_POST['password']; // Get the password (you should hash it before storing)
    
    // Create a new OTP secret
    $g = new GoogleAuthenticator();
    $otp_secret = $g->generateSecret();

    // Insert the new user into the database
    $collection = $database->users; // Assuming 'users' is your MongoDB collection
    $collection->insertOne([
        'email' => $gmail,
        'password' => password_hash($password, PASSWORD_DEFAULT), // Store hashed password
        'otp_secret' => $otp_secret, // Store the OTP secret
    ]);
    
    // Store OTP secret in session for later use (to show QR code)
    $_SESSION['new_otp_secret'] = $otp_secret;
    $_SESSION['new_user_email'] = $gmail; // Save email for context
    $_SESSION['message'] = "Account created successfully! Please scan the QR code below to set up two-factor authentication.";

    // Redirect to the same page to display the QR code
    header("Location: register.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Account</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Georgia&display=swap">
    <style>
        body {
            margin: 0;
            font-family: Georgia, serif;
           
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #1e1e1e;
            
        }
            .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
             background: url('../images/exact_school_front.png') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            filter: blur(3px); 
            z-index: -1;
        }

     
        .content{
            backdrop-filter: blur(3px);
        }
        .container {
            display: flex;
            justify-content: space-between;
            gap: 5px;
            background-color: rgba(122, 122, 122, 0.18);
            border-radius: 10px;
            padding: 30px;
            width: 1000px;
            max-width: 90%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0);
        }

        .form-box, .qr-box {
            border-radius: 10px;
            border:rgb(255, 255, 255) 2px solid;
            flex: 1;
            height: 400px;
            text-align: center;
        }

        .form-box h2, .qr-box h2 {
            color:rgb(0, 96, 192);
            margin-bottom: 10px;
            font-size: 2.5em;
        }

        .form-box p {
            font-size: 0.9em;
            color: white;
        }

        .form-box input {
            width: 50%;
            height: 10px;
            padding: 25px;
            margin: 20px 0;
            border: 1px solid #aaa;
            border-radius: 0px;
            font-size: 1.5em;
            background-color: none;
        }

        .form-box button {
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            border: white 1px solid;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .form-box button:hover {
            background-color: #0055aa;
        }

        .message {
            margin-top: 15px;
            color: white;
            font-weight: bold;
        }

        img.qr-code {
            width: 200px;
            height: 200px;
        }

        .top-logo {
            position: absolute;
            top: 10px;
            text-align: center;
          
        }

        .top-logo img {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="top-logo">
        <img src="../images/exact logo.png" alt="Logo">
    </div>
    <div class="container">
        <div class="form-box">
            <h2>Create Account</h2>
            <!-- <p>Already have an account? <a href="login.php">Sign-in</a></p> -->
            <form method="POST" action="register.php">
                <input type="email" name="gmail" placeholder="Email" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Register</button>
            </form>
        </div>

        <div class="qr-box">
            <h2>Scan this QR Code</h2>
            <p>(Google Authenticator)</p>
            <?php
            if (isset($_SESSION['new_otp_secret']) && isset($_SESSION['new_user_email'])) {
                $otp_secret = $_SESSION['new_otp_secret'];
                $user_email = $_SESSION['new_user_email'];
                $qrUrl = GoogleQrUrl::generate($user_email, $otp_secret, 'HRIMS');
                echo "<img class='qr-code' src='$qrUrl' alt='QR Code for OTP setup'>";
                echo "<div class='message'>{$_SESSION['message']}</div>";
                unset($_SESSION['new_otp_secret']);
                unset($_SESSION['new_user_email']);
                unset($_SESSION['message']);
            }
            ?>
        </div>
    </div>
    
</body>
</html>
