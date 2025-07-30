<?php
require '../vendor/autoload.php';

require '../connection.php'; // MongoDB connection

$collection = $client->hrims_db->municipalities;

// Determine query condition
$provinceId = isset($_GET['province_id']) ? (int) $_GET['province_id'] : null;
$regionId = isset($_GET['region_id']) ? (int) $_GET['region_id'] : null;

$query = [];

if ($provinceId !== null) {
    $query['province_id'] = $provinceId;
} elseif ($regionId !== null) {
    $query['region_id'] = $regionId;
}


// Execute MongoDB query
$municipalities = $collection->find($query, ['sort' => ['name' => 1]]);

$result = [];
foreach ($municipalities as $mun) {
    $result[] = [
        'id' => $mun['id'],
        'name' => $mun['name'],
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
