<?php
require '../vendor/autoload.php'; // Include Composer autoload
require '../connection.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;
    $usersCollection = $database->selectCollection("applicants"); 

$id = $_GET['id'] ?? null;
$applicant = [];

if ($id) {
  $applicant = $usersCollection->findOne(['_id' => new ObjectId($id)]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-4">
    <!-- Summary Card -->
    <div class="card mb-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h4><?= $applicant['personal_info']['first_name'] ?? '' ?> <?= $applicant['personal_info']['last_name'] ?? '' ?></h4>
          <p class="mb-1">Position Applied: <strong><?= $applicant['position_applied'] ?? '' ?></strong></p>
          <p class="mb-1">Desired Salary: <strong><?= $applicant['desired_salary'] ?? '' ?></strong></p>
          <span class="badge bg-warning text-dark"><?= $applicant['status'] ?? 'Pending' ?></span>
        </div>
        <img src="placeholder.jpg" alt="Photo" width="80" height="80" class="rounded-circle">
      </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#personal">Personal Info</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#family">Family</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#education">Education</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#employment">Employment</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#emergency">Emergency</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reference">Reference</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#documents">Documents</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#questionnaire">Questionnaire</a></li>
    </ul>

    <div class="tab-content p-3 border bg-white">
      <!-- Personal Info Tab -->
      <div class="tab-pane fade show active" id="personal">
        <p><strong>Address:</strong> <?= $applicant['personal_info']['address'] ?? '' ?></p>
        <p><strong>Email:</strong> <?= $applicant['personal_info']['email'] ?? '' ?></p>
        <p><strong>Contact:</strong> <?= $applicant['personal_info']['contact'] ?? '' ?></p>
        <p><strong>Age:</strong> <?= $applicant['personal_info']['age'] ?? '' ?> | <strong>Sex:</strong> <?= $applicant['personal_info']['sex'] ?? '' ?></p>
        <p><strong>Civil Status:</strong> <?= $applicant['personal_info']['civil_status'] ?? '' ?></p>
        <p><strong>Birth Date:</strong> <?= $applicant['personal_info']['birth_date'] ?? '' ?> | <strong>Birth Place:</strong> <?= $applicant['personal_info']['birth_place'] ?? '' ?></p>
        <p><strong>Citizenship:</strong> <?= $applicant['personal_info']['citizen'] ?? '' ?> | <strong>Religion:</strong> <?= $applicant['personal_info']['religion'] ?? '' ?></p>
        <p><strong>Height:</strong> <?= $applicant['personal_info']['height'] ?? '' ?> | <strong>Weight:</strong> <?= $applicant['personal_info']['weight'] ?? '' ?></p>
        <p><strong>Physical Defects:</strong> <?= $applicant['personal_info']['physical_defects'] ?? '' ?></p>
      </div>

      <!-- Family Tab -->
      <div class="tab-pane fade" id="family">
        <p><strong>Father:</strong> <?= $applicant['family_background']['father']['name'] ?? '' ?> - <?= $applicant['family_background']['father']['occupation'] ?? '' ?></p>
        <p><strong>Mother:</strong> <?= $applicant['family_background']['mother']['name'] ?? '' ?> - <?= $applicant['family_background']['mother']['occupation'] ?? '' ?></p>
        <p><strong>Spouse:</strong> <?= $applicant['family_background']['spouse']['name'] ?? 'N/A' ?> - <?= $applicant['family_background']['spouse']['occupation'] ?? '' ?></p>
        <p><strong>Parents' Address:</strong> <?= $applicant['family_background']['parents_address'] ?? '' ?></p>
      </div>

      <!-- Education Tab -->
      <div class="tab-pane fade" id="education">
        <p><strong>College:</strong> <?= $applicant['education']['college']['school'] ?? '' ?> - <?= $applicant['education']['college']['degree'] ?? '' ?></p>
        <p><strong>High School:</strong> <?= $applicant['education']['high_school']['school'] ?? '' ?> - <?= $applicant['education']['high_school']['degree'] ?? '' ?></p>
        <p><strong>Elementary:</strong> <?= $applicant['education']['elementary']['school'] ?? '' ?> - <?= $applicant['education']['elementary']['degree'] ?? '' ?></p>
        <p><strong>Vocational:</strong> <?= $applicant['education']['vocational']['school'] ?? 'N/A' ?> - <?= $applicant['education']['vocational']['degree'] ?? '' ?></p>
        <p><strong>Skills:</strong> <?= $applicant['education']['skills'] ?? '' ?></p>
      </div>

      <!-- Employment Tab -->
      <div class="tab-pane fade" id="employment">
        <p><strong>Company:</strong> <?= $applicant['employment_history']['company'] ?? '' ?></p>
        <p><strong>Position:</strong> <?= $applicant['employment_history']['position'] ?? '' ?></p>
        <p><strong>Reason for Leaving:</strong> <?= $applicant['employment_history']['reason_for_leaving'] ?? '' ?></p>
      </div>

      <!-- Emergency Tab -->
      <div class="tab-pane fade" id="emergency">
        <p><strong>Name:</strong> <?= $applicant['emergency_contact']['name'] ?? '' ?></p>
        <p><strong>Relationship:</strong> <?= $applicant['emergency_contact']['relationship'] ?? '' ?></p>
        <p><strong>Address:</strong> <?= $applicant['emergency_contact']['emergency_address'] ?? '' ?></p>
        <p><strong>Contact Number:</strong> <?= $applicant['emergency_contact']['emergency_number'] ?? '' ?></p>
      </div>

      <!-- Reference Tab -->
      <div class="tab-pane fade" id="reference">
        <p><strong>Name:</strong> <?= $applicant['character_reference']['name'] ?? '' ?></p>
        <p><strong>Company:</strong> <?= $applicant['character_reference']['company'] ?? '' ?></p>
        <p><strong>Position:</strong> <?= $applicant['character_reference']['position'] ?? '' ?></p>
        <p><strong>Contact:</strong> <?= $applicant['character_reference']['contact'] ?? '' ?></p>
      </div>

      <!-- Documents Tab -->
      <div class="tab-pane fade" id="documents">
        <?php if (!empty($applicant['documents'])): ?>
          <ul>
            <?php foreach ($applicant['documents'] as $doc): ?>
              <li><?= htmlspecialchars($doc) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No documents uploaded.</p>
        <?php endif; ?>
      </div>

      <!-- Questionnaire Tab -->
      <div class="tab-pane fade" id="questionnaire">
        <p><strong>Description:</strong> <?= $applicant['questionnaire']['description'] ?? '' ?></p>
        <p><strong>Career Plans:</strong> <?= $applicant['questionnaire']['career_plans'] ?? '' ?></p>
        <p><strong>Reason for Joining:</strong> <?= $applicant['questionnaire']['reason_for_joining'] ?? '' ?></p>
        <p><strong>Why Hire:</strong> <?= $applicant['questionnaire']['why_hire'] ?? '' ?></p>
        <p><strong>Expectations:</strong> <?= $applicant['questionnaire']['expectations'] ?? '' ?></p>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>