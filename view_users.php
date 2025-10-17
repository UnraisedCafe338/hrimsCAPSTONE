<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "<h2>HRIMS User List</h2>";

try {
    // Fetch all users
    $users = $usersCollection->find([], ['sort' => ['role' => 1, 'email' => 1]]);
    
    $usersArray = $users->toArray();
    if (count($usersArray) > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
        echo "<thead style='background-color: #f2f2f2;'>";
        echo "<tr>";
        echo "<th>Email</th>";
        echo "<th>Name</th>";
        echo "<th>Role</th>";
        echo "<th>Department</th>";
        echo "<th>Created At</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($usersArray as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['role'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['department'] ?? 'N/A') . "</td>";
            echo "<td>" . (isset($user['created_at']) ? $user['created_at']->toDateTime()->format('Y-m-d H:i:s') : 'N/A') . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No users found in the database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching users: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='set_user_roles.php'>Manage User Roles</a> | <a href='add_sample_users.php'>Add Sample Users</a> | <a href='index.php'>Back to Main Page</a></p>";
?>