<?php
// Script to add a test employee user for testing the employee login
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

echo "Adding test employee user...\n";

// Create a new OTP secret
$g = new GoogleAuthenticator();
$otp_secret = $g->generateSecret();

// Insert test employee into the database
$email = "dean.cs@institution.edu";
$usersCollection->insertOne([
    'email' => $email,
    'first_name' => 'Dean',
    'last_name' => 'ComputerScience',
    'employee_id' => 'EMP001',
    'position' => 'Dean',
    'department' => 'Computer Science',
    'role' => 'employee',
    'otp_secret' => $otp_secret,
    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

// Also insert into employees collection for additional data
$database->selectCollection("employees")->insertOne([
    'email' => $email,
    'first_name' => 'Dean',
    'last_name' => 'ComputerScience',
    'employee_id' => 'EMP001',
    'position' => 'Dean',
    'department' => 'Computer Science',
    'role' => 'employee',
    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

echo "Test employee added successfully!\n";
echo "Email: " . $email . "\n";
echo "OTP Secret: " . $otp_secret . "\n";
echo "Use an OTP generator with the secret above to generate codes for login testing.\n";
?>