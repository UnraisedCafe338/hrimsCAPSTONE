<?php
require '../vendor/autoload.php'; // MongoDB PHP Driver
require '../connection.php'; // Your DB connection

$filename = $_GET['file'];
$filepath = "../uploads/$filename";

if (!file_exists($filepath)) {
    die("File not found.");
}

$gridFS = $database->selectGridFSBucket();
$stream = fopen($filepath, 'rb');

$gridFS->uploadFromStream($filename, $stream, [
    'metadata' => [
        'uploaded_by' => 'hr_staff',
        'type' => 'scanned_document',
        'uploaded_at' => new MongoDB\BSON\UTCDateTime()
    ]
]);

fclose($stream);
echo "Upload successful!";
