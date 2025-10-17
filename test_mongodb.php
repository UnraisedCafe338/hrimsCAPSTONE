<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

echo "<h2>MongoDB Connection Test</h2>";

try {
    // Test the connection by getting database info
    $databaseInfo = $database->command(['ping' => 1])->toArray();
    
    if ($databaseInfo[0]['ok'] == 1) {
        echo "<p style='color: green;'>✓ Successfully connected to MongoDB!</p>";
    } else {
        echo "<p style='color: red;'>✗ MongoDB ping failed</p>";
        exit;
    }
    
    // Show database name
    echo "<p><strong>Database:</strong> " . $database->getDatabaseName() . "</p>";
    
    // Show collections
    echo "<h3>Collections in Database:</h3>";
    $collections = $database->listCollections();
    echo "<ul>";
    foreach ($collections as $collection) {
        // Get document count for each collection
        $count = $database->selectCollection($collection->getName())->countDocuments();
        echo "<li>" . $collection->getName() . " (" . $count . " documents)</li>";
    }
    echo "</ul>";
    
    // Show users collection info
    echo "<h3>Users Collection Info:</h3>";
    $usersCount = $usersCollection->countDocuments();
    echo "<p>Total users: " . $usersCount . "</p>";
    
    // Show sample users
    if ($usersCount > 0) {
        echo "<h4>Sample Users:</h4>";
        $sampleUsers = $usersCollection->find([], ['limit' => 5, 'sort' => ['_id' => -1]]);
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Email</th><th>Role</th><th>Name</th></tr>";
        foreach ($sampleUsers as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['role'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Main Page</a></p>";
?>