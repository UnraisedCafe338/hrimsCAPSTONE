<?php
require '../vendor/autoload.php'; // adjust path if needed

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->hrims_db; // replace with your DB name
$gridFS = $db->selectGridFSBucket();

if (isset($_GET['id']) && $_GET['id'] !== '') {
    $fileId = new MongoDB\BSON\ObjectId($_GET['id']);

    header('Content-Type: application/octet-stream');

    try {
        $stream = $gridFS->openDownloadStream($fileId);
        $metadata = $db->selectCollection('fs.files')->findOne(['_id' => $fileId]);

        if ($metadata && isset($metadata->metadata->mimeType)) {
            header('Content-Type: ' . $metadata->metadata->mimeType);
        }

        if ($metadata && isset($metadata->metadata->originalName)) {
            header('Content-Disposition: attachment; filename="' . $metadata->metadata->originalName . '"');
        }

        fpassthru($stream);
        fclose($stream);
    } catch (Exception $e) {
        http_response_code(404);
        echo "File not found.";
    }
} else {
    http_response_code(400);
    echo "Invalid request.";
}
?>
