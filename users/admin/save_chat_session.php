<?php
// save_chat_session.php - Save a chat session to MongoDB
header('Content-Type: application/json');

try {
    // Get JSON data from request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception("Invalid JSON data");
    }
    
    $sessionId = $data['session_id'] ?? null;
    $messages = $data['messages'] ?? [];
    $title = $data['title'] ?? 'Chat Session';
    
    if (!$sessionId || !is_array($messages)) {
        throw new Exception("Session ID and messages are required");
    }
    
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select chat sessions collection
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Prepare session data
    $sessionData = [
        'title' => $title,
        'messages' => $messages,
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    // If this is a new session (not an existing ObjectId), create a new document
    if (preg_match('/^[0-9a-fA-F]{24}$/', $sessionId)) {
        // Update existing session
        $sessionsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($sessionId)],
            ['$set' => $sessionData]
        );
        $result = ['status' => 'updated', 'session_id' => $sessionId];
    } else {
        // Create new session
        $sessionData['created_at'] = new MongoDB\BSON\UTCDateTime();
        $insertResult = $sessionsCollection->insertOne($sessionData);
        $result = ['status' => 'created', 'session_id' => (string)$insertResult->getInsertedId()];
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>