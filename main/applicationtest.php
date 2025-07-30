<?php
include('../connection.php');
$collection = $database->selectCollection("applicants");

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = [];
if (!empty($search)) {
    $query = ['$or' => [
        ['firstname' => ['$regex' => $search, '$options' => 'i']],
        ['lastname' => ['$regex' => $search, '$options' => 'i']],
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
    <title>Applicants</title>
    <!-- <link rel="stylesheet" href="../styles.css"> -->
    <style>       
        .applicants-button {
            background-color: #00124d;
            border-left: 4px solid #ffffff;
        }

        .content {
    padding: 20px;
    box-sizing: border-box;
    width: 100%; /* Ensures full-width content */
}

table {
    width: 100%; /* Full width to fill the available space */
    max-width: 100%; /* Prevents content from shrinking */
    border-collapse: collapse;
    margin-top: 20px;
    background: #ffffff;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}


th, td {
    padding: 12px 15px;
    text-align: left;
    word-break: break-word; /* Ensures text wraps instead of expanding columns */
}

th {
    background-color: #00124d;
    color: #ffffff;
}

tr:nth-child(even) {
    background-color: #f8f8f8;
}

tr:hover {
    background-color: #f1f1f1;
}

td a {
    color: #0044cc;
    text-decoration: none;
    font-weight: bold;
}

td a:hover {
    background-color:rgb(181, 188, 202);
    padding: 7px;
    border-radius: 7px;
}

        /* Search Bar */
        .search-container {
            display: flex;
            align-items: center;
            max-width: 400px;
            margin-bottom: 15px;
            position: relative;
            margin-top: 20px
        }

        .search-container input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            padding-right: 30px;
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
            background-color:rgb(0, 94, 255);
        }

        /* Add Applicant Button */
        .add-applicant-button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }

        .add-applicant-button:hover {
            background-color: #218838;
        }

        /* Popup Overlay */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            position: relative;
        }

        .close-popup {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }
        .top-section {
    display: flex;
    justify-content: space-between; /* Ensures elements are on opposite sides */
    align-items: center; /* Aligns items vertically */
    margin-bottom: 15px; /* Spacing below the section */
    width: 100%; /* Full width to match table width */
    
}
.box-body {
    border-radius: 10px;
    border: 3px solid #a0a0a0;
    text-align: center;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    background-color: white;
    width: 100%;
}

/* Table styling */
.table-container {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #003366;
    color: white;
}

/* Search Bar */
.table-controls {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

#searchBar {
    width: 60%;
    padding: 8px;
    border: 2px solid #a0a0a0;
    border-radius: 5px;
}

#addApplicantBtn {
    background-color: #28a745;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
}
/* Box Body */
.box-body {
    border-radius: 10px;
    border: 3px solid #a0a0a0;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    background-color: white;
    width: 100%;
}

/* Table Container */
.table-container {
    width: 100%;
    overflow-x: auto;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

/* Table Header */
th {
    background-color: #00124d;
    color: white;
    padding: 12px;
    text-align: left;
}

/* Table Cells */
th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    word-break: break-word;
}

/* Row Hover */
tr:hover {
    background-color: #f1f1f1;
}

/* Action Links */
td a {
    color: #0044cc;
    font-weight: bold;
    text-decoration: none;
}

td a:hover {
    background-color: rgb(181, 188, 202);
    padding: 7px;
    border-radius: 7px;
}

/* Search Bar */
.search-container {
    display: flex;
    align-items: center;
    max-width: 400px;
    margin-bottom: 15px;
    margin-top: 20px;
}

.search-container input {
    flex: 1;
    padding: 8px;
    border: 2px solid #a0a0a0;
    border-radius: 5px;
    outline: none;
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
    background-color: rgb(0, 94, 255);
}


    </style>
    <script>
        function searchApplicants() {
            let searchValue = document.getElementById('searchInput').value;
            window.location.href = 'applicants.php?search=' + encodeURIComponent(searchValue);
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            window.location.href = 'applicants.php';
        }

        function openPopup() {
            document.getElementById('popupOverlay').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }
    </script>
</head>
<body>

<?php include 'sidebartest.php'; ?>
<div class="box-header">
<div class="content">
    <h2 class="header">Applicants</h2><br><br><br><br>
    <div class="top-section">
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search applicants..." value="<?php echo htmlspecialchars($search); ?>">
        <button onclick="clearSearch()">✕</button>
        <button onclick="searchApplicants()">Search</button>
    </div>

    <button class="add-applicant-button" onclick="openPopup()">+ Add Applicant</button>
</div>
    </div>
    <div id="popupOverlay" class="popup-overlay">
        <div class="popup-content">
            <span class="close-popup" onclick="closePopup()">&times;</span>
            <h2>Add Applicant</h2>
            <form action="add" method="POST">
                <input type="text" name="firstname" placeholder="First Name" required><br>
                <input type="text" name="lastname" placeholder="Last Name" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="text" name="phone" placeholder="Phone" required><br>
                <input type="text" name="position_applied" placeholder="Position Applied" required><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    
    <div class="box-body">
    <div class="table-container">
        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search applicants..." value="<?php echo htmlspecialchars($search); ?>">
            <button onclick="clearSearch()">✕</button>
            <button onclick="searchApplicants()">Search</button>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position Applied</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($applicant['firstname'] . ' ' . ($applicant['middlename'] ?? '') . ' ' . $applicant['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['phone']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['position_applied']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['status']); ?></td>
                        <td><a href="view_applicant.php?id=<?php echo $applicant['_id']; ?>">View</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>



