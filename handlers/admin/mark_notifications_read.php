<?php
include('../../handlers/connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $notificationCollection = $database->selectCollection("notifications");
    
    // Mark all admin notifications as read
    $result = $notificationCollection->updateMany(
        ['recipient_role' => 'admin', 'is_read' => false],
        ['$set' => ['is_read' => true, 'updated_at' => new MongoDB\BSON\UTCDateTime()]]
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Notifications marked as read',
        'modified_count' => $result->getModifiedCount()
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>