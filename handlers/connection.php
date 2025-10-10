<?php
// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}
use MongoDB\BSON\ObjectId;
try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db"); 
    $usersCollection = $database->selectCollection("users"); 
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
} 
?>

