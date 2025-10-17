<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

use MongoDB\BSON\UTCDateTime;

echo "<h2>Adding Sample Users to HRIMS</h2>";

// Sample users data
$sampleUsers = [
    [
        'email' => 'admin@example.com',
        'name' => 'System Administrator',
        'role' => 'admin',
        'department' => 'Administration'
    ],
    [
        'email' => 'employee@example.com',
        'name' => 'John Employee',
        'role' => 'employee',
        'department' => 'Human Resources'
    ],
    [
        'email' => 'dept.head@example.com',
        'name' => 'Sarah Department Head',
        'role' => 'department_head',
        'department' => 'Computer Science'
    ],
    [
        'email' => 'faculty@example.com',
        'name' => 'Dr. Michael Faculty',
        'role' => 'faculty',
        'department' => 'Mathematics'
    ],
    [
        'email' => 'staff@example.com',
        'name' => 'Emma Staff',
        'role' => 'staff',
        'department' => 'Finance'
    ],
    [
        'email' => 'applicant@example.com',
        'name' => 'Robert Applicant',
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
            
            // Insert user
            $insertResult = $usersCollection->insertOne($userData);
            
            if ($insertResult->getInsertedCount() > 0) {
                echo "<p style='color: green;'>Added user: " . htmlspecialchars($userData['email']) . " with role: " . htmlspecialchars($userData['role']) . "</p>";
                $insertedCount++;
            }
        } else {
            echo "<p>User " . htmlspecialchars($userData['email']) . " already exists. Skipping.</p>";
        }
    }
    
    echo "<h3>Completed!</h3>";
    echo "<p>Successfully added $insertedCount new users to the database.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error adding sample users: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='set_user_roles.php'>Manage User Roles</a> | <a href='index.php'>Back to Main Page</a></p>";
?>