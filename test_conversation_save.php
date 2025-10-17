<?php
// Test conversation saving functionality
include('handlers/connection.php');

// Select chat sessions collection
$sessionsCollection = $database->selectCollection("ai_chat_sessions");

// Create a test conversation
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

$sessionData = [
    'title' => 'Test Conversation',
    'messages' => $testMessages,
    'created_at' => new MongoDB\BSON\UTCDateTime(),
    'updated_at' => new MongoDB\BSON\UTCDateTime()
];

// Insert test session
$insertResult = $sessionsCollection->insertOne($sessionData);

if ($insertResult->getInsertedId()) {
    echo "<h2>Test Conversation Saved Successfully</h2>";
    echo "<p>Session ID: " . $insertResult->getInsertedId() . "</p>";
    
    // Retrieve and display the saved session
    $savedSession = $sessionsCollection->findOne(['_id' => $insertResult->getInsertedId()]);
    
    echo "<h3>Saved Session Data:</h3>";
    echo "<pre>" . print_r($savedSession, true) . "</pre>";
    
    // Clean up - delete the test session
    $sessionsCollection->deleteOne(['_id' => $insertResult->getInsertedId()]);
    echo "<p>Test session cleaned up.</p>";
} else {
    echo "<h2>Error Saving Test Conversation</h2>";
    echo "<p>Failed to save test conversation.</p>";
}
?>