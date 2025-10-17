<?php
// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

$municipalityId = isset($_GET['municipality_id']) ? (int) $_GET['municipality_id'] : null;

require '../connection.php'; // Database connection

$collection = $client->hrims_db->barangays;

$barangays = $collection->find(['city_municipality_id' => $municipalityId], ['sort' => ['name' => 1]]);

$result = [];
foreach ($barangays as $bgy) {
    $result[] = [
        'id' => $bgy['id'],
        'name' => $bgy['name'],
    ];
}

header('Content-Type: application/json');
echo json_encode($result);