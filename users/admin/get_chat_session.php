<?php
// get_chat_session.php - Retrieve a specific chat session from MongoDB
header('Content-Type: application/json');

try {
    // Get session ID from query parameter
    $sessionId = $_GET['session_id'] ?? null;
    
    if (!$sessionId) {
        throw new Exception("Session ID is required");
    }
    
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select chat sessions collection
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Find the specific session
    $session = $sessionsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($sessionId)]);
    
    if (!$session) {
        throw new Exception("Session not found");
    }
    
    // Add formatted timestamps to messages if they don't have them
    $messages = $session['messages'] ?? [];
    foreach ($messages as &$message) {
        if (!isset($message['timestamp'])) {
            // If no timestamp, use current time or derive from session creation time
            $message['timestamp'] = date('c');
        }
    }
    
    // Return the messages
    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode([]);
}
?>