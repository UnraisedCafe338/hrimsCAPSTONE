<?php
include('../../handlers/connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the POST data
$data = $_POST;

// Validate required fields (excluding overall_rating since we'll calculate it)
$required_fields = ['demo_id', 'presentation', 'personality', 'voice_quality', 'technical_knowledge', 
                   'resourcefulness', 'class_management', 'teaching_ability', 'communication_skills', 
                   'time_management', 'human_relation', 'recommendation'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    // Get collections
    $demoCollection = $database->selectCollection("teaching_demos");
    
    // Verify demo exists
    $demoId = $data['demo_id'];
    
    // Validate ObjectId format
    try {
        $objectId = new MongoDB\BSON\ObjectID($demoId);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Invalid demo ID format']);
        exit;
    }
    
    $demo = $demoCollection->findOne(['_id' => $objectId]);
    
    if (!$demo) {
        echo json_encode(['success' => false, 'message' => 'Teaching demo not found']);
        exit;
    }
    
    // Validate ratings are within range
    $rating_fields = ['presentation', 'personality', 'voice_quality', 'technical_knowledge', 
                     'resourcefulness', 'class_management', 'teaching_ability', 'communication_skills', 
                     'time_management', 'human_relation'];
    
    // Collect rating values for validation and calculation
    $ratings = [];
    foreach ($rating_fields as $field) {
        // Convert to integer and validate
        $value = filter_var($data[$field], FILTER_VALIDATE_INT);
        if ($value === false || $value < 70 || $value > 95) {
            echo json_encode(['success' => false, 'message' => "Rating for $field must be a valid integer between 70 and 95"]);
            exit;
        }
        $ratings[$field] = $value;
    }
    
    // Calculate overall rating as average of all individual ratings
    $sum = array_sum($ratings);
    $count = count($ratings);
    $overall_rating = ($count > 0) ? round($sum / $count) : 0;
    
    // Prepare evaluation data
    $evaluationData = [
        'presentation' => $ratings['presentation'],
        'personality' => $ratings['personality'],
        'voice_quality' => $ratings['voice_quality'],
        'technical_knowledge' => $ratings['technical_knowledge'],
        'resourcefulness' => $ratings['resourcefulness'],
        'class_management' => $ratings['class_management'],
        'teaching_ability' => $ratings['teaching_ability'],
        'communication_skills' => $ratings['communication_skills'],
        'time_management' => $ratings['time_management'],
        'human_relation' => $ratings['human_relation'],
        'overall_rating' => $overall_rating,
        'recommendation' => $data['recommendation'],
        'evaluated_at' => new MongoDB\BSON\UTCDateTime(),
        'evaluator' => 'Admin' // In a real implementation, this would be the logged in user
    ];
    
    // Update demo with evaluation data and mark as evaluated
    $updateData = [
        'evaluation' => $evaluationData,
        'status' => 'evaluated',
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $result = $demoCollection->updateOne(
        ['_id' => $objectId],
        ['$set' => $updateData]
    );
    
    if ($result->getModifiedCount() === 1) {
        echo json_encode([
            'success' => true, 
            'message' => 'Teaching demo evaluation saved successfully',
            'overall_rating' => $overall_rating
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save evaluation']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>