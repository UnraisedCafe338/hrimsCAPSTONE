<?php
require '../vendor/autoload.php'; // adjust path if needed
require '../connection.php'; // Database connection

$collection = $client->hrims_db->region;

$regions = $collection->find([], ['sort' => ['name' => 1]]);

$result = [];
foreach ($regions as $region) {
    $result[] = [
        'id' => $region['id'],
        'name' => $region['name'],
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
