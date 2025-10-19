<?php
include('../../handlers/connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the demo ID from query parameters
$demoId = $_GET['demo_id'] ?? null;

if (!$demoId) {
    echo json_encode(['success' => false, 'message' => 'Demo ID is required']);
    exit;
}

try {
    // Get collections
    $demoCollection = $database->selectCollection("teaching_demos");
    
    // Validate ObjectId format
    try {
        $objectId = new MongoDB\BSON\ObjectID($demoId);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Invalid demo ID format']);
        exit;
    }
    
    // Find the demo
    $demo = $demoCollection->findOne(['_id' => $objectId]);
    
    if (!$demo) {
        echo json_encode(['success' => false, 'message' => 'Teaching demo not found']);
        exit;
    }
    
    // Check if demo has been evaluated
    if (!isset($demo->evaluation) || $demo->status !== 'evaluated') {
        echo json_encode(['success' => false, 'message' => 'This demo has not been evaluated yet']);
        exit;
    }
    
    // Prepare evaluation data
    $evaluationData = [
        'presentation' => $demo->evaluation->presentation ?? null,
        'personality' => $demo->evaluation->personality ?? null,
        'voice_quality' => $demo->evaluation->voice_quality ?? null,
        'technical_knowledge' => $demo->evaluation->technical_knowledge ?? null,
        'resourcefulness' => $demo->evaluation->resourcefulness ?? null,
        'class_management' => $demo->evaluation->class_management ?? null,
        'teaching_ability' => $demo->evaluation->teaching_ability ?? null,
        'communication_skills' => $demo->evaluation->communication_skills ?? null,
        'time_management' => $demo->evaluation->time_management ?? null,
        'human_relation' => $demo->evaluation->human_relation ?? null,
        'overall_rating' => $demo->evaluation->overall_rating ?? null,
        'recommendation' => $demo->evaluation->recommendation ?? null,
        'evaluated_at' => null
    ];
    
    // Handle evaluation date
    if (isset($demo->evaluation->evaluated_at)) {
        if (is_object($demo->evaluation->evaluated_at) && method_exists($demo->evaluation->evaluated_at, 'toDateTime')) {
            $evaluationData['evaluated_at'] = $demo->evaluation->evaluated_at->toDateTime()->format('F j, Y H:i');
        } else {
            $evaluationData['evaluated_at'] = date('F j, Y H:i', strtotime($demo->evaluation->evaluated_at));
        }
    }
    
    echo json_encode([
        'success' => true,
        'evaluation' => $evaluationData,
        'applicant_name' => $demo->applicant_name
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>