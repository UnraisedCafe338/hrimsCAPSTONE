<?php
require 'vendor/autoload.php';
use MongoDB\BSON\ObjectId;
try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db"); 
    $usersCollection = $database->selectCollection("users"); 
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
} 
?>

