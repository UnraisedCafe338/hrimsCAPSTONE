<?php

session_start();
require '../connection.php';
require '../vendor/autoload.php';

use MongoDB\BSON\ObjectId;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

$collection = $database->users;

// Fetch the admin user (you can modify the filter to get a specific admin if needed)
$admin = $collection->findOne([]); 

if (!$admin) {
    die("Admin user not found.");
}

$message = "";

// Handle Gmail (email) update
if (isset($_POST['update_email'])) {
    $new_email = trim($_POST['new_email']);

    if (!empty($new_email)) {
        $existingEmail = $collection->findOne(['email' => $new_email]);
        if ($existingEmail) {
            $message = "Email already taken.";
        } else {
            $collection->updateOne(
                ['_id' => $admin['_id']],
                ['$set' => ['email' => $new_email]]
            );
            $message = "Email updated successfully!";
        }
    } else {
        $message = "Email cannot be empty.";
    }
}

// Handle OTP secret update (regenerate new OTP)
if (isset($_POST['update_otp'])) {
    $g = new GoogleAuthenticator();
    $new_secret = $g->generateSecret();

    // Update the secret in the database
    $collection->updateOne(
        ['_id' => $admin['_id']],
        ['$set' => ['otp_secret' => $new_secret]]
    );
    
    // Store the new secret in session so it can be used for the QR code immediately
    $_SESSION['new_otp_secret'] = $new_secret;
    
    $message = "OTP secret regenerated successfully! Please scan the new QR code.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
</head>
<style>
            .settings-button {
            background-color: #00124d;
            border-left: 4px solid #ffffff;
        }

    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
    }

    .content {
        margin-left: 220px; /* Account for sidebar */
        padding: 20px;
      
    }

    .header {
        font-size: 28px;
        font-weight: bold;
        margin-left: 220px;
        margin-top: 20px;
        color: #003366;
        border-bottom: 4px solid #ffcc00;
        padding-bottom: 10px;
        width: fit-content;
    }

    form {
        margin-bottom: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        width: 100%;
        max-width: 500px;
    }

    input[type="email"] {
        width: calc(100% - 120px);
        padding: 10px;
        margin-right: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    button {
        background-color: #002d72;
        color: white;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0048a1;
    }

    .register-button {
        background-color: #f5f5f5;
        border: 1px solid #ccc;
        color: #002d72;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: bold;
    }

    .register-button:hover {
        background-color: #e6e6e6;
    }

    h3 {
        margin-top: 10px;
        color: #002d72;
    }

    #pageIndicator {
        font-weight: bold;
        color: #003366;
        margin-bottom: 10px;
    }

    img {
        margin-top: 10px;
        max-width: 200px;
    }

    p {
        font-size: 16px;
    }

    .success {
        color: green;
        font-weight: bold;
    }
</style>
<body>
    

<?php include 'sidebar.php'; ?>
      <div class="header">Admin Settings</div><br><br><br>

<div class="content">

    <div class="card">
        <p><strong>Gmail (Current):</strong> <?php echo htmlspecialchars($admin['email'] ?? 'No email set'); ?></p>
        <form method="POST">
            <input type="email" name="new_email" placeholder="New Gmail Address" required>
            <button type="submit" name="update_email">Edit Email</button>
        </form>
    </div>

    <div class="card">
        <h3>Update OTP</h3>
        <form method="POST">
            <button type="submit" name="update_otp">Regenerate New OTP Secret</button>
        </form>
    </div>

    <div class="card">
<?php 
        if (isset($_SESSION['new_otp_secret'])) {
            $otp_secret = $_SESSION['new_otp_secret']; // Get the new OTP secret from session
        } else {
            $otp_secret = $admin['otp_secret']; // Use the existing secret if not regenerated
        }

        if (isset($otp_secret)): 
    ?>
        <h3>Scan this QR Code (Google Authenticator)</h3>
        <?php
            $qrUrl = GoogleQrUrl::generate(
                $admin['email'] ?? 'admin@hrims.local', // Email will show on Google Authenticator
                $otp_secret,  // OTP secret (either old or new)
                'HRIMS' // Your system name
            );
            echo "<img src=\"$qrUrl\">";
        ?>
    <?php endif; ?>
        <p class="success"><?php echo $message; ?></p>

    </div>

    <div class="card">
        <form action="register.php" method="GET">
            <button type="submit" class="register-button">Register New Account</button>
        </form>
    </div>

</div>
</body>
