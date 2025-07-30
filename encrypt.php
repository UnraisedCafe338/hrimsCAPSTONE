<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->hrims_db;
$collection = $database->users;

$password = "admin123";
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$filter = ["username" => "admin_hr"];
$update = ['$set' => ["password" => $hashedPassword]];
$options = ["upsert" => true]; // Update if exists, insert if not

$updateResult = $collection->updateOne($filter, $update, $options);

if ($updateResult->getMatchedCount() > 0 || $updateResult->getUpsertedCount() > 0) {
    echo "User added/updated successfully!";
} else {
    echo "Error updating user.";
}

?>
