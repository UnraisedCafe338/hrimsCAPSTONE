<?php
// Enable error reporting for debugging (optional but helpful)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php'; // MongoDB library
require '../connection.php';

$collection = $database->selectCollection("employees"); 

// 1. Yearly employment breakdown based on root-level date_hired (format: MM/DD/YYYY)
$cursor = $collection->aggregate([
    [
        '$addFields' => [
            'parsedDate' => [
                '$dateFromString' => [
                    'dateString' => '$date_hired',
                    'format' => "%m/%d/%Y"
                ]
            ]
        ]
    ],
    [
        '$project' => [
            'year' => ['$year' => '$parsedDate'],
            'employment_type' => 1
        ]
    ],
    [
        '$group' => [
            '_id' => [
                'year' => '$year',
                'type' => '$employment_type'
            ],
            'count' => ['$sum' => 1]
        ]
    ]
]);

$yearlyStats = [];
foreach ($cursor as $row) {
    $year = $row->_id['year'] ?? 'unknown';
    $type = strtolower($row->_id['type'] ?? 'unknown');

    if (!isset($yearlyStats[$year])) {
        $yearlyStats[$year] = [];
    }

    if (!isset($yearlyStats[$year][$type])) {
        $yearlyStats[$year][$type] = 0;
    }

    $yearlyStats[$year][$type] += $row->count;
}

// 2. Teaching vs Non-Teaching (using faculty_type at root)
$teaching = $collection->countDocuments(['faculty_type' => 'Teaching']);
$nonTeaching = $collection->countDocuments(['faculty_type' => 'Non-teaching']);

// 3. Teaching full-time vs part-time
$teachingFullTime = $collection->countDocuments([
    'faculty_type' => 'Teaching',
    'employment_type' => 'Full-time'
]);

$teachingPartTime = $collection->countDocuments([
    'faculty_type' => 'Teaching',
    'employment_type' => 'Part-time'
]);

// Output results as JSON
echo json_encode([
    'yearlyStats' => $yearlyStats,
    'teachingStats' => [
        'teaching' => $teaching,
        'non_teaching' => $nonTeaching
    ],
    'teachingType' => [
        'full_time' => $teachingFullTime,
        'part_time' => $teachingPartTime
    ]
]);
?>
