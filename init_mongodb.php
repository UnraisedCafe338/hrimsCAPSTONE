<?php
require_once __DIR__ . '/vendor/autoload.php';

echo "<h2>MongoDB Initialization</h2>";

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    
    echo "<p style='color: green;'>✓ Connected to MongoDB successfully!</p>";
    
    // Create collections if they don't exist
    $collectionsToCreate = ['users', 'employees', 'applicants', 'faqs', 'ai_chat_sessions'];
    
    foreach ($collectionsToCreate as $collectionName) {
        try {
            // Try to create the collection
            $database->createCollection($collectionName);
            echo "<p>Created collection: $collectionName</p>";
        } catch (Exception $e) {
            // Collection might already exist
            echo "<p>Collection $collectionName already exists or error: " . $e->getMessage() . "</p>";
        }
    }
    
    // Create indexes for the users collection
    $usersCollection = $database->selectCollection("users");
    
    try {
        // Create index on email field
        $usersCollection->createIndex(['email' => 1], ['unique' => true]);
        echo "<p>Created unique index on users.email</p>";
        
        // Create index on role field
        $usersCollection->createIndex(['role' => 1]);
        echo "<p>Created index on users.role</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>Database Setup Complete!</h3>";
    echo "<p>Your MongoDB database is ready for the HRIMS system.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Main Page</a></p>";
?>