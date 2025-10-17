<?php
// get_chat_sessions.php - Retrieve chat sessions from MongoDB
header('Content-Type: application/json');

try {
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select chat sessions collection
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Retrieve last 20 chat sessions, sorted by most recent
    $sessions = $sessionsCollection->find(
        [], 
        [
            'sort' => ['updated_at' => -1, 'created_at' => -1], // Sort by updated time first, then created time
            'limit' => 20
        ]
    );
    
    $sessionList = [];
    
    foreach ($sessions as $session) {
        $title = $session['title'] ?? 'Chat Session';
        $sessionId = (string)$session['_id'];
        
        // Handle timestamps - prefer updated_at, fallback to created_at
        $timestamp = null;
        if (isset($session['updated_at'])) {
            $timestamp = $session['updated_at'];
        } else if (isset($session['created_at'])) {
            $timestamp = $session['created_at'];
        }
        
        $sessionList[] = [
            '_id' => $sessionId,
            'title' => $title,
            'created_at' => $timestamp
        ];
    }
    
    echo json_encode($sessionList);
} catch (Exception $e) {
    echo json_encode([]);
}
?>