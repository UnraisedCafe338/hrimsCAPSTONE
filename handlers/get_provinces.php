<?php
require '../vendor/autoload.php';

$regionId = isset($_GET['region_id']) ? (int) $_GET['region_id'] : null;

require '../connection.php'; // Database connection

$collection = $client->hrims_db->provinces;

$provinces = $collection->find(['region_id' => $regionId], ['sort' => ['name' => 1]]);

$result = [];
foreach ($provinces as $prov) {
    $result[] = [
        'id' => $prov['id'],
        'name' => $prov['name'],
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
