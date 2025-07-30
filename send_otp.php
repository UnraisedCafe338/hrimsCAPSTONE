<?php
// Include necessary files and configurations
require 'vendor/autoload.php';  // Ensure Composer autoloader is included
include('connection.php');

// Define sendOTPEmail function (either use mail() or PHPMailer as mentioned earlier)
function sendOTPEmail($email, $otp) {
    // Use PHPMailer or PHP's mail() function here.
    // Example using PHP's mail() function:
    $subject = "Your OTP Code";
    $message = "Your OTP code is: $otp. It will expire in 5 minutes.";
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send the email
    return mail($email, $subject, $message, $headers);
}

session_start();

$error_message = "";
$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $otp_input = $_POST['otp'] ?? null;

    $user = $usersCollection->findOne(["username" => $username]);

    if ($user) {
        if (!empty($otp_input)) {
            // ✅ OTP Verification
            $otp_correct = $_SESSION['otp'] ?? null;
            $otp_expiry = $_SESSION['otp_expiry'] ?? 0;

            if (time() > $otp_expiry) {
                $error_message = "OTP expired. Please refresh and try again.";
                $step = 1;
            } elseif ($otp_input == $otp_correct) {
                $_SESSION['username'] = $username;
                unset($_SESSION['otp']);
                unset($_SESSION['otp_expiry']);
                header("Location: sidebar_menu/sidebar.php");
                exit();
            } else {
                $error_message = "Invalid OTP.";
                $step = 2;
            }
        } else {
            // ✅ Send OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + 300;

            if (sendOTPEmail($user['email'], $otp)) {
                $step = 2; // Move to OTP input
                $error_message = "OTP sent to your email.";
            } else {
                $error_message = "Failed to send OTP.";
            }
        }
    } else {
        $error_message = "User not found.";
    }
}
?>
