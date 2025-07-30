<?php
include('../connection.php');
$collection = $database->selectCollection("applicants");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $position_applied = trim($_POST['position_applied']);
    $status = "Pending";

    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($phone) && !empty($position_applied)) {
        $uploads = [];
        $documentFields = [
            "resume", "application_form", "resume_applicant", "diploma", "transcript_of_records",
            "training_certificates", "prc", "contracts", "masteral_documents", "phd_documents"
        ];

        $targetDir = "../upload/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach ($documentFields as $doc) {
            if (isset($_FILES[$doc]) && $_FILES[$doc]['error'] == 0) {
                $fileTmpPath = $_FILES[$doc]['tmp_name'];
                $fileName = basename($_FILES[$doc]['name']);
                $destPath = $targetDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $uploads[$doc] = $destPath;
                }
            }
        }

        $newApplicant = [
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone,
            'position_applied' => $position_applied,
            'status' => $status,
            'documents' => $uploads
        ];
        
        $insertResult = $collection->insertOne($newApplicant);
        if ($insertResult->getInsertedCount() > 0) {
            header("Location: ../sidebar_menu/add_applicant.copy.php");
            exit();
        } else {
            $error = "Failed to add applicant. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<html>
<body>
<?php include '../sidebar_menu/sidebar.php'; ?>
<div class="content">
    <h2 class="header">Upload Applicant Documents</h2><br><br><br>
    <div class="box-body">
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    </div>
</div>
</body>
</html>
