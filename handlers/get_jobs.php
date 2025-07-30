<?php
header('Content-Type: application/json');

try {
    require '../vendor/autoload.php'; // include Composer's autoloader

    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->hrims_db->job_positions;

$cursor = $collection->find([], [
    'projection' => [
        'position_title' => 1,
        'available_slots' => 1,
        '_id' => 0
    ]
]);
$jobs = iterator_to_array($cursor);

header('Content-Type: application/json');
echo json_encode($jobs);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
