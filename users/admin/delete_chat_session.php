<?php
// delete_chat_session.php - Delete a chat session from MongoDB
header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (!$data || empty($data['session_id'])) {
        throw new Exception('session_id is required');
    }

    $sessionId = $data['session_id'];

    require_once '../../handlers/connection.php';

    $sessionsCollection = $database->selectCollection('ai_chat_sessions');

    if (!preg_match('/^[0-9a-fA-F]{24}$/', $sessionId)) {
        throw new Exception('invalid session id');
    }

    $deleteResult = $sessionsCollection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($sessionId)]);

    if ($deleteResult->getDeletedCount() > 0) {
        echo json_encode(['status' => 'deleted', 'session_id' => $sessionId]);
    } else {
        echo json_encode(['status' => 'not_found']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
