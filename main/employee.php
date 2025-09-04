<?php
include('../connection.php');
$collection = $database->selectCollection("employee");

$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = [];
if (!empty($search)) {
    $query = ['$or' => [
        ['personal_info.first_name' => ['$regex' => $search, '$options' => 'i']],
        ['personal_info.middle_name' => ['$regex' => $search, '$options' => 'i']],
        ['personal_info.last_name' => ['$regex' => $search, '$options' => 'i']],
        ['email' => ['$regex' => $search, '$options' => 'i']],
        ['position_applied' => ['$regex' => $search, '$options' => 'i']]
    ]];
}
$applicants = $collection->find($query);
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


        /* Search Bar */
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

        .search-container button:hover {
            background-color: #003080;
        }
        .search-container .clear-button {
            background-color:rgba(0, 47, 128, 0);
            color: black
        }
        .search-container .clear-button:hover {
            background-color:rgba(0, 47, 128, 0);
        }
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

        .clear-button:hover {
            color: black;
        }


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
        .add-applicant-button:hover {
            background-color: #003080; 
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }
     
                
    </style>
    
</head>
<body>

<?php include 'sidebar.php'; ?>
    <div class="header">Employee List</div><br><br><br>
<div class="content">

    <div class="box-header">
    <!-- <div class="top-section"> -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search employee..." value="<?php echo htmlspecialchars($search); ?>" oninput="toggleClearButton()">
            <button id="clearBtn" class="clear-button" onclick="clearSearch()">✕</button>
            <button onclick="searchApplicants()">Search</button>
        </div>
    <!-- <button class="add-applicant-button" onclick="addapplicant()">+ Add Applicant</button> -->

    <!-- </div> -->
</div>  
    <div class="box-body">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant)
                 { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($applicant['personal_info']['first_name'] . ' ' . ($applicant['personal_info']['middle_name'] ?? '') . ' ' . $applicant['personal_info']['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['personal_info']['email']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['personal_info']['contact']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['position_applied']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['status']); ?></td>
                        <td style="text-align: center">
                            <a href="view_employee.php?id=<?php echo $applicant['_id']; ?>">View</a>
                        </td>
                    </tr>
                <?php 
                } ?>
            </tbody>
        </table>
    </div>
    </div>
</div>
</body>
<script>
        function searchApplicants() {
    let searchValue = document.getElementById('searchInput').value;
    window.location.href = 'employee.php?search=' + encodeURIComponent(searchValue);
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearBtn').style.display = 'none';
    window.location.href = 'applicants.php';
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
