<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Application Form</title>
    <link rel="stylesheet" href="../css/application_form.css?v=<?= filemtime('../css/application_form.css') ?>">

    <style>
        .validation-message small {
            position: relative;
            background: rgb(255, 116, 116);
            padding: 12px 16px;
            border-radius: 12px;
            max-width: 370px;
            margin-top: 10px;
            left: 0px;
            font-family: sans-serif;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);

        }

        .validation-message small {
            position: relative;
            top: -3px;
            left: -185px;
            margin-top: 8px;
        }

        .validation-message small:before {
            content: "";
            position: absolute;
            top: -9px;
            left: 10px;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid rgb(256, 116, 116);
        }

        .application-button {
            background-color: #00124d;
            border-left: 4px solid #ffffff;
        }

        #pageIndicator {
            font-size: 23px;
            color: black;
            font-weight: bold;
            /* position: fixed; */
        }

        .box-body {
            margin-top: 0% !important;
        }
    </style>
</head>
<?php include 'sidebar2.php'; ?>

<body>
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <button class="x" onclick="exitForm()"><i class="fas fa-xmark" style="cursor:pointer;"></i></button>

            <h2>Data Privacy Notice</h2>
            <p>
                By proceeding with this application form, I acknowledge that the personnel information I providing will be collected and processed by the HR Department in accordance with the Data Privacy Act of 2012.
            </p>
            <button class="agree" onclick="closePrivacyModal()">I Agree</button>
        </div>
    </div>

    <div id="confirmModal" class="modal-overlay" style="display: none;">
        <div class="modal-box2">
            <h3>Are you sure you want to submit?</h3>
            <div class="modal-buttons">
                <button class="yes-btn" id="yesButton" onclick="submitForm()">Yes</button>
                <button class="no-btn" onclick="closeModal()">No</button>
            </div>
        </div>
    </div>
    <div class="header">Employment Application Form</div><br><br><br><br>
    <div class="content">

    <form action="../handlers/process_application.php" method="POST" id="empform" enctype="multipart/form-data">

        <div class="box-header">
            <div id="pageIndicator">Page 1/7</div>
                <!-- <a href="applicants.php" class="button"> Back </a> -->

        </div>
        <div class="box-body">
            <!-- filepath: vsls:/sidebar_menu/application_form.php -->
            <div id="page1" class="page active">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; gap: 40px;">
                    <!-- Position Applied For -->
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                        <label for="position_applied" style="white-space: nowrap;"><strong>Position Applied For:</strong></label>
                        <select id="position_applied" name="position_applied" required style="flex: 1; padding: 8px;">
                            <option value="">Select a position</option>
                        </select>
                    </div>

                    <!-- Desired Salary -->
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                        <label for="desired_salary" style="white-space: nowrap;"><strong>Desired Salary:</strong></label>
                        <input type="number" id="desired_salary" name="desired_salary" placeholder="₱" style="flex: 1; padding: 8px;" step="1000" required>
                    </div>

                    <!-- Employment Type -->
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                    <td colspan="1"><label>Employment Type:</label></td>
                                <td colspan="2"><select id="employment_type" name="employment_type" style="flex: 1; padding: 8px;" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="full-time">Full-Time</option>
                                        <option value="part-time">Part-Time</option>
                                        
                                    </select></td>
                                    
                    </div>
                </div>

                <!-- <form action="" method="post"> -->
                <div class="form-container_left">
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th colspan="6">
                                    <h3>Personal Information</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="1"><label>Name:</label></td>
                                <td colspan="5">
                                    <div style="width: 100%; display: flex; gap: 2px;">
                                        <input name="last_name" type="text" placeholder="Last Name">
                                        <input name="first_name" placeholder="First Name">
                                        <input name="middle_name" placeholder="Middle Name">
                                    </div>
                                </td>
                            </tr>
                            <tr class="applicant_address">
                                <td rowspan="1"><label>Current Address:</label></td>

                                <!-- Address fields in a single column -->
                                <td colspan="5">
                                    <!-- Street Input -->
                                    <div style="margin-bottom: 10px;">
                                        <input type="text" name="personal_street" id="personal_street" placeholder="Street/Building/Blck" style="width: 100%;" required>
                                    </div>

                                    <!-- Dropdowns: Region, Province, Municipality, Barangays -->
                                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                                        <select name="personal_region" id="personal_region" onchange="loadProvinces('personal')" required>
                                            <option value="">Region</option>
                                        </select>
                                        <select name="personal_province" id="personal_province" onchange="loadMunicipalities('personal')" disabled required>
                                            <option value="">Province</option>
                                        </select>
                                        <select name="personal_municipality" id="personal_municipality" onchange="loadBarangays('personal')" disabled required>
                                            <option value="">Municipality</option>
                                        </select>
                                        <select name="personal_barangay" id="personal_barangay" disabled required>
                                            <option value="">Barangays</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr style="width: 100%">
                                <td colspan="1"><label>Email:</label></td>
                                <td colspan="2"><input name="email" type="email" placeholder="juandelacruz@gmail.com"></td>
<td colspan="1"><label for="contact_no">Contact No. (+63):</label></td>
<td colspan="2">
  <input
    id="contact_no"
    name="contact_no"
    type="text"
    pattern="[0-9]{10}"
    maxlength="10"
    placeholder="9123456789"
    required
    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
    title="Enter 10-digit mobile number after +63 (e.g., 9123456789)"
  >
</td>

                            </tr>
                            <tr>
                                <td colspan="1"><label>Birth Date:</label></td>
                                <td colspan="2"><input type="date" name="birth_date" id="birthdate"></td>
                                <td colspan="1"><label>Birth Place:</label></td>
                                <td colspan="2"><input type="text" name="birth_place" placeholder="City only"></td>
                            </tr>
                            <tr>
                                <td colspan="1"><label>Age:</label></td>
                                <td colspan="2"><input type="text" name="age" id="age" readonly></td> <!-- make it readonly -->
                                <td colspan="1"><label>Gender:</label></td>
                                <td colspan="2"><select id="gender" name="gender">
                                        <option value="">-- Select Gender --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="nonbinary">Non-binary</option>
                                        <option value="prefer_not_to_say">Prefer not to say</option>
                                        <option value="other">Other</option>
                                    </select></td>
                            </tr>
                            <tr style="width: 100%">
                                <td colspan="1"><label>Height(inches):</label></td>
                                <td colspan="2"><input type="height" name="height" placeholder="5'7"></td>
                                <td colspan="1"><label>Weight(kg):</label></td>
                                <td colspan="2"><input type="weight" name="weight" placeholder="65kg"></td>
                            </tr>
                            <tr style="width: 100%;">
                                <div style="">
                                    <td style="width: 13;"><label>Citizenship:</label></td>
                                    <td style="width: 20%;"><input type="text" name="citizenship"></td>
                                    <td style="width: 13%;"><label>Civil Status:</label></td>
                                    <td style="width: 20%;"><select name="civil_status">
                                            <option value="married">Married </option>
                                            <option value="never_married">Never Married </option>
                                            <option value="separated">Separated </option>
                                            <option value="divorced">Divorced</option>
                                            <option value="legally_separated">Legally Separated </option>
                                            <option value="single">Single </option>
                                            <option value="widowed">Widowed </option>
                                            <option value="living_common_law">Living Common Law </option>
                                            <option value="marital_status">Marital Status </option>
                                        </select>
                                    </td>
                                    <td style="width: 13%;"><label>Religion:</label></td>
                                    <td style="width: 20%;"><input type="email" name="religion"></td>
                                </div>
                            </tr>
                            <tr style="width: 100% align-items: left;">

                            <tr>
                                <td style="width: 150px;"><label>Disabilities?</label></td>
                                <td style="padding-left: 0;">
                                    <div style="display: flex; gap: 20px;">
                                        <label style="display: flex; align-items: center; gap: 5px;">
                                            <input id="disability" type="checkbox" name="disability" value="yes" onclick="toggleCheckbox(this)">
                                            Yes
                                        </label>

                                        <label style="display: flex; align-items: center; gap: 5px;">
                                            <input type="checkbox" name="disability" value="no" onclick="toggleCheckbox(this)">
                                            No
                                        </label>
                                    </div>
                                </td>
                            </tr>



                            </tr>
                            </tr>
                        </tbody>
                    </table>
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th colspan="4">
                                    <h3>Family Background: </h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 15%;"><label>Father's Name:</label></td>
                                <td style="width: 35%"><input type="text" name="father_name"></td>
                                <td><label>Occupation:</label></td>
                                <td><input type="text" name="father_occupation"></td>
                            </tr>
                            <tr>
                                <td><label>Mother's Name:</label></td>
                                <td><input type="text" name="mother_name"></td>
                                <td><label>Occupation:</label></td>
                                <td><input type="text" name="mother_occupation"></td>
                            </tr>
                            <tr>
                                <td colspan="1"><label>Parent's Address:</label></td>
                                <td colspan="5">
                                    <div style="margin-bottom: 10px;">
                                        <input type="text" name="parents_address" id="parent_street" placeholder="Street/Building/Block" style="width: 100%;" required>
                                    </div>

                                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                                        <select name="parent_region" id="parent_region" onchange="loadProvinces('parent')" required>
                                            <option value="">Region</option>
                                        </select>
                                        <select name="parent_province" id="parent_province" onchange="loadMunicipalities('parent')" disabled required>
                                            <option value="">Province</option>
                                        </select>
                                        <select name="parent_municipality" id="parent_municipality" onchange="loadBarangays('parent')" disabled required>
                                            <option value="">Municipality</option>
                                        </select>
                                        <select name="parent_barangay" id="parent_barangay" disabled required>
                                            <option value="">Barangays</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Spouse Name:</label></td>
                                <td><input type="text" name="spouse_name"></td>
                                <td><label>Occupation:</label></td>
                                <td><input type="text" name="spouse_occupation"></td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th colspan="6">
                                    <h3>Educational Background</h3>
                                </th>
                            </tr>
                            <tr>
                                <th style="text-align: left;">
                                    <h4>School and Address:</h4>
                                </th>
                                <th>
                                    <h4>Degree/Honor Received:</h4>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            <tr>
                                <td><input type="text" placeholder="Elementary" name="elementary"></td>
                                <td><input type="text" name="elementary_degree"></td>
                            </tr>
                            <tr>
                                <td><input type="text" placeholder="High School" name="high_school"></td>
                                <td><input type="text" name="high_school_degree"></td>
                            </tr>
                            <tr>
                                <td><input type="text" placeholder="College" name="college"></td>
                                <td><input type="text" name="college_degree"></td>
                            </tr>
                            <tr>
                                <td><input type="text" placeholder="Vocational" name="vocational"></td>
                                <td><input type="text" name="vocational_degree"></td>
                            </tr>
                            <tr>
                                <td><input type="text" placeholder="Masteral/Doctoral" name="masteral"></td>
                                <td><input type="text" name="masteral_degree"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form_container rignt">
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th colspan="4">
                                    <h3>Employment Record</h3>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <h4><label>Company & Address</label></h4>
                                </td>
                                <td>
                                    <h4><label>Position</label></h4>
                                </td>
                                <td>
                                    <h4><label>Reason for Leaving</label></h4>
                                </td>
                                <td>
                                    <h4>Action</h4>
                                </td>
                            </tr>
                        </thead>
                        <tbody id="employmentBody">
                            <tr>
                                <td><input type="text" name="company[]"></td>
                                <td><input type="text" name="position[]"></td>
                                <td><input type="text" name="reason_for_leaving[]"></td>
                                <td class="action-cell"></td>
                            </tr>

                        </tbody>
                        <tr>
                            <td colspan="6"><button type="button" onclick="addRow()">+ Add New Row</button></td>
                        </tr>
                    </table>


                    <table class="form-table">
                        <thead>
                            <tr>
                                <th colspan="2">
                                    <h3>Emergency Contact</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label>Person to Notify</label></td>
                                <td><input type="text" name="ref_name"></td>
                            </tr>
                            <tr>
                                <td><label>Relationship</label></td>
                                <td><input type="text" name="emergency_relationship"></td>
                            </tr>
                             <tr class="applicant_address">
                                <td rowspan="1"><label>Address:</label></td>

                                <!-- Address fields in a single column -->
                                <td colspan="5">
                                    <!-- Street Input -->
                                    <div style="margin-bottom: 10px;">
                                        <input type="text" name="emergency_street" id="emergency_street" placeholder="Street/Building/Blck" style="width: 100%;" required>
                                    </div>

                                    <!-- Dropdowns: Region, Province, Municipality, Barangays -->
                                    <div style="display: flex; gap: 10px; justify-content: space-between;">
                                        <select name="emergency_region" id="emergency_region" onchange="loadProvinces('emergency')" required>
                                            <option value="">Region</option>
                                        </select>
                                        <select name="emergency_province" id="emergency_province" onchange="loadMunicipalities('emergency')" disabled required>
                                            <option value="">Province</option>
                                        </select>
                                        <select name="emergency_municipality" id="emergency_municipality" onchange="loadBarangays('emergency')" disabled required>
                                            <option value="">Municipality</option>
                                        </select>
                                        <select name="emergency_barangay" id="emergency_barangay" disabled required>
                                            <option value="">Barangays</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Contact Number</label></td>
                                <td><input type="text" name="emergency_number"></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Character Reference Table -->
                    <table class="form-table">
                        <thead>
                            <tr>
                                <th colspan="1">
                                    <h3>Character Reference</h3>
                                </th>
                                <th>
                                    <h3>Reference 1</h3>
                                </th>
                                <th>
                                    <h3>Reference 2</h3>
                                </th>
                                <th>
                                    <h3>Reference 3</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label>Name</label></td>
                                <td><input type="text" name="ref1_name"></td>
                                <td><input type="text" name="ref2_name"></td>
                                <td><input type="text" name="ref3_name"></td>
                            </tr>
                            <tr>
                                <td><label>Company</label></td>
                                <td><input type="text" name="ref1_company"></td>
                                <td><input type="text" name="ref2_company"></td>
                                <td><input type="text" name="ref3_company"></td>
                            </tr>
                            <tr>
                                <td><label>Position</label></td>
                                <td><input type="text" name="ref1_position"></td>
                                <td><input type="text" name="ref2_position"></td>
                                <td><input type="text" name="ref3_position"></td>
                            </tr>
                            <tr>
                                <td><label>Contact No.</label></td>
                                <td><input type="text" name="ref1_contact"></td>
                                <td><input type="text" name="ref2_contact"></td>
                                <td><input type="text" name="ref3_contact"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="form-table" style="text-align: right;">
                        <thead>
                            <tr>
                                <th colspan="6">
                                    <h3>Other Information</h3>
                                </th>
                            </tr>
                        </thead>

                        <tr>
                            <td><label>How did you learn about this position?</label></td>
                            <td><input type="text" name="how_did_you_learn"></td>

                            <td style="text-align: right;"><label>Do you have any relevant certifications or licenses?</label></td>
                            <td><input type="text" name="certifications" placeholder="e.g., NCII, TESDA, Red Cross"></td>
                        </tr>
                        <tr>
                            <td><label>Are you willing to work overtime?</label></td>
                            <td style="display: flex;">
                                <input type="radio" name="willing_overtime" value="yes" style="width:100px;">Yes
                                <input type="radio" name="willing_overtime" value="no" style="width:100px;">No
                            </td>

                            <td><label>Are you willing to travel?</label></td>
                            <td style="display: flex;">
                                <input type="radio" name="willing_travel" value="yes" style="width:100px;">Yes
                                <input type="radio" name="willing_travel" value="no" style="width:100px;">No
                            </td>
                        </tr>
                        <tr>
                            <td><label>Do you have a valid driver's license?</label></td>
                            <td style="display: flex;">
                                <input type="radio" name="has_driver_license" value="yes" style="width:100px;" onclick="toggleVehicleType(true)">Yes
                                <input type="radio" name="has_driver_license" value="no" style="width:100px;" onclick="toggleVehicleType(false)">No
                            </td>

                            <td id="vehicleLabel" style="display: none;"><label>If yes, what type of vehicle can you drive?</label></td>
                            <td id="vehicleInput" style="display: none;"><input type="text" name="vehicle_type" placeholder="e.g., Motorcycle, Car, Truck"></td>
                        </tr>

                        </tbody>
                    </table>
                    <table class="form-table">
                        <thead>
                            <th colspan="6">
                                <h3>Skills</h3>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><textarea style="height: 100px; width: 100%; resize: vertical; max-height:300px;" name="skills" id="skills" placeholder="Briefly describe your relevant skills. Include both technical and soft skills that are applicable to the role you're applying for. You may mention your proficiency level, tools or technologies you're familiar with, and how you've applied these skills in academic, professional, or personal settings."></textarea></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="navigation-box">
                    <div class="navigation">
                        <button onclick="showPage('page2')" type="button">Next</button>
                    </div>
                </div>
            </div>


            <!-- PAGE 2 -->
            <div id="page2" class="page">
                <div class="question-box">
                    <h2>Questionnaire for Applicant (1/5)</h2>
                    <label class="label">1. Please give a candid description of yourself as a person:</label>
                    <textarea name="description" rows="4" class="question-box" onblur="autoSave('description', this.value)"></textarea><br><br>
                </div>
                <div class="navigation-box">
                    <div class="navigation">
                        <button onclick="showPage('page1')" type="button">Previous</button>
                        <button onclick="validateAndNext('page2', 'page3')" type="button">Next</button>
                    </div>
                </div>
            </div>

            <!-- PAGE 3 -->
            <div id="page3" class="page">
                <div class="question-box">
                    <h2>Questionnaire for Applicant (2/5)</h2>
                    <label class="label">2. What is your job objective and career plans for the future?</label>
                    <textarea name="career_plans" rows="4" class="question-box" onblur="autoSave('career_plans', this.value)"></textarea><br><br>
                </div>
                <div class="navigation-box">
                    <div class="navigation">
                        <button onclick="showPage('page2')" type="button">Previous</button>
                        <button onclick="validateAndNext('page3', 'page4')" type="button">Next</button>
                    </div>
                </div>
            </div>

            <!-- PAGE 4 -->
            <div id="page4" class="page">
                <div class="question-box">
                    <h2>Questionnaire for Applicant (3/5)</h2>
                    <label class="label">3. Why do you want to join the teaching or office staff of EXACT Colleges of Asia?</label>
                    <textarea name="reason_for_joining" rows="4" class="question-box" onblur="autoSave('reason_for_joining', this.value)"></textarea><br><br>
                </div>
                <div class="navigation-box">
                    <div class="navigation">
                        <button onclick="showPage('page3')" type="button">Previous</button>
                        <button onclick="validateAndNext('page4', 'page5')" type="button">Next</button>
                    </div>
                </div>
            </div>

            <!-- PAGE 5 -->
            <div id="page5" class="page">
                <div class="question-box">
                    <h2>Questionnaire for Applicant (4/5)</h2>
                    <label class="label">4. Why do you think EXACT Inc. should hire you?</label>
                    <textarea name="why_hire" rows="4" class="question-box" onblur="autoSave('why_hire', this.value)"></textarea><br><br>
                </div>
                <div class="navigation-box">
                    <div class="navigation">
                        <button onclick="showPage('page4')" type="button">Previous</button>
                        <button onclick="validateAndNext('page5', 'page6')" type="button">Next</button>
                    </div>
                </div>
            </div>

            <!-- PAGE 6 -->
            <div id="page6" class="page">
                <div class="question-box">
                    <h2>Questionnaire for Applicant (5/5)</h2>
                    <label class="label">5. In case you are hired, what do you expect in return from EXACT Colleges of Asia?</label>
                    <textarea name="expectations" rows="4" class="question-box" onblur="autoSave('expectations', this.value)"></textarea><br><br>
                </div>
                <div class="navigation-box">
                    <div class="navigation">
                        <button onclick="showPage('page5')" type="button">Previous</button>
                        <button onclick="showPage('page7')" type="button">Next</button>
                    </div>
                </div>
            </div>

            <div id="page7" class="page">
                <h3>Upload Required Documents</h3>
                <div class="file-upload-container">
                    <div class="file-box">
                        <div class="file-upload-button" style="display: flex;">
                            <label class="custom-file-button">
                                <input type="file" id="2x2_pic" name="2x2_pic[]" multiple hidden onchange="updateFileList(this)">
                                Select Files
                            </label>
                            <button class="custom-file-button" onclick="startScan()">Scan File</button>
                            <div id="preview"></div>
                        </div>

                        <p>Formal Picture</p>
                        <div class="file-list" id="2x2_pic">
                            <p>No files selected</p>
                        </div>
                        </div>

                    <div class="file-box">
                        <div class="file-upload-button" style="display: flex;">
                            <label class="custom-file-button">
                                <input type="file" id="resume_applicant" name="resume_applicant[]" multiple hidden onchange="updateFileList(this)">
                                Select Files
                            </label>
                            <button class="custom-file-button" onclick="startScan()">Scan File</button>
                            <div id="preview"></div>
                        </div>

                        <p>Resume (Applicant)</p>
                        <div class="file-list" id="resumeList">
                            <p>No files selected</p>
                        </div>
                    </div>

                    <div class="file-box">
                        <div class="file-upload-button" style="display: flex;">
                            <label class="custom-file-button">
                                <input type="file" id="training_certificates" name="training_certificates[]" multiple hidden onchange="updateFileList(this)">
                                Select Files
                            </label>
                            <button class="custom-file-button" onclick="startScan()">Scan File</button>
                            <div id="preview"></div>
                        </div>
                        
                        <p>Training Certificates</p>
                        <div class="file-list" id="trainingList">
                            <p>No files selected</p>
                        </div>
                    </div>

                    <div class="file-box">
                        <div class="file-upload-button" style="display: flex;">
                            <label class="custom-file-button">
                                <input type="file" id="diploma" name="diploma[]" multiple hidden onchange="updateFileList(this)">
                                Select Files
                            </label>
                            <button class="custom-file-button" onclick="startScan()">Scan File</button>
                            <div id="preview"></div>
                        </div>

                        <p>Diploma</p>
                        <div class="file-list" id="diplomaList">
                            <p>No files selected</p>
                        </div>
                    </div>

                    <div class="file-box">
                       <div class="file-upload-button" style="display: flex;">
                            <label class="custom-file-button">
                                <input type="file" id="contracts" name="contracts[]" multiple hidden onchange="updateFileList(this)">
                                Select Files
                            </label>
                            <button class="custom-file-button" onclick="startScan()">Scan File</button>
                            <div id="preview"></div>
                        </div>

                        <p>Contracts</p>
                        <div class="file-list" id="contractsList">
                            <p>No files selected</p>
                        </div>
                    </div>


                    <div class="file-box">
                        <div class="file-upload-button" style="display: flex;">
                            <label class="custom-file-button">
                                <input type="file" id="transcript_of_records" name="transcript_of_records[]" multiple hidden onchange="updateFileList(this)">
                                Select Files
                            </label>
                            <button class="custom-file-button" onclick="startScan()">Scan File</button>
                            <div id="preview"></div>
                        </div>

                        <p>Transcript of Records (Bachelor, Masteral, and PhD)</p>
                        <div class="file-list" id="torList">
                            <p>No files selected</p>
                        </div>
                    </div>
                </div>

                <div class="navigation">
                    <button onclick="showPage('page6')" type="button">Previous</button>
                    <button type="button" onclick="handleSubmitClick()" id="submitButton">Submit</button>
                    
                </div>

                <div class="confirmation">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="certifyCheckbox" name="certify">
                        <span class="checkmark"></span>
                        I hereby certify that the above information is true and complete to the best of my knowledge.
                    </label>
                </div>

                <div id="checkboxMessage" class="validation-message" style="display: none;">
                    <small>Please check this box to confirm before submitting.</small>
                </div>
             </form>   
</body>
<script>
    function validateAndNext(currentPageId, nextPageId) {
        const textarea = document.querySelector(`#${currentPageId} textarea`);
        if (!textarea && textarea.value.trim() === "") {
            alert("Please complete the question before proceeding.");
            return;
        }
        showPage(nextPageId);
    }

    function autoSave(fieldName, value) {
        fetch('../handlers/autosave.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `field=${encodeURIComponent(fieldName)}&value=${encodeURIComponent(value)}`
        });
    }

    function toggleVehicleType(show) {
        const vehicleLabel = document.getElementById("vehicleLabel");
        const vehicleInput = document.getElementById("vehicleInput");
        const display = show ? "table-cell" : "none";

        vehicleLabel.style.display = display;
        vehicleInput.style.display = display;
    }

    function toggleCheckbox(clicked) {
        const checkboxes = document.getElementsByName("disability");
        checkboxes.forEach(cb => {
            if (cb !== clicked) cb.checked = false;
        });
    }

function handleSubmitClick() {
    const checkbox = document.getElementById('certifyCheckbox');
    const message = document.getElementById('checkboxMessage');
    const submitButton = document.getElementById('submitButton');

    if (checkbox.checked) {
        message.style.display = 'none';
        showModal(); // Proceed to show your modal
    } else {
        message.style.display = 'block';

        // Scroll to the checkbox or the submit button (choose one)
        // Option 1: Scroll to the checkbox area
        document.querySelector('.confirmation').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Option 2 (if you prefer to scroll to the submit button instead):
        // submitButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

    function startScan() {
      fetch("http://localhost:5000/scan")
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById("preview").innerHTML = `
              <p>Scanned: ${data.filename}</p>
              <img src="uploads/${data.filename}" width="300"><br>
              <a href="upload.php?file=${data.filename}">Upload to MongoDB</a>
            `;
          } else {
            alert("Scan failed: " + data.message);
          }
        });
    }
    // Array of page IDs in order
    const pages = ['page1', 'page2', 'page3', 'page4', 'page5', 'page6', 'page7'];
    let currentPageIndex = 0; // Starts at page1

    function showPage(pageId) {
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });

        document.getElementById(pageId).classList.add('active');

        // Update the current index based on the pageId
        currentPageIndex = pages.indexOf(pageId);

        // Update the page indicator
        document.getElementById('pageIndicator').innerText = `Page ${currentPageIndex + 1}/7`;
    }

    function nextPage() {
        if (currentPageIndex < pages.length - 1) {
            currentPageIndex++;
            showPage(pages[currentPageIndex]);
        }
    }

    function prevPage() {
        if (currentPageIndex > 0) {
            currentPageIndex--;
            showPage(pages[currentPageIndex]);
        }
    }


    const fileStore = new Map();

    function updateFileList(input) {
        const fileBox = input.closest('.file-box');
        const fileListContainer = fileBox.querySelector('.file-list');
        const inputName = input.name;

        let storedFiles = fileStore.get(inputName) || [];
        const newFiles = Array.from(input.files);

        storedFiles = storedFiles.concat(newFiles);

        // Remove duplicates
        storedFiles = storedFiles.filter((file, index, self) =>
            index === self.findIndex(f => f.name === file.name && f.size === file.size)
        );

        fileStore.set(inputName, storedFiles);

        const newDataTransfer = new DataTransfer();
        storedFiles.forEach(file => newDataTransfer.items.add(file));
        input.files = newDataTransfer.files;

        // Show file names
        fileListContainer.innerHTML = "";
        if (storedFiles.length > 0) {
            storedFiles.forEach((file, i) => {
                const fileItem = document.createElement("div");
                fileItem.classList.add("file-item");
                fileItem.innerHTML = `
                                <span>${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
                                <button type="button" class="remove-file" onclick="removeFile(this, '${inputName}', ${i})">❌</button>
                            `;
                fileListContainer.appendChild(fileItem);
            });
        } else {
            fileListContainer.innerHTML = "<p>No files selected</p>";
        }
    }

    function removeFile(button, inputName, index) {
        const fileBox = button.closest('.file-box');
        const input = fileBox.querySelector('input[type="file"]');
        let storedFiles = fileStore.get(inputName) || [];

        storedFiles.splice(index, 1);
        fileStore.set(inputName, storedFiles);

        const dataTransfer = new DataTransfer();
        storedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;

        updateFileList(input);
    }

    function showModal() {
        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function submitForm() {
        document.getElementById('empform').submit();
    }

    function closePrivacyModal() {
        document.getElementById("privacyModal").style.display = "none";
    }

    function exitForm() {
        window.location.href = 'index.php';

    }
    document.getElementById('birthdate').addEventListener('change', function() {
        const birthdate = new Date(this.value);
        const today = new Date();

        let age = today.getFullYear() - birthdate.getFullYear();
        const m = today.getMonth() - birthdate.getMonth();

        // Adjust if birth month/day hasn't occurred yet this year
        if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }

        document.getElementById('age').value = age;
    });
    window.onload = function() {
        loadRegions('personal');
        loadRegions('parent');
        loadRegions('emergency');
    };

    function loadRegions(type) {
        fetch('../handlers/get_regions.php')
            .then(response => response.json())
            .then(data => {
                const regionDropdown = document.getElementById(`${type}_region`);
                data.forEach(region => {
                    regionDropdown.innerHTML += `<option value="${region.id}">${region.name}</option>`;
                });
            });
    }

    function loadProvinces(type) {
        const regionId = document.getElementById(`${type}_region`).value;
        const provinceDropdown = document.getElementById(`${type}_province`);
        const municipalityDropdown = document.getElementById(`${type}_municipality`);
        const barangayDropdown = document.getElementById(`${type}_barangay`);

        provinceDropdown.innerHTML = `<option value="">Select Province</option>`;
        municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
        barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

        provinceDropdown.disabled = true;
        municipalityDropdown.disabled = true;
        barangayDropdown.disabled = true;

        if (regionId) {
            // Check if NCR (region_id 14) is selected
            if (parseInt(regionId) === 14) {
                // Skip province selection and go directly to municipalities
                loadMunicipalitiesFromRegion(type, regionId);
            } else {
                // Normal province loading for other regions
                fetch(`../handlers/get_provinces.php?region_id=${regionId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(province => {
                            provinceDropdown.innerHTML += `<option value="${province.id}">${province.name}</option>`;
                        });
                        provinceDropdown.disabled = false;
                    });
            }
        }
    }

    function loadMunicipalities(type) {
        const provinceId = document.getElementById(`${type}_province`).value;
        const municipalityDropdown = document.getElementById(`${type}_municipality`);
        const barangayDropdown = document.getElementById(`${type}_barangay`);

        municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
        barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

        barangayDropdown.disabled = true;

        if (provinceId) {
            fetch(`../handlers/get_municipalities.php?province_id=${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(municipality => {
                        municipalityDropdown.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
                    });
                    municipalityDropdown.disabled = false;
                });
        }
    }

    function loadBarangays(type) {
        const municipalityId = document.getElementById(`${type}_municipality`).value;
        const barangayDropdown = document.getElementById(`${type}_barangay`);

        barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;

        if (municipalityId) {
            fetch(`../handlers/get_barangays.php?municipality_id=${municipalityId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(barangay => {
                        barangayDropdown.innerHTML += `<option value="${barangay.id}">${barangay.name}</option>`;
                    });
                    barangayDropdown.disabled = false;
                });
        }
    }

    function loadMunicipalitiesFromRegion(type, regionId) {
        const municipalityDropdown = document.getElementById(`${type}_municipality`);
        const barangayDropdown = document.getElementById(`${type}_barangay`);

        municipalityDropdown.innerHTML = `<option value="">Select Municipality/City</option>`;
        barangayDropdown.innerHTML = `<option value="">Select Barangay</option>`;
        barangayDropdown.disabled = true;

        fetch(`../handlers/get_municipalities.php?region_id=${regionId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(municipality => {
                    municipalityDropdown.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
                });
                municipalityDropdown.disabled = false;
            });
    }
    fetch('../handlers/get_jobs.php')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('position_applied');
            data.forEach(job => {
                const option = document.createElement('option');
                option.value = job.position_title;
                option.textContent = job.position_title;
                select.appendChild(option);
            });
        })
        .catch(err => {
            console.error('Error loading job positions:', err);
            alert("Failed to load job positions.");
        });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('#employmentBody tr');
        rows.forEach((row, index) => {
            const actionCell = row.querySelector('.action-cell');
            if (!actionCell) return;

            // Clear existing button
            actionCell.innerHTML = '';

            if (rows.length > 1 && index !== 0) {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.textContent = 'Remove';
                removeBtn.onclick = function() {
                    row.remove();
                    updateRemoveButtons();
                };
                actionCell.appendChild(removeBtn);
            }
        });
    }

    function addRow() {
          console.log("Add row triggered");
        const tableBody = document.getElementById('employmentBody');
        const currentRows = tableBody.getElementsByTagName('tr').length;

        // if (currentRows >= 5) {
        //   alert('You can only add up to 5 employment records.');
        //   return;
        // }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
                <td><input type="text" name="company[]"></td>
                <td><input type="text" name="position[]"></td>
                <td><input type="text" name="reason_for_leaving[]"></td>
                <td class="action-cell"></td>
                `;
        tableBody.appendChild(newRow);
        updateRemoveButtons();
    }

    // Initial setup
    updateRemoveButtons();
</script>


</html>