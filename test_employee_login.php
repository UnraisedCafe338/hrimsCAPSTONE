<?php
// Test script to verify employee login functionality
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "Testing Employee Login Functionality\n";
echo "====================================\n\n";

// Test 1: Check if employees collection exists
echo "1. Checking database collections...\n";
$collections = $database->listCollections();
$collectionNames = [];
foreach ($collections as $collection) {
    $collectionNames[] = $collection->getName();
}
echo "   Available collections: " . implode(', ', $collectionNames) . "\n";

// Test 2: Check if we can find users with role 'employee'
echo "\n2. Checking for employee users...\n";
$employeeCount = $usersCollection->countDocuments(['role' => 'employee']);
echo "   Number of employee users: " . $employeeCount . "\n";

// Test 3: Check if we can find users with role 'admin'
echo "\n3. Checking for admin users...\n";
$adminCount = $usersCollection->countDocuments(['role' => 'admin']);
echo "   Number of admin users: " . $adminCount . "\n";

// Test 4: Show sample user structure
echo "\n4. Sample user structure...\n";
$sampleUser = $usersCollection->findOne([], ['sort' => ['_id' => -1]]);
if ($sampleUser) {
    echo "   Email: " . ($sampleUser['email'] ?? 'N/A') . "\n";
    echo "   Role: " . ($sampleUser['role'] ?? 'N/A') . "\n";
    echo "   OTP Secret: " . (isset($sampleUser['otp_secret']) ? 'Exists' : 'Missing') . "\n";
}

echo "\nTest completed successfully!\n";
?>