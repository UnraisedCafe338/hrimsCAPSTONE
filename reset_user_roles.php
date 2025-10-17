<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "<h2>Reset User Roles</h2>";

// Only proceed if confirmed
if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    try {
        // Update all users to have a default role if they don't have one
        $result = $usersCollection->updateMany(
            ['role' => ['$exists' => false]],
            ['$set' => ['role' => 'employee']]
        );
        
        echo "<p style='color: green;'>Updated " . $result->getModifiedCount() . " users with default role 'employee'.</p>";
        
        // Show current role distribution
        echo "<h3>Current Role Distribution:</h3>";
        $pipeline = [
            ['$group' => ['_id' => '$role', 'count' => ['$sum' => 1]]],
            ['$sort' => ['_id' => 1]]
        ];
        
        $roleStats = $usersCollection->aggregate($pipeline);
        
        echo "<ul>";
        foreach ($roleStats as $stat) {
            echo "<li><strong>" . htmlspecialchars($stat['_id']) . ":</strong> " . $stat['count'] . " users</li>";
        }
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>This script will ensure all users have a role assigned.</p>";
    echo "<p>Users without a role will be assigned the default 'employee' role.</p>";
    
    echo "<form method='POST'>";
    echo "<input type='hidden' name='confirm' value='yes'>";
    echo "<p><input type='submit' value='Confirm Reset User Roles' style='background-color: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'></p>";
    echo "</form>";
}

echo "<hr>";
echo "<p><a href='set_user_roles.php'>Manage User Roles</a> | <a href='index.php'>Back to Main Page</a></p>";
?>