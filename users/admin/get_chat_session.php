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
    
    // Return the messages
    echo json_encode($session['messages'] ?? []);
} catch (Exception $e) {
    echo json_encode([]);
}
?>