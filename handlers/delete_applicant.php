<?php
require '../connection.php'; // Database connection

$collection = $client->hrims_db->applicants; // Replace accordingly

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    try {
        $deleteResult = $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if ($deleteResult->getDeletedCount() === 1) {
            header("Location: ../main/applicants.php?deleted=1");
            exit();
        } else {
            echo "Failed to delete applicant.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
