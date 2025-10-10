<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}
require '../connection.php';

$collection = $database->selectCollection("employee");

// Optional department filter
$department = isset($_GET['department']) ? trim($_GET['department']) : '';
$baseFilter = [];
if ($department !== '') {
    $baseFilter['department'] = $department;
}

// Distinct list of departments for the dropdown
$departments = [];
try {
    $departments = $collection->distinct('department');
    // Normalize and sort
    $departments = array_values(array_filter(array_map(function($d) { return is_string($d) ? trim($d) : ''; }, $departments), function($d) { return $d !== ''; }));
    sort($departments, SORT_NATURAL | SORT_FLAG_CASE);
} catch (Exception $e) {
    $departments = [];
}

$totalEmployees = $collection->countDocuments($baseFilter);

// Compute newly hired for the current month (based on date_hired in MM/DD/YYYY)
$now = new DateTime('now');
$currentMonth = (int)$now->format('n');
$currentYear = (int)$now->format('Y');

$newlyHired = 0;
$newlyHiredPipeline = [
    [
        '$addFields' => [
            'parsedDate' => [
                '$dateFromString' => [
                    'dateString' => '$date_hired',
                    'format' => "%m/%d/%Y",
                    'onError' => null,
                    'onNull' => null
                ]
            ]
        ]
    ],
    [
        '$match' => [
            '$expr' => [
                '$and' => [
                    ['$eq' => [ ['$month' => '$parsedDate'], $currentMonth ]],
                    ['$eq' => [ ['$year' => '$parsedDate'], $currentYear ]]
                ]
            ]
        ]
    ],
    [ '$count' => 'count' ]
];

// If filtering by department, inject into $match
if (!empty($baseFilter)) {
    // Merge department filter into the second stage $match (outside $expr)
    $newlyHiredPipeline[1]['$match'] = array_merge($newlyHiredPipeline[1]['$match'], $baseFilter);
}

$newlyHiredCursor = $collection->aggregate($newlyHiredPipeline);

foreach ($newlyHiredCursor as $doc) {
    $newlyHired = (int)($doc['count'] ?? 0);
}

$aggregation = [
    [
        '$addFields' => [
            'parsedDate' => [
                '$dateFromString' => [
                    'dateString' => '$date_hired',
                    'format' => "%m/%d/%Y",
                    'onError' => null,
                    'onNull' => null
                ]
            ]
        ]
    ],
    // Optional department filter stage
    !empty($baseFilter) ? [ '$match' => $baseFilter ] : null,
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
];

// Remove null entries from pipeline (when no filter)
$aggregation = array_values(array_filter($aggregation));

$cursor = $collection->aggregate($aggregation);

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
$teachingFilter = array_merge($baseFilter, ['faculty_type' => 'Teaching']);
$nonTeachingFilter = array_merge($baseFilter, ['faculty_type' => 'Non-teaching']);
$teaching = $collection->countDocuments($teachingFilter);
$nonTeaching = $collection->countDocuments($nonTeachingFilter);

// 3. Teaching full-time vs part-time
$teachingFullTime = $collection->countDocuments(array_merge($baseFilter, [
    'faculty_type' => 'Teaching',
    'employment_type' => 'Full-time'
]));

$teachingPartTime = $collection->countDocuments(array_merge($baseFilter, [
    'faculty_type' => 'Teaching',
    'employment_type' => 'Part-time'
]));

// Output results as JSON
echo json_encode([
    'totalEmployees' => $totalEmployees,
    'newlyHired' => $newlyHired,
    'departments' => $departments,
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
