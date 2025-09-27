<?php
include('../connection.php');
$collection = $database->selectCollection("employee");

$search = isset($_GET['search']) ? $_GET['search'] : '';
$selectedDepartment = isset($_GET['department']) ? $_GET['department'] : '';

// =============================
// Define headers
// =============================

// Headers for overview (All Departments) and default fallback (no dept-specific mapping)
$overviewHeaders = ["Department", "Full Name", "Email", "Phone", "Position", "Action"];
$defaultHeaders = ["Full Name", "Email", "Phone", "Position", "Action"];

// Department-specific headers
$departmentHeaders = [
    "School Administrators" => ["Name", "Position/Designation", "Highest Educational Attainment", "Professional License No.", "Status", "Action"],
    "Non-Teaching Personnel" => ["Name", "Position/Designation","When and Where Obtained","Professional License No. and Expiration Date", "Highest Educational Attainment", "Status", "Action"],
    "Faculty - Maritime" => ["Name", "Position/Designation", "Highest Educational Attainment", "Professional License", "Sea Experience", "Teaching Experience","Trainings", "Subject Taught","Status of Appointment","Status of Appointment","Nature of Appointment", "Action"],
    "Faculty - Nursing" => ["Name", "Position/Designation", "Highest Educational Attainment", "Professional License", "Teaching Experience", "Subjects Handled", "Status", "Action"],
    "Faculty - Education" => ["Name", "Position/Designation", "Bachelor's Degree", "Master’s/PhD", "Professional License", "Teaching Experience", "Subjects Handled", "Status", "Action"],
    "Faculty - Business" => ["Name", "Position/Designation", "Bachelor's Degree", "Master’s/PhD", "Professional License", "Teaching Experience", "Subjects Handled", "Status", "Action"],
    "Faculty - Criminology" => ["Name", "Position/Designation", "Bachelor's Degree", "Master’s/PhD", "Professional License", "Teaching Experience", "Subjects Handled", "Status", "Action"],
    "Faculty - Information System" => ["Name", "Position/Designation", "Bachelor's Degree", "Master’s/PhD", "Professional License", "Teaching Experience", "Subjects Handled", "Status", "Action"],
];

// Pick headers
$isOverview = empty($selectedDepartment);
$headers = $isOverview ? $overviewHeaders : ($departmentHeaders[$selectedDepartment] ?? $defaultHeaders);

// =============================
// Query employees
// =============================
$query = [];
if (!empty($search)) {
    $query['$or'] = [
        ['personal_info.first_name' => ['$regex' => $search, '$options' => 'i']],
        ['personal_info.middle_name' => ['$regex' => $search, '$options' => 'i']],
        ['personal_info.last_name' => ['$regex' => $search, '$options' => 'i']],
        ['email' => ['$regex' => $search, '$options' => 'i']],
        ['position_applied' => ['$regex' => $search, '$options' => 'i']]
    ];
}
if (!empty($selectedDepartment)) {
    $query['department'] = $selectedDepartment;
}

// Fetch list of departments
$departments = [];
try {
    $distinct = $collection->distinct('department');
    if (is_array($distinct)) {
        $departments = array_values(array_filter(array_map(function($v){ return is_string($v) ? trim($v) : '';}, $distinct), function($v){ return $v !== ''; }));
        sort($departments, SORT_NATURAL | SORT_FLAG_CASE);
    }
} catch (Exception $e) {
    $departments = [];
}

$cursor = $collection->find($query);

// Group employees by department
$groupedByDepartment = [];
foreach ($cursor as $doc) {
    $deptName = isset($doc['department']) && $doc['department'] !== '' ? $doc['department'] : 'Unspecified';
    if (!isset($groupedByDepartment[$deptName])) {
        $groupedByDepartment[$deptName] = [];
    }
    $groupedByDepartment[$deptName][] = $doc;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <style>       
        .employee-button {
            background-color: #00124d;
            border-left: 4px solid #ffffff;
        }
        .search-container {
            display: flex;
            align-items: center;
            max-width:  500px;
            margin-bottom: 5px;
            position: relative;
            margin-top: 5px;
            padding-left: 5px;
        }
        .search-container input {
            flex: 1;
            padding:14px 100px 14px 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            width: 100%;
        }
        .search-container button {
            background-color: #00124d;
            color: #ffffff;
            border: none;
            padding: 8px 15px;
            margin-left: 5px;
            cursor: pointer;
            border-radius: 5px;
        }
        .search-container button:hover { background-color: #003080; }
        .clear-button {
            position: absolute;
            right: 75px;
            background: none;
            border: none;
            font-size: 20px; 
            color: lightgray;
            cursor: pointer;
            display: none;
            font-weight: bold;
        }
        .clear-button:hover { color: black; }
        .add-applicant-button {
            background-color: #00124d; 
            color: #ffffff; 
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            margin-top: 4px;
        }
        .add-applicant-button:hover { background-color: #003080; }

        /* Loading Popup Styles */
        .loading-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(3px);
        }

        .loading-content {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: popupSlideIn 0.3s ease-out;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00124d;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .loading-text {
            font-size: 18px;
            color: #00124d;
            font-weight: 600;
            margin: 0;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="header">Employee List</div><br><br><br>
<div class="content">

    <div class="box-header">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search employee..." value="<?php echo htmlspecialchars($search); ?>" oninput="toggleClearButton()">
            <button id="clearBtn" class="clear-button" onclick="clearSearch()">✕</button>
            <select id="departmentSelect" style="margin-left:6px; padding: 12px 8px; border:1px solid #ccc; border-radius:5px;">
                <option value="">All Departments (Overview)</option>
                <?php foreach ($departments as $dept) { ?>
                    <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $selectedDepartment === $dept ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept); ?></option>
                <?php } ?>
            </select>
            <button onclick="searchApplicants()">Search</button>
        </div>
    </div>  

    <div class="box-body">
    <?php if (empty($groupedByDepartment)) { ?>
        <p style="padding:10px; color:#666;">No employees found.</p>
    <?php } ?>
    <?php if ($isOverview) { ?>
        <div class="table-container" style="margin-bottom:20px;">
            <table>
                <thead>
                    <tr>
                        <?php foreach ($headers as $header) { ?>
                            <th><?php echo htmlspecialchars($header); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($groupedByDepartment as $deptName => $employees) { ?>
                    <?php foreach ($employees as $applicant) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($deptName); ?></td>
                            <td><?php echo htmlspecialchars(($applicant['personal_info']['first_name'] ?? '') . ' ' . ($applicant['personal_info']['middle_name'] ?? '') . ' ' . ($applicant['personal_info']['last_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($applicant['personal_info']['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($applicant['personal_info']['contact'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($applicant['position_applied'] ?? ''); ?></td>
                            <td style="text-align:center"><a class="btn-view" href="view_employee.php?id=<?php echo $applicant['_id']; ?>">View</a></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <?php foreach ($groupedByDepartment as $deptName => $employees) { ?>
            <div class="table-container" style="margin-bottom:20px;">
                <div style="background:#00124d; color:#fff; padding:10px 12px; font-weight:bold; border-radius:6px;">
                    <?php echo htmlspecialchars($deptName); ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($headers as $header) { ?>
                                <th><?php echo htmlspecialchars($header); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($employees as $applicant) { ?>
                        <tr>
                            <?php foreach ($headers as $header) { ?>
                                <?php if ($header === "Full Name" || $header === "Name") { ?>
                                    <td><?php echo htmlspecialchars(($applicant['personal_info']['first_name'] ?? '') . ' ' . ($applicant['personal_info']['middle_name'] ?? '') . ' ' . ($applicant['personal_info']['last_name'] ?? '')); ?></td>
                                <?php } elseif ($header === "Email") { ?>
                                    <td><?php echo htmlspecialchars($applicant['personal_info']['email'] ?? ''); ?></td>
                                <?php } elseif ($header === "Phone") { ?>
                                    <td><?php echo htmlspecialchars($applicant['personal_info']['contact'] ?? ''); ?></td>
                                <?php } elseif ($header === "Position" || $header === "Position/Designation") { ?>
                                    <td><?php echo htmlspecialchars($applicant['position_applied'] ?? ''); ?></td>
                                <?php } elseif ($header === "Action") { ?>
                                    <td style="text-align:center"><a href="view_employee.php?id=<?php echo $applicant['_id']; ?>">View</a></td>
                                <?php } else { ?>
                                    <td>—</td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    <?php } ?>
    </div>
</div>

<!-- Loading Popup -->
<div id="loadingPopup" class="loading-popup">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p class="loading-text">Filtering...</p>
    </div>
</div>

</body>
<script>
function showLoadingPopup() {
    document.getElementById('loadingPopup').style.display = 'flex';
}

function hideLoadingPopup() {
    document.getElementById('loadingPopup').style.display = 'none';
}

function searchApplicants() {
    showLoadingPopup();
    let searchValue = document.getElementById('searchInput').value;
    let dept = document.getElementById('departmentSelect').value;
    let params = new URLSearchParams();
    if (searchValue) params.set('search', searchValue);
    if (dept) params.set('department', dept);
    window.location.href = 'employee.php' + (params.toString() ? ('?' + params.toString()) : '');
}

function clearSearch() {
    showLoadingPopup();
    document.getElementById('searchInput').value = '';
    document.getElementById('clearBtn').style.display = 'none';
    document.getElementById('departmentSelect').value = '';
    window.location.href = 'employee.php';
}

function toggleClearButton() {
    let searchInput = document.getElementById('searchInput');
    let clearBtn = document.getElementById('clearBtn');
    clearBtn.style.display = searchInput.value.trim() ? 'block' : 'none';
}

window.onload = function () {
    toggleClearButton();
};
function addapplicant(){
    window.location.href = 'application_form.php';
}
</script>
</html>
