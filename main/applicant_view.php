<?php
include('../connection.php');
$collection = $database->selectCollection("applicants");

// Get ID from the URL
$id = $_GET['id'] ?? null;

if ($id) {
  try {
    $applicant = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($id)]);
  } catch (Exception $e) {
    die("Invalid ID format.");
  }

  if (!$applicant) {
    die("Applicant not found.");
  }

  // Extract the needed data
  $firstName = $applicant['personal_info']['first_name'] ?? '';
  $middleName = $applicant['personal_info']['middle_name'] ?? '';
  $lastName = $applicant['personal_info']['last_name'] ?? '';
  $fullName = "$firstName $middleName $lastName";

  $positionApplied = $applicant['position_applied'] ?? '';
  $desiredSalary = $applicant['desired_salary'] ?? '';
  $status = $applicant['status'] ?? '';
}
$personal = $applicant['personal_info'] ?? [];
$family = $applicant['family_background'] ?? [];
$education = $applicant['education'] ?? [];
$employment = $applicant['employment_history'] ?? [];
$emergency = $applicant['emergency_contact'] ?? [];
$references = $applicant['character_reference'] ?? [];
$questionnaire = $applicant['questionnaire'] ?? [];
$documents = $applicant['documents'] ?? [];

$regionCol = $database->selectCollection("region");
$provinceCol = $database->selectCollection("provinces");
$municipalityCol = $database->selectCollection("municipalities");
$barangayCol = $database->selectCollection("barangays");

$street = $applicant['personal_info']['personal_street'] ?? '';
$parentstreet = $applicant['family_background']['parent_street'] ?? '';
$emergencystreet = $applicant['emergency_contact']['emergency_street'] ?? '';

$region = $regionCol->findOne(['id' => (int)($applicant['personal_info']['personal_region'] ?? 0)]);
$province = $provinceCol->findOne(['id' => (int)($applicant['personal_info']['personal_province'] ?? 0)]);
$municipality = $municipalityCol->findOne(['id' => (int)($applicant['personal_info']['personal_municipality'] ?? 0)]);
$barangay = $barangayCol->findOne(['id' => (int)($applicant['personal_info']['personal_barangay'] ?? 0)]);

$parentregion = $regionCol->findOne(['id' => (int)($applicant['family_background']['parent_region'] ?? 0)]);
$parentprovince = $provinceCol->findOne(['id' => (int)($applicant['family_background']['parent_province'] ?? 0)]);
$parentmunicipality = $municipalityCol->findOne(['id' => (int)($applicant['family_background']['parent_municipality'] ?? 0)]);
$parentbarangay = $barangayCol->findOne(['id' => (int)($applicant['family_background']['parent_barangay'] ?? 0)]);

$emergencyregion = $regionCol->findOne(['id' => (int)($applicant['emergency_contact']['emergency_region'] ?? 0)]);
$emergencyprovince = $provinceCol->findOne(['id' => (int)($applicant['emergency_contact']['emergency_province'] ?? 0)]);
$emergencymunicipality = $municipalityCol->findOne(['id' => (int)($applicant['emergency_contact']['emergency_municipality'] ?? 0)]);
$emergencybarangay = $barangayCol->findOne(['id' => (int)($applicant['emergency_contact']['emergency_barangay'] ?? 0)]);

$currentAddress = $street;
$currentAddress .= ', ' . ($barangay['name'] ?? 'Unknown Barangay');
$currentAddress .= ', ' . ($municipality['name'] ?? 'Unknown City/Municipality');
$currentAddress .= ', ' . ($province['name'] ?? 'Unknown Province');
$currentAddress .= ', ' . ($region['name'] ?? 'Unknown Region');

$parentsFullAddress = $parentstreet;
$parentsFullAddress .= ', ' . ($parentbarangay['name'] ?? 'Unknown Barangay');
$parentsFullAddress .= ', ' . ($parentmunicipality['name'] ?? 'Unknown City/Municipality');
$parentsFullAddress .= ', ' . ($parentprovince['name'] ?? 'Unknown Province');
$parentsFullAddress .= ', ' . ($parentregion['name'] ?? 'Unknown Region');

$emergencyFullAddress = $emergencystreet;
$emergencyFullAddress .= ', ' . ($emergencybarangay['name'] ?? 'Unknown Barangay');
$emergencyFullAddress .= ', ' . ($emergencymunicipality['name'] ?? 'Unknown City/Municipality');
$emergencyFullAddress .= ', ' . ($emergencyprovince['name'] ?? 'Unknown Province');
$emergencyFullAddress .= ', ' . ($emergencyregion['name'] ?? 'Unknown Region');

// echo "<pre>";
// print_r($applicant);
// echo "</pre>";

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Applicant View</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 95%;
      min-height: 90%;
      margin: auto;
      margin-top: 20px;
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .profile {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
      margin-left: 20px;
    }

    .profile img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #003366;
    }

    .status-select {
      margin-top: 10px;
    }

    .tabs {
      display: flex;

      gap: 10px;
      margin-bottom: 20px;
    }

    .tabs button {
      background: #003366;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .tabs button.active {
      background: #0055aa;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      padding: 10px;

      border-radius: 5px;
    }

    .subtabs {
      display: flex;
      gap: 10px;
      margin: 10px 0;
    }

    .subtabs button {
      padding: 5px 10px;
    }

    .sub-content {
      display: none;
    }

    .sub-content.active {
      display: block;
    }

    .applicants-button {
      background-color: #00124d;
      border-left: 4px solid #ffffff;
    }

    .employment-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background-color: #ffffff;
      border: 1px solid #d0d7de;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .employment-table th {
      background: linear-gradient(to right, #4a90e2, #6fb1fc);
      color: #fff;
      text-align: left;
      padding: 10px 14px;
      font-weight: 600;
    }

    .employment-table td {
      padding: 10px 12px;
      border-top: 1px solid #e1e4e8;
      font-size: 13px;
      color: #333;
    }

    .employment-table tr:last-child td {
      border-bottom: none;
    }

    .employment-table tbody tr:nth-child(even) {
      background-color: #f8f9fa;
      /* light gray for zebra effect */
    }

    .employment-table tbody tr:hover {
      background-color: #e6f0ff;
      /* subtle blue hover */
    }




    .personal-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin-bottom: 20px;
      font-family: "Segoe UI", sans-serif;
    }

    .personal-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
    }

    .personal-table th {
      background: linear-gradient(to right, #4a90e2, #6fb1fc);
      color: #fff;
      text-align: left;
      padding: 10px 14px;
      font-weight: 600;
    }

    .personal-table td {
      padding: 8px 12px;
      border-top: 1px solid #eee;
    }

    .personal-table tr:hover td {
      background-color: #f5f9ff;
      /* light hover effect */
    }

    .personal-table label {
      font-weight: 500;
      color: #444;
      margin-right: 4px;
    }





    .references-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
      margin-top: 10px;
    }

    .references-table th {
      background: linear-gradient(to right, #4a90e2, #6fb1fc);
      color: #fff;
      text-align: left;
      padding: 10px 14px;
      font-weight: 600;
    }

    .references-table td {
      padding: 8px 12px;
      border: 1px solid #ddd;
      /* cell borders */
    }

    .references-table tr:nth-child(even) {
      background: #fafafa;
      /* subtle striping */
    }

    .references-table tr:hover td {
      background: #f5f9ff;
      /* light hover effect */
    }






    .question-card {
      background: #f9fafc;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .question-card strong {
      display: block;
      margin-bottom: 4px;
      color: #333;
      font-weight: 600;
    }

    .question-card p {
      margin: 0;
      color: #555;
      line-height: 1.4;
    }








    .info-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-top: 10px;
      font-size: 16px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .info-table th {
      background: linear-gradient(to right, #4a90e2, #6fb1fc);
      color: #fff;
      text-align: left;
      padding: 10px 14px;
      font-weight: 600;
    }

    .info-table td {
      padding: 10px 14px;
      border-bottom: 1px solid #eee;
      background: #fff;
      color: #444;
    }

    .info-table tr:nth-child(even) td {
      background: #f9fbff;
    }

    .info-table tr:last-child td {
      border-bottom: none;
    }

    .info-table tr:hover td {
      background: #eef5ff;
    }






    .subtabs {
      display: flex;
      gap: 6px;
      margin-bottom: 12px;
    }

    .subtab {
      padding: 8px 14px;
      background: #f0f0f0;
      border: none;
      cursor: pointer;
      border-radius: 5px;
      font-size: 14px;
    }

    .subtab.active {
      background: #4a90e2;
      color: #fff;
    }

    .documents-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      margin-top: 12px;
    }

    .doc-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.12);
      width: 180px;
      text-align: center;
      padding: 8px;
      font-size: 14px;
      transition: transform 0.2s;
    }

    .doc-card:hover {
      transform: translateY(-4px);
    }

    .doc-thumb img {
      width: 100%;
      height: 130px;
      object-fit: cover;
      border-radius: 5px;
      margin-bottom: 6px;
    }

    .doc-name {
      margin-bottom: 6px;
      word-break: break-all;
    }

    .doc-button {
      display: inline-block;
      background: #4a90e2;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      text-decoration: none;
    }

    .doc-button:hover {
      background: #3578c9;
    }
  </style>




</head>

<body>
  <?php include 'sidebar.php'; ?>
  <div class="header">Applicant View</div><br><br><br>
  <div class="content">

    <div class="container">
      <div class="profile">
        <?php
        $photoIds = $applicant['documents']['2x2_pic'] ?? [];
        $photoId = isset($photoIds[0]) ? $photoIds[0] : null;
        ?>
        <?php if ($photoId): ?>
          <img src="../handlers/get_image.php?id=<?php echo $photoId; ?>" alt="Applicant Photo" />
        <?php else: ?>
          <img src="../images/default-avatar.png" alt="No Photo" />
        <?php endif; ?>

        <div>
          <h2 id="applicant-name"></h2>
          <p>Position Applied: <strong id="position-applied"></strong></p>
          <p>Desired Salary: <strong id="desired-salary"></strong></p>
          <div class="status-select">
            <label for="status">Status:</label>
            <select id="status">
              <option selected>Under Review</option>
              <option>Hired</option>
              <option>Declined</option>
              <option>Rejected</option>
            </select>
          </div>
        </div>
      </div>

      <div class="tabs">
        <button class="tab active" data-tab="personal">Personal Information</button>
        <button class="tab" data-tab="family">Family Background</button>
        <button class="tab" data-tab="education">Educational Background</button>
        <button class="tab" data-tab="employment">Employment History</button>
        <button class="tab" data-tab="emergency">Emergency Contact</button>
        <button class="tab" data-tab="references">Character References</button>
        <button class="tab" data-tab="questionnaire">Questionnaire</button>
        <button class="tab" data-tab="documents">Documents</button>
      </div>
      <div style="text-align: right; margin-bottom: 10px;">
      <button id="edit-btn">Edit</button>
      <button id="save-btn" style="display: none;">Save</button>
      </div>
      <div id="personal" class="tab-content active">


        <div class="personal-card">
          <table class="personal-table">
            <thead>
              <tr>
                <th colspan="6" style="font-size: 16px;">Personal Information</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="6">
                  <label>Current Address:</label> <?php echo htmlspecialchars($currentAddress); ?>
                </td>
              </tr>
              <tr>
                <td colspan="3">
                  <label>Birth Date:</label>
                  <span class="view-mode"><?php echo htmlspecialchars($personal['birth_date'] ?? ''); ?></span>
                  <input type="text" name="birth_date" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['birth_date'] ?? ''); ?>">
                </td>
                <td colspan="3">
                  <label>Birth Place:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['birth_place'] ?? ''); ?></span>
                  <input type="text" name="birth_place" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['birth_place'] ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <label>Age:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['age'] ?? ''); ?></span>
                  <input type="text" name="age" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['age'] ?? ''); ?>">
                </td>
                <td colspan="2">
                  <label>Gender:</label>
                  <span class="view-mode"><?php echo htmlspecialchars($personal['gender'] ?? ''); ?></span>
                  <input type="text" name="gender" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['gender'] ?? ''); ?>">
                </td>
                <td colspan="2">
                  <label>Civil Status:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['civil_status'] ?? ''); ?></span>
                  <input type="text" name="civil_status" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['civil_status'] ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <td colspan="3">
                  <label>Citizenship:</label> 
                   <span class="view-mode"><?php echo htmlspecialchars($personal['citizenship'] ?? ''); ?></span>
                  <input type="text" name="citizenship" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['citizenship'] ?? ''); ?>">
                </td>
                <td colspan="3">
                  <label>Religion:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['religion'] ?? ''); ?></span>
                  <input type="text" name="religion" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['religion'] ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <td colspan="3">
                  <label>Email:</label>
                  <span class="view-mode"><?php echo htmlspecialchars($personal['email'] ?? ''); ?></span>
                  <input type="text" name="email" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['email'] ?? ''); ?>">
                </td>
                <td colspan="3">
                  <label>Contact No:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['contact_no'] ?? ''); ?></span>
                  <input type="text" name="contact_no" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['contact_no'] ?? ''); ?>">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <label>Height:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['height'] ?? ''); ?></span>
                  <input type="text" name="height" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['height'] ?? ''); ?>">
                </td>
                <td colspan="2">
                  <label>Weight:</label> 
                  <span class="view-mode"><?php echo htmlspecialchars($personal['weight'] ?? ''); ?></span>
                  <input type="text" name="weight" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($personal['weight'] ?? ''); ?>">
                </td>
                <td colspan="2">
                  <label>Disability:</label>
                  <span class="view-mode"><?php echo htmlspecialchars($personal['disability'] ?? ''); ?></span>
                  <input type="text" name="disability" class="disability" style="display:none;" value="<?php echo htmlspecialchars($personal['disability'] ?? ''); ?>">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>




      <div id="family" class="tab-content">

        <table class="info-table">
          <thead>
            <tr>
              <th colspan="2">Family Background</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Father</strong></td>
              <td>
              <span class="view-mode"><?php echo htmlspecialchars($family['father']['name'] ?? ''); ?></span>
                  <input type="text" name="father" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($family['father']['name'] ?? ''); ?>">
            </tr>
            <tr>
              <td><strong>Mother</strong></td>
              <td>
              <span class="view-mode"><?php echo htmlspecialchars($family['mother']['name'] ?? ''); ?></span>
                  <input type="text" name="mother" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($family['mother']['name'] ?? ''); ?>">
            </tr>
            <tr>
              <td><strong>Spouse</strong></td>
              <td>
              <span class="view-mode"><?php echo htmlspecialchars($family['spouse']['name'] ?? ''); ?></span>
                  <input type="text" name="spouse" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($family['spouse']['name'] ?? ''); ?>">
            </tr>
            <tr>
              <td><strong>Parents' Address</strong></td>
              <td>
               <span class="view-mode"><?php echo htmlspecialchars($parentsFullAddress); ?></span>
                  <input type="text" name="spouse" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($parentsFullAddress); ?>">
            </tr>
          </tbody>
        </table>
      </div>




      <div id="education" class="tab-content">

        <table class="info-table">
          <thead>
            <tr>
              <th>Level</th>
              <th>School</th>
              <th>Degree</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>College</td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($education['college']['school'] ?? ''); ?></span>
                  <input type="text" name="college" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['college']['school'] ?? ''); ?>">
            </td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($education['college']['degree'] ?? ''); ?></span>
                  <input type="text" name="college_degree" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['college']['degree'] ?? ''); ?>"></td>
            </tr>
            <tr>
              <td>High School</td>
              <td>
              <span class="view-mode"><?php echo htmlspecialchars($education['high_school']['school'] ?? ''); ?></span>
                  <input type="text" name="high_school" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['high_school']['school'] ?? ''); ?>">
            </td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($education['high_school']['degree'] ?? ''); ?></span>
                  <input type="text" name="high_school_degree" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['high_school']['degree'] ?? ''); ?>"></td>
            </td>
            </tr>
            <tr>
              <td>Elementary</td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($education['elementary']['school'] ?? ''); ?></span>
                  <input type="text" name="elementary" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['elementary']['school'] ?? ''); ?>">
            </td>
              <td>
             <span class="view-mode"><?php echo htmlspecialchars($education['elementary']['degree'] ?? ''); ?></span>
                  <input type="text" name="elementary_degree" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['elementary']['degree'] ?? ''); ?>"></td>
            </td>
            </tr>
            <tr>
              <td>Vocational</td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($education['vocational']['school'] ?? ''); ?></span>
                  <input type="text" name="vocational" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['vocational']['school'] ?? ''); ?>">
            </td>
              <td>
              <span class="view-mode"><?php echo htmlspecialchars($education['vocational']['degree'] ?? ''); ?></span>
                  <input type="text" name="vocational_degree" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['vocational']['degree'] ?? ''); ?>"></td>
            </td>
            </tr>
            <tr>
              <td>Masteral</td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($education['masteral']['school'] ?? ''); ?></span>
                  <input type="text" name="masteral" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['masteral']['school'] ?? ''); ?>">
            </td>
              <td>
              <span class="view-mode"><?php echo htmlspecialchars($education['masteral']['degree'] ?? ''); ?></span>
                  <input type="text" name="masteral_degree" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($education['masteral']['degree'] ?? ''); ?>"></td>
            </td>
            </tr>
          </tbody>
        </table>
      </div>


      <div id="employment" class="tab-content">

        <?php if (!empty($employment)): ?>
          <table class="employment-table">
            <thead>
              <tr>
                <th>Company Name</th>
                <th>Position</th>
                <th>Reason for Leaving</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($employment as $job): ?>
                <tr>
                  <td>
                <span class="view-mode"><?php echo htmlspecialchars($job['company'] ?? ''); ?></span>
                  <input type="text" name="company" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($employment['company'] ?? ''); ?>">
                </td>
                  <td>
                  <span class="view-mode"><?php echo htmlspecialchars($job['position'] ?? ''); ?></span>
                  <input type="text" name="position" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($employment['company'] ?? ''); ?>">
                </td>
                  <td>
                <span class="view-mode"><?php echo htmlspecialchars($job['reason_for_leaving'] ?? ''); ?></span>
                  <input type="text" name="reason_for_leaving" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($employment['reason_for_leaving'] ?? ''); ?>">
                </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>


        <?php else: ?>
          <p>No employment history provided.</p>
        <?php endif; ?>
      </div>


      <div id="emergency" class="tab-content">

        <table class="info-table">
          <thead>
            <tr>
              <th colspan="2">Emergency Contact</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Name</strong></td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($emergency['name'] ?? ''); ?></span>
                  <input type="text" name="name" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($emergency['name'] ?? ''); ?>">
            </td>
            </tr>
            <tr>
              <td><strong>Relationship</strong></td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($emergency['relationship'] ?? ''); ?></span>
                  <input type="text" name="relationship" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($emergency['relationship'] ?? ''); ?>">
            </td>
            </tr>
            <tr>
              <td><strong>Address</strong></td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($emergencyFullAddress); ?></span>
                  <input type="text" name="relationship" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($emergencyFullAddress); ?>">
            </td>
            </tr>
            <tr>
              <td><strong>Contact Number</strong></td>
              <td>
            <span class="view-mode"><?php echo htmlspecialchars($emergency['emergency_number'] ?? ''); ?></span>
                  <input type="text" name="emergency_number" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($emergency['emergency_number'] ?? ''); ?>">
            </td>
            </tr>
          </tbody>
        </table>
      </div>


      <div id="references" class="tab-content">
        <?php if (!empty($references)): ?>
          <table class="references-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Position</th>
                <th>Contact</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($references as $ref): ?>
                <tr>
                  <td>
                <span class="view-mode"><?php echo htmlspecialchars($ref['name'] ?? ''); ?></span>
                  <input type="text" name="name" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($references['name'] ?? ''); ?>">
                </td>
                  <td>
                <span class="view-mode"><?php echo htmlspecialchars($ref['company'] ?? ''); ?></span>
                  <input type="text" name="company" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($references['company'] ?? ''); ?>">
                </td>
                  <td>
                <span class="view-mode"><?php echo htmlspecialchars($ref['position'] ?? ''); ?></span>
                  <input type="text" name="position" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($references['position'] ?? ''); ?>">
                </td>
                  <td>
                  <span class="view-mode"><?php echo htmlspecialchars($ref['contact'] ?? ''); ?></span>
                  <input type="text" name="contact" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($references['contact'] ?? ''); ?>">
                </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No references provided.</p>
        <?php endif; ?>
      </div>


      <div id="questionnaire" class="tab-content">

        <div class="question-card">
          <strong>Description:</strong>
          <p>  
        <span class="view-mode"><?php echo htmlspecialchars($questionnaire['description'] ?? ''); ?></span>
                  <input type="text" name="description" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($questionnaire['description'] ?? ''); ?>">
        </p>
        </div>
        <div class="question-card">
          <strong>Career Plans:</strong>
          <p>
        <span class="view-mode"><?php echo htmlspecialchars($questionnaire['career_plans'] ?? ''); ?></span>
                  <input type="text" name="career_plans" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($questionnaire['career_plans'] ?? ''); ?>">
        </p>
        </div>
        <div class="question-card">
          <strong>Reason for Joining:</strong>
          <p>
        <span class="view-mode"><?php echo htmlspecialchars($questionnaire['reason_for_joining'] ?? ''); ?></span>
                  <input type="text" name="reason_for_joining" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($questionnaire['reason_for_joining'] ?? ''); ?>">
        </p>
        </div>
        <div class="question-card">
          <strong>Why Should We Hire You:</strong>
          <p>
        <span class="view-mode"><?php echo htmlspecialchars($questionnaire['why_hire'] ?? ''); ?></span>
                  <input type="text" name="why_hire" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($questionnaire['why_hire'] ?? ''); ?>">
        </p>
        </div>
        <div class="question-card">
          <strong>Expectations:</strong>
          <p>
        <span class="view-mode"><?php echo htmlspecialchars($questionnaire['expectations'] ?? ''); ?></span>
                  <input type="text" name="expectations" class="edit-mode" style="display:none;" value="<?php echo htmlspecialchars($questionnaire['expectations'] ?? ''); ?>">
        </p>
        </div>
      </div>

      <div id="documents" class="tab-content">
        
        <div class="subtabs">
          <button class="subtab active" data-sub="photo">2x2 Photo</button>
          <button class="subtab" data-sub="resume">Resume</button>
          <button class="subtab" data-sub="training">Training Certificate</button>
          <button class="subtab" data-sub="diploma">Diploma</button>
          <button class="subtab" data-sub="contracts">Contracts</button>
          <button class="subtab" data-sub="tor">Transcript of Records</button>
        </div>

        <?php
        // Map input names to display-friendly subtab IDs
        $docSections = [
          '2x2_pic' => 'photo',
          'resume_applicant' => 'resume',
          'training_certificates' => 'training',
          'diploma' => 'diploma',
          'contracts' => 'contracts',
          'transcript_of_records' => 'tor'
        ];

        foreach ($docSections as $inputName => $subId): ?>
          <div id="<?php echo $subId; ?>" class="sub-content <?php echo ($subId === 'photo') ? 'active' : ''; ?>">
            <?php if (!empty($documents[$inputName])): ?>
              <div class="documents-grid">
                <?php foreach ($documents[$inputName] as $fileId):
                  // Fetch metadata
                  $metadata = $database->selectCollection('fs.files')->findOne(['_id' => new MongoDB\BSON\ObjectId($fileId)]);
                  $mimeType = $metadata->metadata->mimeType ?? '';
                  $fileName = $metadata->metadata->originalName ?? ($fileId . ".file");

                  $downloadUrl = "../handlers/download_file.php?id=" . urlencode($fileId);
                  $isImage = strpos($mimeType, 'image/') === 0;
                  $previewUrl = $isImage ? $downloadUrl : '../images/default_file_icon.png'; // replace with your actual icon path
                ?>
                  <div class="doc-card">
                    <div class="doc-thumb">                      <img src="<?php echo htmlspecialchars($previewUrl); ?>" alt="Document">
                    </div>
                    <div class="doc-name"><?php echo htmlspecialchars($fileName); ?></div>
                    <a href="<?php echo htmlspecialchars($downloadUrl); ?>" class="doc-button" target="_blank">View / Download</a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p>No files uploaded.</p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
    <script>
    // Sample data injection (replace with dynamic content)
    document.getElementById("applicant-name").textContent = "<?php echo htmlspecialchars($fullName); ?>";
    document.getElementById("position-applied").textContent = "<?php echo htmlspecialchars($positionApplied); ?>";
    document.getElementById("desired-salary").textContent = "<?php echo htmlspecialchars($desiredSalary); ?>";
    document.getElementById("status").value = "<?php echo htmlspecialchars($status); ?>";

    // Tabs
    document.querySelectorAll(".tab").forEach((tab) => {
      tab.addEventListener("click", () => {
        document
          .querySelectorAll(".tab")
          .forEach((t) => t.classList.remove("active"));
        document
          .querySelectorAll(".tab-content")
          .forEach((c) => c.classList.remove("active"));
        tab.classList.add("active");
        document.getElementById(tab.dataset.tab).classList.add("active");
      });
    });

    // Subtabs
    document.querySelectorAll(".subtab").forEach((sub) => {
      sub.addEventListener("click", () => {
        document
          .querySelectorAll(".subtab")
          .forEach((s) => s.classList.remove("active"));
        document
          .querySelectorAll(".sub-content")
          .forEach((c) => c.classList.remove("active"));
        sub.classList.add("active");
        document.getElementById(sub.dataset.sub).classList.add("active");
      });
    });

    // document.getElementById("applicant-name").textContent =
    //   applicantData.name;
    // document.getElementById("position-applied").textContent =
    //   applicantData.position;
    // document.getElementById("desired-salary").textContent =
    //   applicantData.salary;

//Edit and Save functionality
 document.getElementById("edit-btn").addEventListener("click", function () {
  document.querySelectorAll(".view-mode").forEach(el => el.style.display = "none");
  document.querySelectorAll(".edit-mode").forEach(el => el.style.display = "inline-block");

  document.getElementById("edit-btn").style.display = "none";
  document.getElementById("save-btn").style.display = "inline-block";
});

document.getElementById("save-btn").addEventListener("click", function () {
  const formData = new FormData();

  document.querySelectorAll(".edit-mode").forEach(input => {
    formData.append(input.name, input.value);
  });

  fetch("../handlers/update_application.php?id=<?php echo $id; ?>", {
    method: "POST",
    body: formData
  })
    .then(response => response.text())
    .then(result => {
      alert("Updated successfully.");
      location.reload(); // or switch back to view mode manually
    })
    .catch(error => {
      alert("Update failed.");
      console.error(error);
    });
});

  </script>


</body>

</html>