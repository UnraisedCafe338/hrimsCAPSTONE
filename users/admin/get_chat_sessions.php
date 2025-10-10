<?php
// get_chat_sessions.php - Retrieve chat sessions from MongoDB
header('Content-Type: application/json');

try {
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select chat sessions collection
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Retrieve last 10 chat sessions, sorted by most recent
    $sessions = $sessionsCollection->find(
        [], 
        [
            'sort' => ['created_at' => -1],
            'limit' => 10
        ]
    );
    
    $sessionList = [];
    foreach ($sessions as $session) {
        $sessionList[] = [
            '_id' => (string)$session['_id'],
            'title' => $session['title'] ?? 'Chat Session',
            'created_at' => $session['created_at'] ?? null
        ];
    }
    
    echo json_encode($sessionList);
} catch (Exception $e) {
    echo json_encode([]);
}
?>