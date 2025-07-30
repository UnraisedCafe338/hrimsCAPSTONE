<?php
require '../vendor/autoload.php'; // MongoDB autoload
require '../connection.php'; // Database connection

use MongoDB\Client;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["applicant_id"])) {
    $applicantId = $_POST["applicant_id"];
    
    // Select collections
    $applicantsCollection = $database->selectCollection("applicants");
    $employeesCollection = $database->selectCollection("employees");
    
    // Find the applicant
    $applicant = $applicantsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($applicantId)]);
    
    if ($applicant) {
        // Move the record to employees collection
        $insertResult = $employeesCollection->insertOne($applicant);
        
        if ($insertResult->getInsertedCount() > 0) {
            // Delete from applicants collection
            $applicantsCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($applicantId)]);
            echo json_encode(["status" => "success", "message" => "Applicant hired successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to hire applicant."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Applicant not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
