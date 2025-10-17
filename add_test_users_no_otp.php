<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

use MongoDB\BSON\UTCDateTime;

echo "<h2>Adding Test Users (No OTP Required)</h2>";

// Sample users data without OTP secrets for easier testing
$sampleUsers = [
    [
        'email' => 'test.employee@example.com',
        'name' => 'Test Employee',
        'role' => 'employee',
        'department' => 'Human Resources'
    ],
    [
        'email' => 'test.dept.head@example.com',
        'name' => 'Test Department Head',
        'role' => 'department_head',
        'department' => 'Computer Science'
    ],
    [
        'email' => 'test.faculty@example.com',
        'name' => 'Test Faculty',
        'role' => 'faculty',
        'department' => 'Mathematics'
    ],
    [
        'email' => 'test.staff@example.com',
        'name' => 'Test Staff',
        'role' => 'staff',
        'department' => 'Finance'
    ],
    [
        'email' => 'test.applicant@example.com',
        'name' => 'Test Applicant',
        'role' => 'applicant'
    ]
];

try {
    $insertedCount = 0;
    
    foreach ($sampleUsers as $userData) {
        // Check if user already exists
        $existingUser = $usersCollection->findOne(['email' => $userData['email']]);
        
        if (!$existingUser) {
            // Add timestamp
            $userData['created_at'] = new UTCDateTime();
            // Add empty OTP secret for testing
            $userData['otp_secret'] = '';
            
            // Insert user
            $insertResult = $usersCollection->insertOne($userData);
            
            if ($insertResult->getInsertedCount() > 0) {
                echo "<p style='color: green;'>Added test user: " . htmlspecialchars($userData['email']) . " with role: " . htmlspecialchars($userData['role']) . "</p>";
                $insertedCount++;
            }
        } else {
            echo "<p>User " . htmlspecialchars($userData['email']) . " already exists. Skipping.</p>";
        }
    }
    
    echo "<h3>Completed!</h3>";
    echo "<p>Successfully added $insertedCount test users to the database.</p>";
    echo "<p>These users can log in using the test mode (check 'Test Mode' checkbox on login page).</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error adding test users: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='users/employees/login.php'>Go to Login Page</a> | <a href='index.php'>Back to Main Page</a></p>";
?>