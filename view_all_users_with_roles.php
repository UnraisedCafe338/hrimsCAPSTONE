<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "<h2>All Users with Roles</h2>";
echo "<a href='index.php'>Back to Main Page</a><br><br>";

try {
    // Fetch all users
    $users = $usersCollection->find();
    
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Email</th><th>Name</th><th>Role</th><th>Department</th><th>Can Evaluate</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>" . htmlspecialchars($user['department'] ?? 'N/A') . "</td>";
        
        // Display can_evaluate field for department heads
        if (isset($user['can_evaluate']) && is_array($user['can_evaluate'])) {
            echo "<td>" . htmlspecialchars(implode(', ', $user['can_evaluate'])) . "</td>";
        } else {
            echo "<td>N/A</td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching users: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>Back to Main Page</a>";
?>