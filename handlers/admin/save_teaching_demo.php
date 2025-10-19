<?php
include('../../handlers/connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the POST data - handle both JSON and form data
$data = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    // Handle JSON data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
} else {
    // Handle form data
    $data = $_POST;
}

// Validate required fields
$required_fields = ['applicant_id', 'demo_date', 'demo_time', 'duration', 'room', 'topic', 'area_of_specialization'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    // Get collections
    $demoCollection = $database->selectCollection("teaching_demos");
    $applicantCollection = $database->selectCollection("applicants");
    
    // Verify applicant exists
    $applicantId = $data['applicant_id'];
    
    // Validate ObjectId format
    try {
        $objectId = new MongoDB\BSON\ObjectID($applicantId);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Invalid applicant ID format']);
        exit;
    }
    
    $applicant = $applicantCollection->findOne(['_id' => $objectId]);
    
    if (!$applicant) {
        echo json_encode(['success' => false, 'message' => 'Applicant not found']);
        exit;
    }
    
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $data['demo_date']);
    if (!$date || $date->format('Y-m-d') !== $data['demo_date']) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit;
    }
    
    // Validate time format
    $timeParts = explode(':', $data['demo_time']);
    if (count($timeParts) !== 2 || !is_numeric($timeParts[0]) || !is_numeric($timeParts[1]) ||
        $timeParts[0] < 0 || $timeParts[0] > 23 || $timeParts[1] < 0 || $timeParts[1] > 59) {
        echo json_encode(['success' => false, 'message' => 'Invalid time format']);
        exit;
    }
    
    // Ensure duration is an integer
    $duration = (int)$data['duration'];
    
    // Validate duration is a positive number
    if ($duration <= 0) {
        echo json_encode(['success' => false, 'message' => 'Duration must be a positive number']);
        exit;
    }
    
    // Prepare demo data
    $demoData = [
        'applicant_id' => $objectId,
        'applicant_name' => ($applicant['personal_info']['first_name'] ?? '') . ' ' . ($applicant['personal_info']['last_name'] ?? ''),
        'demo_date' => $data['demo_date'],
        'demo_time' => $data['demo_time'],
        'duration' => $duration,
        'room' => $data['room'],
        'topic' => $data['topic'],
        'area_of_specialization' => $data['area_of_specialization'],
        'license' => $data['license'] ?? '',
        'materials' => $data['materials'] ?? '',
        'notes' => $data['notes'] ?? '',
        'status' => 'scheduled',
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    // Insert demo data
    $result = $demoCollection->insertOne($demoData);
    
    if ($result->getInsertedCount() === 1) {
        // DON'T create an immediate notification - only time-based notifications
        echo json_encode([
            'success' => true, 
            'message' => 'Teaching demo scheduled successfully',
            'demo_id' => (string)$result->getInsertedId()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to schedule demo']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>