<?php
require '../vendor/autoload.php';

$mongo = new MongoDB\Client("mongodb://localhost:27017");
$db = $mongo->hrims_db;

$applicantsCollection = $db->applicants;
$employeeCollection = $db->employee;
$status = $_POST['status'];
if (!isset($_POST['applicant_id'])) {
    die("No applicant ID received.");
}

$applicantId = $_POST['applicant_id'];
echo "Applicant ID received: " . htmlspecialchars($applicantId) . "<br>";

try {
    $objectId = new MongoDB\BSON\ObjectId($applicantId);
    
    // DEBUG: check all current applicant IDs
    echo "Current applicant IDs in DB:<br>";
    $allApplicants = $applicantsCollection->find();
    foreach ($allApplicants as $doc) {
        echo (string)$doc['_id'] . "<br>";
    }

    $applicant = $applicantsCollection->findOne(['_id' => $objectId]);

    if ($applicant) {
        $applicant['status'] = $status;
        $employeeCollection->insertOne($applicant);
        $applicantsCollection->deleteOne(['_id' => $applicant->_id]);
        echo "<br><strong>Transferred successfully.</strong>";
    } else {
        echo "<br><strong>Applicant not found in the database.</strong>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
