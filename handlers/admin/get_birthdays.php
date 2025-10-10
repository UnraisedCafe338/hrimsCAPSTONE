<?php
header('Content-Type: application/json');

// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}
require '../connection.php';

$collection = $database->selectCollection("employee");

$currentMonth = (int)date('m');
$employees = $collection->find([
    'personal_info.birth_date' => ['$exists' => true]
]);

$birthdayList = [];

foreach ($employees as $emp) {
    $birthDate = $emp['personal_info']['birth_date'] ?? '';
    if (!$birthDate) continue;

    $month = (int)date('m', strtotime($birthDate));
    if ($month === $currentMonth) {
        $birthdayList[] = [
            'name' => $emp['personal_info']['first_name'] . ' ' . $emp['personal_info']['last_name'],
            'birth_date' => $birthDate,
            'photo' => $emp['personal_info']['photo_id'] ?: null  // This line fetches the photo_id
        ];
    }
}

echo json_encode($birthdayList);
?>  