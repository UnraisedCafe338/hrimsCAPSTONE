<?php
// Script to update existing users with role field
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "Updating users with role field...\n";

// Update all existing users to have 'admin' role by default
// (assuming existing users are admins since there was no distinction before)
$updateResult = $usersCollection->updateMany(
    ['role' => ['$exists' => false]], // Find users without role field
    ['$set' => ['role' => 'admin']]    // Set role to admin
);

echo "Updated " . $updateResult->getModifiedCount() . " users with 'admin' role.\n";

// Show all users
echo "\nCurrent users in database:\n";
$users = $usersCollection->find();
foreach ($users as $user) {
    echo "- " . $user['email'] . " (" . ($user['role'] ?? 'no role') . ")\n";
}

echo "\nDone!\n";
?>