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

    // Fetch the file metadata from the fs.files collection
    $fileDoc = $database->selectCollection("$bucketName.files")->findOne([
        '_id' => $objectId
    ]);

    if (!$fileDoc) {
        throw new Exception('File not found in database.');
    }

    // Get the filename and content type (mime type)
    $filename = $fileDoc['filename'] ?? 'file';
    $contentType = $fileDoc['metadata']['mimeType'] ?? 'application/octet-stream';

    // Open the download stream for the file
    $stream = $gridFS->openDownloadStream($objectId);

    // Set headers for image display
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: inline; filename="' . $filename . '"');  // Use inline to display the image
    header('Content-Length: ' . $fileDoc['length']);

    // Output the file content to the browser
    fpassthru($stream);
    exit;

} catch (Exception $e) {
    echo 'Error loading file: ' . htmlspecialchars($e->getMessage());
}
?>
