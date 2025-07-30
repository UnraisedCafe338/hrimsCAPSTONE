<?php
require 'vendor/autoload.php';

$mongo = new MongoDB\Client("mongodb://localhost:27017");
$db = $mongo->hrims;
$gridFS = $db->selectGridFSBucket();

if (!isset($_GET['id'])) {
    die("File ID is required.");
}

$fileId = new MongoDB\BSON\ObjectId($_GET['id']);
$stream = $gridFS->openDownloadStream($fileId);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"downloaded_file\"");

fpassthru($stream);
?>
