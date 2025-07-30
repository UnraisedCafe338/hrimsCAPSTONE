<?php
require '../vendor/autoload.php';

$provinceId = isset($_GET['municipality_id']) ? (int) $_GET['municipality_id'] : null;

require '../connection.php'; // Database connection

$collection = $client->hrims_db->barangays;

$barangays = $collection->find(['city_municipality_id' => $provinceId], ['sort' => ['name' => 1]]);

$result = [];
foreach ($barangays as $bgy) {
    $result[] = [
        'id' => $bgy['id'],
        'name' => $bgy['name'],
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
