<?php
require '../vendor/autoload.php'; // Include MongoDB library
require '../connection.php';
// include 'sidebar.php';

// Connect to MongoDB
$usersCollection = $database->selectCollection("applicants"); 

// Get applicant ID from URL
$id = $_GET['id'] ?? '';
$applicant = null;

if ($id) {
    $applicant = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);

    if (!$applicant) {
        die("Applicant not found.");
    }
} else {
    die("No applicant ID provided.");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Applicant</title>
  <style>
    .readonly {
      background-color: #f5f5f5;
      border: none;
    }
    .toggle-button {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="content">
  <h2>Employment Application Form</h2>

  <form action="process_application.php" method="POST" id="applicantForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="section-container">
      <h3>Personal Information</h3>
      <table border="1" cellpadding="5">
        <tr>
          <td>Name:</td>
          <td><input type="text" name="last_name" value="<?= $applicant['last_name'] ?? '' ?>" class="editable-field readonly" readonly></td>
          <td><input type="text" name="first_name" value="<?= $applicant['personal_info']['first_name'] ?? '' ?>" class="editable-field readonly" readonly></td>
          <td><input type="text" name="middle_name" value="<?= $applicant['middle_name'] ?? '' ?>" class="editable-field readonly" readonly></td>
        </tr>
        <tr>
          <td>Address:</td>
          <td colspan="3"><input type="text" name="address" value="<?= $applicant['address'] ?? '' ?>" class="editable-field readonly" readonly></td>
        </tr>
        <tr>
          <td>Birthdate:</td>
          <td><input type="date" name="birthdate" value="<?= $applicant['birthdate'] ?? '' ?>" class="editable-field readonly" readonly></td>
          <td>Age:</td>
          <td><input type="text" name="age" value="<?= $applicant['age'] ?? '' ?>" class="editable-field readonly" readonly></td>
        </tr>
        <tr>
          <td>Sex:</td>
          <td><input type="text" name="sex" value="<?= $applicant['sex'] ?? '' ?>" class="editable-field readonly" readonly></td>
          <td>Civil Status:</td>
          <td><input type="text" name="civil_status" value="<?= $applicant['civil_status'] ?? '' ?>" class="editable-field readonly" readonly></td>
        </tr>
        <tr>
          <td>Religion:</td>
          <td><input type="text" name="religion" value="<?= $applicant['religion'] ?? '' ?>" class="editable-field readonly" readonly></td>
          <td>Contact Number:</td>
          <td><input type="text" name="contact_no" value="<?= $applicant['contact_no'] ?? '' ?>" class="editable-field readonly" readonly></td>
        </tr>
      </table>

      <!-- Button to toggle edit/save -->
      <button type="button" class="toggle-button" onclick="toggleEdit('applicantForm', this)">Edit</button>
      <button type="submit">Submit</button>
    </div>
  </form>
</div>

<script>
function toggleEdit(formId, button) {
  const form = document.getElementById(formId);
  const fields = form.querySelectorAll('.editable-field');

  const isReadOnly = fields[0].readOnly;

  fields.forEach(field => {
    field.readOnly = !isReadOnly;
    field.classList.toggle('readonly', isReadOnly);
  });

  button.textContent = isReadOnly ? 'Save' : 'Edit';
}
</script>

</body>
</html>
