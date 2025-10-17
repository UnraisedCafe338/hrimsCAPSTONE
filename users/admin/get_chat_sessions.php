<?php
// get_chat_sessions.php - Retrieve chat sessions from MongoDB
header('Content-Type: application/json');

try {
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select chat sessions collection
    $sessionsCollection = $database->selectCollection("ai_chat_sessions");
    
    // Retrieve last 20 chat sessions, sorted by most recent (increased from 10 to 20)
    $sessions = $sessionsCollection->find(
        [], 
        [
            'sort' => ['created_at' => -1],
            'limit' => 20  // Increased limit
        ]
    );
    
    $sessionList = [];
    $seenTitles = []; // To prevent duplicates
    
    foreach ($sessions as $session) {
        $title = $session['title'] ?? 'Chat Session';
        $sessionId = (string)$session['_id'];
        
        // Create a unique key to prevent duplicates
        $uniqueKey = $title . '_' . $sessionId;
        
        // Skip if we've already seen this session
        if (in_array($uniqueKey, $seenTitles)) {
            continue;
        }
        
        $seenTitles[] = $uniqueKey;
        
        $sessionList[] = [
            '_id' => $sessionId,
            'title' => $title,
            'created_at' => $session['created_at'] ?? null
        ];
    }
    
    echo json_encode($sessionList);
} catch (Exception $e) {
    echo json_encode([]);
}
?>