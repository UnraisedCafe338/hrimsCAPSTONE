<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
if (!isset($_SESSION['registered_gmail']) || !isset($_SESSION['qr_code_url'])) {
    header('Location: register.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful - HRIMS</title>
</head>
<body>
    <h2>Registration Successful!</h2>
    <p>Scan this QR code using your Google Authenticator app:</p>
    <img src="<?php echo $_SESSION['qr_code_url']; ?>" alt="QR Code">

    <p><strong>Gmail:</strong> <?php echo $_SESSION['registered_gmail']; ?></p>

    <a href="../index.php">Go to Login</a>
</body>
</html>
