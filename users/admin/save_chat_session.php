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
    
    if (!is_array($messages)) {
        throw new Exception("Messages must be an array");
    }
    
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select chat sessions collection
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Current timestamp
    $currentTime = new MongoDB\BSON\UTCDateTime();
    
    // Prepare session data
    $sessionData = [
        'title' => $title,
        'messages' => array_values($messages), // Ensure array is reindexed
        'updated_at' => $currentTime
    ];
    
    // Handle new chat creation vs existing chat update
    if (!$sessionId || $sessionId === 'new' || !preg_match('/^[0-9a-fA-F]{24}$/', $sessionId)) {
        // Create new session
        $sessionData['created_at'] = $currentTime;
        $insertResult = $sessionsCollection->insertOne($sessionData);
        $result = ['status' => 'created', 'session_id' => (string)$insertResult->getInsertedId()];
    } else {
        // Update existing session
        $updateResult = $sessionsCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($sessionId)],
            ['$set' => $sessionData]
        );
        
        if ($updateResult->getMatchedCount() > 0) {
            $result = ['status' => 'updated', 'session_id' => $sessionId];
        } else {
            // If session not found, create new one
            $sessionData['created_at'] = $currentTime;
            $insertResult = $sessionsCollection->insertOne($sessionData);
            $result = ['status' => 'created', 'session_id' => (string)$insertResult->getInsertedId()];
        }
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>