<?php
include('../../handlers/connection.php');
$employeesCol = $database->selectCollection("employee");
// 🔹 Store evaluations in performance_evaluations instead of evaluations
$evalsCol = $database->selectCollection("performance_evaluations");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("No employee selected.");
}

$emp = $employeesCol->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
if (!$emp) {
    die("Employee not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect scores
    $job_scores = $_POST['job'] ?? [];
    $class_scores = $_POST['class'] ?? [];
    $prep_scores = $_POST['prep'] ?? [];
    $dependres_scores = $_POST['dependres'] ?? [];
    $humanrel_scores = $_POST['humanrel'] ?? [];
    $jobcoop_scores = $_POST['jobcoop'] ?? [];
    $personal_scores = $_POST['personal'] ?? [];
    $attendance_scores = $_POST['attendance'] ?? [];

    // Average per category
    $job_avg = count($job_scores) ? array_sum($job_scores) / count($job_scores) : 0;
    $class_avg = count($class_scores) ? array_sum($class_scores) / count($class_scores) : 0;
    $prep_avg = count($prep_scores) ? array_sum($prep_scores) / count($prep_scores) : 0;
    $dependres_avg = count($dependres_scores) ? array_sum($dependres_scores) / count($dependres_scores) : 0;
    $humanrel_avg = count($humanrel_scores) ? array_sum($humanrel_scores) / count($humanrel_scores) : 0;
    $jobcoop_avg = count($jobcoop_scores) ? array_sum($jobcoop_scores) / count($jobcoop_scores) : 0;
    $personal_avg = count($personal_scores) ? array_sum($personal_scores) / count($personal_scores) : 0;
    $attendance_avg = count($attendance_scores) ? array_sum($attendance_scores) / count($attendance_scores) : 0;

    // Weighted scores
    $job_percent = $job_avg * 0.20;
    $class_percent = $class_avg * 0.20;
    $prep_percent = $prep_avg * 0.15;
    $dependres_percent = $dependres_avg * 0.10;
    $humanrel_percent = $humanrel_avg * 0.10;
    $jobcoop_percent = $jobcoop_avg * 0.10;
    $personal_percent = $personal_avg * 0.10;
    $attendance_percent = $attendance_avg * 0.05;

    $total = $job_percent + $class_percent + $prep_percent + $dependres_percent +
        $humanrel_percent + $jobcoop_percent + $personal_percent + $attendance_percent;

    // Convert numeric rating to category
    if ($total >= 4.1) {
        $category = "Outstanding";
    } elseif ($total >= 3.1) {
        $category = "Very Satisfactory";
    } elseif ($total >= 2.1) {
        $category = "Satisfactory";
    } elseif ($total >= 1.1) {
        $category = "Unsatisfactory";
    } else {
        $category = "Very Unsatisfactory";
    }

    // Store evaluation in performance_evaluations collection
    $evaluationData = [
        'employee_id' => new MongoDB\BSON\ObjectId($id),
        'evaluated_at' => new MongoDB\BSON\UTCDateTime(),
        'rating' => number_format($total, 2),
        'category' => $category,
        'sections' => [
            'job' => [
                'scores' => $job_scores,
                'average' => $job_avg,
                'percent' => $job_percent,
                'comments' => $_POST['job_comments'] ?? ''
            ],
            'class' => [
                'scores' => $class_scores,
                'average' => $class_avg,
                'percent' => $class_percent,
                'comments' => $_POST['class_comments'] ?? ''
            ],
            'prep' => [
                'scores' => $prep_scores,
                'average' => $prep_avg,
                'percent' => $prep_percent,
                'comments' => $_POST['prep_comments'] ?? ''
            ],
            'dependres' => [
                'scores' => $dependres_scores,
                'average' => $dependres_avg,
                'percent' => $dependres_percent,
                'comments' => $_POST['dependres_comments'] ?? ''
            ],
            'humanrel' => [
                'scores' => $humanrel_scores,
                'average' => $humanrel_avg,
                'percent' => $humanrel_percent,
                'comments' => $_POST['humanrel_comments'] ?? ''
            ],
            'jobcoop' => [
                'scores' => $jobcoop_scores,
                'average' => $jobcoop_avg,
                'percent' => $jobcoop_percent,
                'comments' => $_POST['jobcoop_comments'] ?? ''
            ],
            'personal' => [
                'scores' => $personal_scores,
                'average' => $personal_avg,
                'percent' => $personal_percent,
                'comments' => $_POST['personal_comments'] ?? ''
            ],
            'attendance' => [
                'scores' => $attendance_scores,
                'average' => $attendance_avg,
                'percent' => $attendance_percent,
                'comments' => $_POST['attendance_comments'] ?? ''
            ]
        ]
    ];

    $evalsCol->insertOne($evaluationData);

    header("Location: performance_appraisal.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Evaluate Employee</title>
    <style>
        :root {
            --primary-color: #00124d;
            --secondary-color: #001a66;
            --accent-color: #ffdd00;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --light-text: #fff;
            --border-color: #ddd;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            color: var(--dark-text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .header {
            background: #001a66;
            width: 100%;
            height: auto;
            padding: 20px;
            color: white;
            border-bottom: 5px solid #ffdd00; 
            z-index: 2;
            margin-bottom: 0px;
            margin-left: 245px;
            margin-top: 0px;
            position: fixed;
            font-size: 25px;
            font-weight: bold;
        }
        
        .main-content {
            display: flex;
            flex: 1;
            width: 100%;
        }
        
        .content {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            width: calc(100% - 250px);
            margin-top: 85px; /* Account for fixed header height */
        }
        
        .sidebar-container.collapsed ~ .content {
            margin-left: 60px;
            width: calc(100% - 60px);
        }
        
        .box-header {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            width: 100%;
            box-sizing: border-box;
        }
        
        .box-header h2 {
            color: var(--primary-color);
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .employee-info {
            display: flex;
            gap: 30px;
            margin-top: 15px;
            width: 100%;
        }
        
        .info-card {
            flex: 1;
            background: var(--light-bg);
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid var(--primary-color);
            box-sizing: border-box;
        }
        
        .box-body {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 100%;
            box-sizing: border-box;
        }
        
        .evaluation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .evaluation-header h3 {
            color: var(--primary-color);
            font-size: 22px;
        }
        
        .progress-container {
            background: var(--light-bg);
            border-radius: 20px;
            height: 12px;
            overflow: hidden;
            margin: 20px 0;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 20px;
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .page {
            display: none;
        }
        
        .page.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .section-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }
        
        .section-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-header h4 {
            font-size: 18px;
            font-weight: 500;
        }
        
        .section-weight {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            box-sizing: border-box;
        }
        
        .criteria-table th {
            background: var(--light-bg);
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .criteria-table td {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .criteria-text {
            width: 70%;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .rating-cell {
            width: 30%;
            text-align: center;
        }
        
        .radio-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
        }
        
        .radio-option {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .radio-option input {
            margin-bottom: 5px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .radio-option label {
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }
        
        .comments-section {
            padding: 20px;
            background: var(--light-bg);
            border-top: 1px solid var(--border-color);
        }
        
        .comments-section textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
            font-size: 15px;
        }
        
        .comments-section textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 18, 77, 0.2);
        }
        
        .summary-row {
            background: var(--light-bg);
            font-weight: 600;
        }
        
        .summary-row td {
            padding: 15px 20px;
        }
        
        .nav-controls {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline:hover {
            background: rgba(0, 18, 77, 0.05);
        }
        
        .btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Modal Styles */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: modalAppear 0.3s ease;
        }
        
        @keyframes modalAppear {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #ffffff;
            padding: 20px;
            font-weight: 600;
            font-size: 18px;
        }
        
        .modal-body {
            padding: 25px;
            max-height: 50vh;
            overflow: auto;
        }
        
        .modal-message {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .modal-list {
            margin: 15px 0;
        }
        
        .modal-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 10px;
            background: var(--light-bg);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }
        
        .modal-list-item span {
            font-weight: 500;
        }
        
        .modal-footer {
            padding: 15px 25px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            border-top: 1px solid #eee;
        }
        
        .hidden {
            display: none !important;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 15px;
                width: 100%;
            }
            
            .employee-info {
                flex-direction: column;
                gap: 15px;
            }
            
            .radio-group {
                flex-wrap: wrap;
            }
            
            .criteria-text, .rating-cell {
                width: 100%;
                display: block;
                text-align: left;
                margin-bottom: 10px;
            }
            
            .nav-controls {
                flex-direction: column;
            }
        }
    </style>
    <script>
        function calcAverage(section, totalItems) {
            let scores = [];
            for (let i = 0; i < totalItems; i++) {
                let checked = document.querySelector(`input[name="${section}[${i}]"]:checked`);
                if (checked) {
                    scores.push(parseInt(checked.value));
                }
            }
            if (scores.length === totalItems) {
                let sum = scores.reduce((a, b) => a + b, 0);
                let avg = (sum / totalItems).toFixed(2);
                document.getElementById(section + "_avg").innerText = avg;
                let percent = 0;
                if (section === "job") percent = (avg * 0.20).toFixed(2);
                if (section === "class") percent = (avg * 0.20).toFixed(2);
                if (section === "prep") percent = (avg * 0.15).toFixed(2);
                if (section === "dependres") percent = (avg * 0.10).toFixed(2);
                if (section === "humanrel") percent = (avg * 0.10).toFixed(2);
                if (section === "jobcoop") percent = (avg * 0.10).toFixed(2);
                if (section === "personal") percent = (avg * 0.10).toFixed(2);
                if (section === "attendance") percent = (avg * 0.05).toFixed(2);
                document.getElementById(section + "_percent").innerText = percent;
                
                // Update progress bar
                updateProgressBar();
            }
        }

        function updateProgressBar() {
            // This would calculate overall progress based on completed sections
            // For simplicity, we'll just incrementally fill as sections are completed
        }

        document.addEventListener('DOMContentLoaded', function() {
            const pages = Array.from(document.querySelectorAll('.page'));
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('evalForm');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalList = document.getElementById('modalList');
            const modalOk = document.getElementById('modalOk');
            const modalNo = document.getElementById('modalNo');
            const modalYes = document.getElementById('modalYes');
            let currentPageIndex = 0;

            function setRequiredForPage(pageEl, required) {
                const inputs = pageEl.querySelectorAll('input[type="radio"]');
                inputs.forEach(inp => {
                    if (required) {
                        inp.setAttribute('required', 'required');
                    } else {
                        inp.removeAttribute('required');
                    }
                });
            }

            function showPage(index) {
                pages.forEach((p, i) => {
                    p.classList.toggle('active', i === index);
                    setRequiredForPage(p, i === index);
                });
                prevBtn.style.display = index === 0 ? 'none' : 'inline-flex';
                nextBtn.style.display = index === pages.length - 1 ? 'none' : 'inline-flex';
                submitBtn.style.display = index === pages.length - 1 ? 'inline-flex' : 'none';
                
                // Update progress bar
                const progressPercent = ((index + 1) / pages.length) * 100;
                document.querySelector('.progress-bar').style.width = progressPercent + '%';
            }

            prevBtn.addEventListener('click', function() {
                if (currentPageIndex > 0) {
                    currentPageIndex -= 1;
                    showPage(currentPageIndex);
                }
            });

            nextBtn.addEventListener('click', function() {
                // Optionally, ensure current page complete before next
                currentPageIndex = Math.min(currentPageIndex + 1, pages.length - 1);
                showPage(currentPageIndex);
            });

            function openModal() {
                modalBackdrop.style.display = 'flex';
            }

            function closeModal() {
                modalBackdrop.style.display = 'none';
            }

            function getIncompleteSections() {
                const sections = [{
                        key: 'job',
                        label: 'Job Knowledge & Skills',
                        count: 5,
                        pageIndex: 0
                    },
                    {
                        key: 'class',
                        label: 'Classroom Effectiveness',
                        count: 5,
                        pageIndex: 1
                    },
                    {
                        key: 'prep',
                        label: 'Preparation & Use of Instructional Materials',
                        count: 5,
                        pageIndex: 2
                    },
                    {
                        key: 'dependres',
                        label: 'Dependability & Resourcefullness',
                        count: 5,
                        pageIndex: 3
                    },
                    {
                        key: 'humanrel',
                        label: 'Human Relations',
                        count: (function() {
                            const list = document.querySelectorAll('input[name^="humanrel["]');
                            // Count unique indexes for humanrel
                            const idxs = new Set();
                            list.forEach(inp => {
                                const m = inp.name.match(/humanrel\[(\d+)\]/);
                                if (m) idxs.add(m[1]);
                            });
                            return idxs.size || 0;
                        })(),
                        pageIndex: 4
                    },
                    {
                        key: 'jobcoop',
                        label: 'Job Attitude/Cooperation',
                        count: 5,
                        pageIndex: 5
                    },
                    {
                        key: 'personal',
                        label: 'Personal Qualities',
                        count: 5,
                        pageIndex: 6
                    },
                    {
                        key: 'attendance',
                        label: 'Attendance & Punctuality',
                        count: 5,
                        pageIndex: 7
                    }
                ];
                const incomplete = [];
                sections.forEach(s => {
                    let selected = 0;
                    for (let i = 0; i < s.count; i++) {
                        if (document.querySelector(`input[name="${s.key}[${i}]"]:checked`)) {
                            selected++;
                        }
                    }
                    if (selected < s.count) incomplete.push({
                        label: s.label,
                        pageIndex: s.pageIndex
                    });
                });
                return incomplete;
            }

            function showIncompleteModal(missing) {
                modalTitle.textContent = 'Incomplete Evaluation';
                modalMessage.textContent = 'Please complete all ratings. Missing sections:';
                
                // Clear and populate modal list
                modalList.innerHTML = '';
                missing.forEach(m => {
                    const listItem = document.createElement('div');
                    listItem.className = 'modal-list-item';
                    listItem.innerHTML = `
                        <span>${m.label}</span>
                        <button type="button" class="btn btn-outline btn-small" onclick="jumpToPage(${m.pageIndex})">
                            Jump to page
                        </button>
                    `;
                    modalList.appendChild(listItem);
                });
                
                modalOk.classList.remove('hidden');
                modalNo.classList.add('hidden');
                modalYes.classList.add('hidden');
                openModal();
            }

            function showConfirmModal() {
                modalTitle.textContent = 'Submit Evaluation';
                modalMessage.textContent = 'Are you sure you want to submit this evaluation?';
                modalList.innerHTML = '';
                modalOk.classList.add('hidden');
                modalNo.classList.remove('hidden');
                modalYes.classList.remove('hidden');
                openModal();
            }

            // Make jumpToPage function globally accessible
            window.jumpToPage = function(pageIndex) {
                currentPageIndex = pageIndex;
                showPage(currentPageIndex);
                closeModal();
            };

            modalOk.addEventListener('click', function() {
                closeModal();
            });
            modalNo.addEventListener('click', function() {
                closeModal();
            });
            modalYes.addEventListener('click', function() {
                closeModal();
                form.submit();
            });

            form.addEventListener('submit', function(e) {
                // Only manage custom flow on last page
                if (currentPageIndex === pages.length - 1) {
                    e.preventDefault();
                    const missing = getIncompleteSections();
                    if (missing.length > 0) {
                        showIncompleteModal(missing);
                        return false;
                    }
                    showConfirmModal();
                    return false;
                }
            });

            // Initialize
            pages.forEach(p => setRequiredForPage(p, false));
            showPage(currentPageIndex);
        });
    </script>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="header">Employee Performance Evaluation</div>
    
    <div class="main-content">
        <div class="content">
            <div class="box-header">
                <h2>Employee Performance Evaluation</h2>
                <div class="employee-info">
                    <div class="info-card">
                        <h3>Employee Information</h3>
                        <p><strong>Name:</strong> <?php
                            echo htmlspecialchars($emp['personal_info']['first_name'] ?? '') . " " .
                                htmlspecialchars($emp['personal_info']['middle_name'] ?? '') . " " .
                                htmlspecialchars($emp['personal_info']['last_name'] ?? '');
                            ?></p>
                        <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($emp['employee_id'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-card">
                        <h3>Department Information</h3>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($emp['department'] ?? 'N/A'); ?></p>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($emp['position'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar"></div>
                </div>
                <p>Section 1 of 8</p>
            </div>
            
            <div class="box-body">
                <form method="POST" id="evalForm" style="width: 100%;">
                    <!-- JOB KNOWLEDGE -->
                    <div class="page active" data-section="job">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Job Knowledge & Skills</h4>
                                <div class="section-weight">Weight: 20%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $job_criteria = [
                                        "Has thorough knowledge and understanding of handled course/s",
                                        "Brings in useful information connecting lessons to actual experiences",
                                        "Discusses and analyzes subject matter effectively",
                                        "Objectives of the course are clearly stated and attained",
                                        "Prepares well constructed assessment/test items"
                                    ];
                                    foreach ($job_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='job[$i]' value='$j' id='job_{$i}_{$j}' onchange='calcAverage(\"job\",5)'>
                                                <label for='job_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="job_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="job_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="job_comments" placeholder="Additional comments for Job Knowledge & Skills..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- CLASSROOM EFFECTIVENESS -->
                    <div class="page" data-section="class">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Classroom Effectiveness</h4>
                                <div class="section-weight">Weight: 20%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $class_criteria = [
                                        "Provides organized delivery of instruction",
                                        "Elicits participation through critical/logic questions",
                                        "Uses supplementary materials and varied activities",
                                        "Provides instruction consistent with course goals",
                                        "Presents examples and illustrations effectively"
                                    ];
                                    foreach ($class_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='class[$i]' value='$j' id='class_{$i}_{$j}' onchange='calcAverage(\"class\",5)'>
                                                <label for='class_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="class_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="class_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="class_comments" placeholder="Additional comments for Classroom Effectiveness..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- PREPARATION -->
                    <div class="page" data-section="prep">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Preparation & Use of Instructional Materials</h4>
                                <div class="section-weight">Weight: 15%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $prep_criteria = [
                                        "Instructional materials are clear and presentable",
                                        "Makes effective use of teaching aids",
                                        "Improves course contents and methods",
                                        "Revises and updates materials",
                                        "Coordinates modifications with training manager"
                                    ];
                                    foreach ($prep_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='prep[$i]' value='$j' id='prep_{$i}_{$j}' onchange='calcAverage(\"prep\",5)'>
                                                <label for='prep_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="prep_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="prep_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="prep_comments" placeholder="Additional comments for Preparation & Use of Instructional Materials..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- DEPENDABILITY & RESOURCEFULNESS -->
                    <div class="page" data-section="dependres">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Dependability & Resourcefulness</h4>
                                <div class="section-weight">Weight: 10%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dependres_criteria = [
                                        "A self-starter with outstanding initiative. Always makes worthwhile suggestions and ideas",
                                        "Exerts effort to enhance his knowledge, skills and work methods.",
                                        "Makes an attempt to work beyond what is required of him such as helping in the development of manual/hand-outs.",
                                        "Highly dependable under most circumstances and performs work assignments without the need for checking.",
                                        "Readily accepts additional load or work assignments without complain"
                                    ];
                                    foreach ($dependres_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='dependres[$i]' value='$j' id='dependres_{$i}_{$j}' onchange='calcAverage(\"dependres\",5)'>
                                                <label for='dependres_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="dependres_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="dependres_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="dependres_comments" placeholder="Additional comments for Dependability & Resourcefulness..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- HUMAN RELATIONS -->
                    <div class="page" data-section="humanrel">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Human Relations</h4>
                                <div class="section-weight">Weight: 10%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $humanrel_criteria = [
                                        // Section 1
                                        ["section" => "Trainees/Students"],
                                        "Relates to trainees in ways which promotes mutual respect",
                                        "Has good rapport with trainees.",
                                        // Section 2
                                        ["section" => "Other Employees and Superior"],
                                        "Maintains harmonious relationship with co-workers or other employees.",
                                        "Easily deals with people with whom he/she works and comes in contact.",
                                        "Shows respect to subordinate, colleagues and superior."
                                    ];

                                    // Only count criteria that are not section labels
                                    $humanrel_criteria_count = 0;
                                    foreach ($humanrel_criteria as $c) {
                                        if (!is_array($c)) $humanrel_criteria_count++;
                                    }
                                    $humanrel_index = 0;
                                    foreach ($humanrel_criteria as $i => $c) {
                                        if (is_array($c) && isset($c['section'])) {
                                            // Section label row
                                            echo "<tr><td colspan='2'><strong>{$c['section']}</strong></td></tr>";
                                        } else {
                                            // Criteria with radio buttons
                                            echo "<tr>
                                                <td class='criteria-text'>$c</td>
                                                <td class='rating-cell'>
                                                    <div class='radio-group'>";
                                            for ($j = 1; $j <= 5; $j++) {
                                                echo "<div class='radio-option'>
                                                    <input type='radio' name='humanrel[$humanrel_index]' value='$j' id='humanrel_{$humanrel_index}_{$j}' onchange='calcAverage(\"humanrel\",$humanrel_criteria_count)'>
                                                    <label for='humanrel_{$humanrel_index}_{$j}'>$j</label>
                                                </div>";
                                            }
                                            echo "          </div>
                                                </td>
                                            </tr>";
                                            $humanrel_index++;
                                        }
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="humanrel_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="humanrel_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="humanrel_comments" placeholder="Additional comments for Human Relations..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- JOB ATTITUDE/COOPERATION -->
                    <div class="page" data-section="jobcoop">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Job Attitude/Cooperation</h4>
                                <div class="section-weight">Weight: 10%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $jobcoop_criteria = [
                                        "Gives whole-hearted cooperation with others and his superiors towards the attainment of corporate goal.",
                                        "Shows active participation in various activities of the Training Centre.",
                                        "Shows enthusiasm for teaching the course and doing assigned tasks.",
                                        "Shows positive work attitude at all times.",
                                        "Regularly coordinates and reports necessary information, or inquiries to concerned personnel and/or department."
                                    ];
                                    foreach ($jobcoop_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='jobcoop[$i]' value='$j' id='jobcoop_{$i}_{$j}' onchange='calcAverage(\"jobcoop\",5)'>
                                                <label for='jobcoop_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="jobcoop_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="jobcoop_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="jobcoop_comments" placeholder="Additional comments for Job Attitude/Cooperation..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- PERSONAL QUALITIES -->
                    <div class="page" data-section="personal">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Personal Qualities</h4>
                                <div class="section-weight">Weight: 10%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $personal_criteria = [
                                        "Has very respectable personality and appearance.",
                                        "Showed evidence of self-confidence when teaching a class.",
                                        "Has a high level of patience as an instructor.",
                                        "Shows honesty in all dealings related to his/her work.",
                                        "Good communication skills and displays reasonable judgment."
                                    ];
                                    foreach ($personal_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='personal[$i]' value='$j' id='personal_{$i}_{$j}' onchange='calcAverage(\"personal\",5)'>
                                                <label for='personal_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="personal_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="personal_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="personal_comments" placeholder="Additional comments for Personal Qualities..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ATTENDANCE & PUNCTUALITY -->
                    <div class="page" data-section="attendance">
                        <div class="section-card">
                            <div class="section-header">
                                <h4>Attendance & Punctuality</h4>
                                <div class="section-weight">Weight: 5%</div>
                            </div>
                            <table class="criteria-table">
                                <thead>
                                    <tr>
                                        <th>Criteria</th>
                                        <th>Rating (1-5)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $attendance_criteria = [
                                        "Shows punctuality in observing work hours.",
                                        "Good attendance record. Rarely absent to work.",
                                        "Never leave post without any permission or a substitute co-worker.",
                                        "Regularly attends, faculty meetings or other related function of the organization as required by his/her superior.",
                                        "Promptly return to his/her class after break time."
                                    ];
                                    foreach ($attendance_criteria as $i => $c) {
                                        echo "<tr>
                                            <td class='criteria-text'>$c</td>
                                            <td class='rating-cell'>
                                                <div class='radio-group'>";
                                        for ($j = 1; $j <= 5; $j++) {
                                            echo "<div class='radio-option'>
                                                <input type='radio' name='attendance[$i]' value='$j' id='attendance_{$i}_{$j}' onchange='calcAverage(\"attendance\",5)'>
                                                <label for='attendance_{$i}_{$j}'>$j</label>
                                            </div>";
                                        }
                                        echo "      </div>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                    <tr class="summary-row">
                                        <td colspan="2">
                                            <strong>Average:</strong> <span id="attendance_avg">0.00</span> | 
                                            <strong>Weighted Score:</strong> <span id="attendance_percent">0.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="comments-section">
                                <textarea name="attendance_comments" placeholder="Additional comments for Attendance & Punctuality..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="nav-controls">
                        <button type="button" id="prevBtn" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <div style="flex:1"></div>
                        <button type="button" id="nextBtn" class="btn btn-primary">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-primary" style="display:none;">
                            <i class="fas fa-paper-plane"></i> Submit Evaluation
                        </button>
                    </div>
                </form>

                <!-- Modal Markup -->
                <div class="modal-backdrop" id="modalBackdrop">
                    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                        <div class="modal-header" id="modalTitle">Modal</div>
                        <div class="modal-body">
                            <div class="modal-message" id="modalMessage">Message</div>
                            <div class="modal-list" id="modalList"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="modalYes">Yes</button>
                            <button type="button" class="btn btn-secondary" id="modalNo">No</button>
                            <button type="button" class="btn btn-primary" id="modalOk">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/all.js"></script>
</body>

</html>