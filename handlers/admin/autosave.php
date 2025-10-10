<?php
session_start();

// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

use MongoDB\Client;

// Check required POST data
if (!isset($_POST['field'], $_POST['value'])) {
    http_response_code(400);
    echo 'Missing field or value';
    exit;
}

$field = $_POST['field'];
$value = $_POST['value'];

// Use a unique session-based identifier (replace with applicant ID if available)
$applicantId = $_SESSION['applicant_id'] ?? session_id();

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$collection = $client->hrims_db->applicants_answers;

// Update or insert
$collection->updateOne(
    ['applicant_id' => $applicantId],
    ['$set' => [
        'applicant_id' => $applicantId,
        "answers.$field" => $value,
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ]],
    ['upsert' => true]
);

http_response_code(200);
echo 'Saved';
