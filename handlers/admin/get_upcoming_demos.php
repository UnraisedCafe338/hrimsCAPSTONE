<?php
include('../../handlers/connection.php');

header('Content-Type: application/json');

try {
    $demoCollection = $database->selectCollection("teaching_demos");
    
    // Get demos scheduled for today and tomorrow
    $startOfDay = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-d 00:00:00')) * 1000);
    $endOfTomorrow = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-d 00:00:00', strtotime('+2 days'))) * 1000);
    
    $demos = $demoCollection->find(
        [
            'demo_date' => [
                '$gte' => $startOfDay,
                '$lt' => $endOfTomorrow
            ],
            'status' => 'scheduled'
        ],
        ['sort' => ['demo_date' => 1, 'demo_time' => 1], 'limit' => 10]
    );
    
    $demoList = [];
    foreach ($demos as $demo) {
        // Check if demo is upcoming (within 1 hour)
        $demoDateTime = $demo->demo_date->toDateTime()->format('Y-m-d') . ' ' . $demo->demo_time;
        $demoTimestamp = strtotime($demoDateTime);
        $now = time();
        $timeDiff = $demoTimestamp - $now;
        
        // If demo is within 1 hour, mark as urgent
        $isUrgent = ($timeDiff > 0 && $timeDiff <= 3600);
        
        $demoList[] = [
            'id' => (string)$demo->_id,
            'applicant_name' => $demo->applicant_name,
            'demo_date' => $demo->demo_date->toDateTime()->format('Y-m-d'),
            'demo_time' => $demo->demo_time,
            'room' => $demo->room,
            'topic' => $demo->topic,
            'is_urgent' => $isUrgent
        ];
    }
    
    echo json_encode([
        'success' => true,
        'demos' => $demoList,
        'count' => count($demoList)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>