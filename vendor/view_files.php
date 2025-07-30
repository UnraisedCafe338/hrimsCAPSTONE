<?php
require 'vendor/autoload.php';

$mongo = new MongoDB\Client("mongodb://localhost:27017");
$db = $mongo->hrims;
$files = $db->fs.files->find();

echo "<h2>Uploaded Files</h2>";

foreach ($files as $file) {
    echo "<p>
        <strong>Category:</strong> " . ($file->metadata->type ?? 'Unknown') . "<br>
        <strong>File Name:</strong> " . $file->filename . "<br>
        <a href='download.php?id=" . $file->_id . "'>Download</a>
    </p>";
}
?>
