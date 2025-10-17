<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "<h2>Checking User Data</h2>";
echo "<a href='index.php'>Back to Main Page</a><br><br>";

try {
    // Check if we can connect to MongoDB
    echo "<p>MongoDB connection successful.</p>";
    
    // Count total users
    $userCount = $usersCollection->countDocuments();
    echo "<p>Total users in database: $userCount</p>";
    
    // Find department heads
    $deptHeads = $usersCollection->find(['role' => 'department_head']);
    echo "<h3>Department Heads:</h3>";
    echo "<ul>";
    foreach ($deptHeads as $head) {
        echo "<li>" . htmlspecialchars($head['name']) . " (" . htmlspecialchars($head['email']) . ") - Department: " . htmlspecialchars($head['department']);
        if (isset($head['can_evaluate']) && is_array($head['can_evaluate'])) {
            echo " - Can evaluate: " . htmlspecialchars(implode(', ', $head['can_evaluate']));
        }
        echo "</li>";
    }
    echo "</ul>";
    
    // Find faculty members
    $faculty = $usersCollection->find(['role' => 'faculty']);
    echo "<h3>Faculty Members:</h3>";
    echo "<ul>";
    foreach ($faculty as $member) {
        echo "<li>" . htmlspecialchars($member['name']) . " (" . htmlspecialchars($member['email']) . ") - Department: " . htmlspecialchars($member['department'] ?? 'N/A');
        if (isset($member['position'])) {
            echo " - Position: " . htmlspecialchars($member['position']);
        }
        echo "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>Back to Main Page</a>";
?>