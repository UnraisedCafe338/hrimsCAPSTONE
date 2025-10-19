<?php
include('../../handlers/connection.php');

header('Content-Type: application/json');

try {
    $notificationCollection = $database->selectCollection("notifications");
    $demoCollection = $database->selectCollection("teaching_demos");
    
    // DON'T get immediate notifications - only time-based reminders
    $notificationList = [];
    
    // Get upcoming demos (within 24 hours) - these will be our time-based notifications
    // Let's make this more inclusive to catch demos for today
    $startOfDay = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-d 00:00:00')) * 1000);
    $endOfDay = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-d 23:59:59')) * 1000);
    
    $demos = $demoCollection->find(
        [
            'demo_date' => [
                '$gte' => $startOfDay,
                '$lte' => $endOfDay
            ],
            'status' => 'scheduled'
        ],
        ['sort' => ['demo_date' => 1, 'demo_time' => 1], 'limit' => 20]
    );
    
    foreach ($demos as $demo) {
        // Check if demo is upcoming (within 1 hour)
        $demoDateTime = $demo->demo_date->toDateTime()->format('Y-m-d') . ' ' . $demo->demo_time;
        $demoTimestamp = strtotime($demoDateTime);
        $now = time();
        $timeDiff = $demoTimestamp - $now;
        
        // If demo is within 1 hour, mark as urgent
        $isUrgent = ($timeDiff > 0 && $timeDiff <= 3600);
        
        $timeText = '';
        if ($timeDiff > 0 && $timeDiff <= 3600) {
            $timeText = ' (Starting soon!)';
        } elseif ($timeDiff > 3600) {
            $hours = floor($timeDiff / 3600);
            $timeText = " (In $hours hours)";
        } elseif ($timeDiff <= 0 && $timeDiff > -3600) {
            $timeText = ' (Started)';
        }
        
        $notificationList[] = [
            'id' => (string)$demo->_id,
            'type' => 'demo_reminder',
            'title' => $isUrgent ? 'URGENT: Demo Reminder' : 'Demo Reminder',
            'message' => "Teaching demo for {$demo->applicant_name} on " . 
                         $demo->demo_date->toDateTime()->format('Y-m-d') . 
                         " at {$demo->demo_time} in {$demo->room}" . $timeText,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => false,
            'is_urgent' => $isUrgent,
            'demo_time' => $demo->demo_time // Include exact demo time for scheduling
        ];
    }
    
    // Sort all notifications by date
    usort($notificationList, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    // Limit to 15 most recent notifications
    $notificationList = array_slice($notificationList, 0, 15);
    
    echo json_encode([
        'success' => true,
        'notifications' => $notificationList,
        'count' => count($notificationList)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>