<?php
require_once '../vendor/autoload.php';

if (!isset($_GET['id'])) {
    die('No file ID provided');
}

$fileId = $_GET['id'];

// Validate ObjectId format manually
if (!preg_match('/^[a-f\d]{24}$/i', $fileId)) {
    die('Invalid ObjectId');
}

try {
    $client = new MongoDB\Client;
    $database = $client->hrims_db;
    $gridFS = $database->selectGridFSBucket(); // default is "fs"
    $bucketName = $gridFS->getBucketName();

    $objectId = new MongoDB\BSON\ObjectId($fileId);

    $fileDoc = $database->selectCollection("$bucketName.files")->findOne([
        '_id' => $objectId
    ]);

    if (!$fileDoc) {
        throw new Exception('File not found in database.');
    }

    $filename = $fileDoc['filename'] ?? 'file';
    $contentType = $fileDoc['metadata']['mimeType'] ?? 'application/octet-stream';

    $stream = $gridFS->openDownloadStream($objectId);

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $fileDoc['length']);

    fpassthru($stream);
    exit;

} catch (Exception $e) {
    echo 'Error loading file: ' . htmlspecialchars($e->getMessage());
}
?>
