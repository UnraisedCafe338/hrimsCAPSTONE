<?php
// Test chat sessions functionality
include('handlers/connection.php');

// Select chat sessions collection
$sessionsCollection = $database->selectCollection("ai_chat_sessions");

// Test 1: Create a new session
echo "<h2>Test 1: Creating a New Chat Session</h2>";

$testMessages = [
    [
        'sender' => 'user',
        'content' => 'Hello, how many employees do we have?',
        'timestamp' => date('c')
    ],
    [
        'sender' => 'ai',
        'content' => 'We have 25 employees in the database.',
        'timestamp' => date('c')
    ]
];

$currentTime = new MongoDB\BSON\UTCDateTime();

$sessionData = [
    'title' => 'Test Conversation - Employee Count',
    'messages' => $testMessages,
    'created_at' => $currentTime,
    'updated_at' => $currentTime
];

// Insert test session
$insertResult = $sessionsCollection->insertOne($sessionData);

if ($insertResult->getInsertedId()) {
    $sessionId = (string)$insertResult->getInsertedId();
    echo "<p style='color: green;'>✓ Session created successfully with ID: " . $sessionId . "</p>";
    
    // Test 2: Retrieve the session
    echo "<h2>Test 2: Retrieving the Chat Session</h2>";
    $retrievedSession = $sessionsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($sessionId)]);
    
    if ($retrievedSession) {
        echo "<p style='color: green;'>✓ Session retrieved successfully</p>";
        echo "<h3>Session Details:</h3>";
        echo "<ul>";
        echo "<li>Title: " . htmlspecialchars($retrievedSession['title']) . "</li>";
        echo "<li>Message Count: " . count($retrievedSession['messages']) . "</li>";
        echo "<li>Created At: " . ($retrievedSession['created_at'] ?? 'N/A') . "</li>";
        echo "<li>Updated At: " . ($retrievedSession['updated_at'] ?? 'N/A') . "</li>";
        echo "</ul>";
        
        // Test 3: Update the session
        echo "<h2>Test 3: Updating the Chat Session</h2>";
        $updatedTime = new MongoDB\BSON\UTCDateTime();
        $updateResult = $sessionsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($sessionId)],
            ['$set' => [
                'title' => 'Updated Test Conversation',
                'updated_at' => $updatedTime
            ]]
        );
        
        if ($updateResult->getModifiedCount() > 0) {
            echo "<p style='color: green;'>✓ Session updated successfully</p>";
            
            // Verify update
            $updatedSession = $sessionsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($sessionId)]);
            echo "<p>Updated Title: " . htmlspecialchars($updatedSession['title']) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to update session</p>";
        }
        
        // Test 4: List all sessions
        echo "<h2>Test 4: Listing All Chat Sessions</h2>";
        $allSessions = $sessionsCollection->find([], ['sort' => ['updated_at' => -1], 'limit' => 5]);
        $sessionCount = 0;
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Session ID</th><th>Title</th><th>Message Count</th><th>Created At</th><th>Updated At</th></tr>";
        
        foreach ($allSessions as $session) {
            $sessionCount++;
            echo "<tr>";
            echo "<td>" . (string)$session['_id'] . "</td>";
            echo "<td>" . htmlspecialchars($session['title'] ?? 'Untitled') . "</td>";
            echo "<td>" . count($session['messages'] ?? []) . "</td>";
            echo "<td>" . ($session['created_at'] ?? 'N/A') . "</td>";
            echo "<td>" . ($session['updated_at'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<p>Total sessions found: " . $sessionCount . "</p>";
        
        // Clean up - delete the test session
        $sessionsCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($sessionId)]);
        echo "<p style='color: blue;'>Test session cleaned up.</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to retrieve session</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to create test session</p>";
}

echo "<h2>Database Connection Test</h2>";
try {
    // Test database connection
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Try a simple operation
    $count = $sessionsCollection->countDocuments();
    echo "<p style='color: green;'>✓ Database connection successful. Total chat sessions: " . $count . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
?>