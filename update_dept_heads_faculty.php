<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

use MongoDB\BSON\UTCDateTime;

echo "<h2>Updating Department Heads and Faculty Users</h2>";

// Department heads with can_evaluate arrays for multiple programs
$deptHeads = [
    [
        'email' => 'test.dept.head@example.com',
        'name' => 'Test Department Head',
        'role' => 'department_head',
        'department' => 'Computer Science',
        'can_evaluate' => ['BSIS', 'BSME', 'BSTM', 'BSN']
    ]
];

// Additional department heads for different departments with specific program evaluations
$additionalDeptHeads = [
    [
        'email' => 'cs.dean@example.com',
        'name' => 'Dr. Alan Turing',
        'role' => 'department_head',
        'department' => 'Computer Science',
        'can_evaluate' => ['BSIS', 'BSME'] // Can evaluate Information Systems and Mechanical Engineering
    ],
    [
        'email' => 'nursing.dean@example.com',
        'name' => 'Dr. Florence Nightingale',
        'role' => 'department_head',
        'department' => 'Nursing',
        'can_evaluate' => ['BSN'] // Can evaluate Nursing program
    ],
    [
        'email' => 'tourism.dean@example.com',
        'name' => 'Dr. Marco Polo',
        'role' => 'department_head',
        'department' => 'Tourism Management',
        'can_evaluate' => ['BSTM'] // Can evaluate Tourism Management
    ],
    [
        'email' => 'engineering.dean@example.com',
        'name' => 'Dr. Nikola Tesla',
        'role' => 'department_head',
        'department' => 'Engineering',
        'can_evaluate' => ['BSME'] // Can evaluate Mechanical Engineering
    ]
];

// Faculty members - one each per department head and 3-5 each for faculty types
$facultyMembers = [
    // Computer Science Faculty (5 members)
    [
        'email' => 'cs.faculty1@example.com',
        'name' => 'Prof. Ada Lovelace',
        'role' => 'faculty',
        'department' => 'Computer Science',
        'position' => 'Professor'
    ],
    [
        'email' => 'cs.faculty2@example.com',
        'name' => 'Dr. Grace Hopper',
        'role' => 'faculty',
        'department' => 'Computer Science',
        'position' => 'Associate Professor'
    ],
    [
        'email' => 'cs.faculty3@example.com',
        'name' => 'Prof. Linus Torvalds',
        'role' => 'faculty',
        'department' => 'Computer Science',
        'position' => 'Assistant Professor'
    ],
    [
        'email' => 'cs.faculty4@example.com',
        'name' => 'Dr. Tim Berners-Lee',
        'role' => 'faculty',
        'department' => 'Computer Science',
        'position' => 'Instructor'
    ],
    [
        'email' => 'cs.faculty5@example.com',
        'name' => 'Prof. Margaret Hamilton',
        'role' => 'faculty',
        'department' => 'Computer Science',
        'position' => 'Lecturer'
    ],
    
    // Nursing Faculty (3 members)
    [
        'email' => 'nursing.faculty1@example.com',
        'name' => 'Dr. Mary Eliza Mahoney',
        'role' => 'faculty',
        'department' => 'Nursing',
        'position' => 'Professor'
    ],
    [
        'email' => 'nursing.faculty2@example.com',
        'name' => 'Prof. Dorothea Dix',
        'role' => 'faculty',
        'department' => 'Nursing',
        'position' => 'Associate Professor'
    ],
    [
        'email' => 'nursing.faculty3@example.com',
        'name' => 'Dr. Clara Barton',
        'role' => 'faculty',
        'department' => 'Nursing',
        'position' => 'Assistant Professor'
    ],
    
    // Tourism Management Faculty (3 members)
    [
        'email' => 'tourism.faculty1@example.com',
        'name' => 'Prof. Ernest Hemingway',
        'role' => 'faculty',
        'department' => 'Tourism Management',
        'position' => 'Professor'
    ],
    [
        'email' => 'tourism.faculty2@example.com',
        'name' => 'Dr. Marco Polo Jr.',
        'role' => 'faculty',
        'department' => 'Tourism Management',
        'position' => 'Associate Professor'
    ],
    [
        'email' => 'tourism.faculty3@example.com',
        'name' => 'Prof. Ibn Battuta',
        'role' => 'faculty',
        'department' => 'Tourism Management',
        'position' => 'Assistant Professor'
    ],
    
    // Engineering Faculty (4 members)
    [
        'email' => 'engineering.faculty1@example.com',
        'name' => 'Dr. Thomas Edison',
        'role' => 'faculty',
        'department' => 'Engineering',
        'position' => 'Professor'
    ],
    [
        'email' => 'engineering.faculty2@example.com',
        'name' => 'Prof. Alexander Graham Bell',
        'role' => 'faculty',
        'department' => 'Engineering',
        'position' => 'Associate Professor'
    ],
    [
        'email' => 'engineering.faculty3@example.com',
        'name' => 'Dr. Galileo Galilei',
        'role' => 'faculty',
        'department' => 'Engineering',
        'position' => 'Assistant Professor'
    ],
    [
        'email' => 'engineering.faculty4@example.com',
        'name' => 'Prof. Isaac Newton',
        'role' => 'faculty',
        'department' => 'Engineering',
        'position' => 'Instructor'
    ]
];

try {
    $updatedCount = 0;
    $insertedCount = 0;
    
    // Update existing department heads
    foreach ($deptHeads as $deptHead) {
        $result = $usersCollection->updateOne(
            ['email' => $deptHead['email']],
            ['$set' => $deptHead]
        );
        
        if ($result->getModifiedCount() > 0) {
            echo "<p style='color: green;'>Updated department head: " . htmlspecialchars($deptHead['email']) . "</p>";
            $updatedCount++;
        }
    }
    
    // Add additional department heads
    foreach ($additionalDeptHeads as $deptHead) {
        // Check if user already exists
        $existingUser = $usersCollection->findOne(['email' => $deptHead['email']]);
        
        if (!$existingUser) {
            // Add timestamp and empty OTP secret for testing
            $deptHead['created_at'] = new UTCDateTime();
            $deptHead['otp_secret'] = '';
            
            // Insert user
            $insertResult = $usersCollection->insertOne($deptHead);
            
            if ($insertResult->getInsertedCount() > 0) {
                echo "<p style='color: green;'>Added department head: " . htmlspecialchars($deptHead['email']) . " (" . htmlspecialchars($deptHead['department']) . ")</p>";
                echo "<p style='color: blue; margin-left: 20px;'>Can evaluate programs: " . implode(', ', $deptHead['can_evaluate']) . "</p>";
                $insertedCount++;
            }
        } else {
            echo "<p>User " . htmlspecialchars($deptHead['email']) . " already exists. Skipping.</p>";
        }
    }
    
    // Add faculty members
    foreach ($facultyMembers as $faculty) {
        // Check if user already exists
        $existingUser = $usersCollection->findOne(['email' => $faculty['email']]);
        
        if (!$existingUser) {
            // Add timestamp and empty OTP secret for testing
            $faculty['created_at'] = new UTCDateTime();
            $faculty['otp_secret'] = '';
            
            // Insert user
            $insertResult = $usersCollection->insertOne($faculty);
            
            if ($insertResult->getInsertedCount() > 0) {
                echo "<p style='color: green;'>Added faculty: " . htmlspecialchars($faculty['email']) . " (" . htmlspecialchars($faculty['department']) . ")</p>";
                $insertedCount++;
            }
        } else {
            echo "<p>User " . htmlspecialchars($faculty['email']) . " already exists. Skipping.</p>";
        }
    }
    
    echo "<h3>Completed!</h3>";
    echo "<p>Updated $updatedCount existing users and added $insertedCount new users.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error updating users: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='view_users.php'>View All Users</a> | <a href='index.php'>Back to Main Page</a></p>";
?>