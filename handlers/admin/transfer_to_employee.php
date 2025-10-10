<?php
// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

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
