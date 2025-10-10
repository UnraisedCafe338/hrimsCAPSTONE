<?php
// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}
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
