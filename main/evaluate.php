<?php
include('../connection.php');
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
    .performance-button {
    background-color: #00124d;
    border-left: 4px solid #ffffff;
  }
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #00124d; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; 
        }
        th.rating, td.rating {
            width: 120px;
            min-width: 120px;
            max-width: 120px;
            text-align: center;
        }
        th, td {
            vertical-align: middle;
        }
        .submit-btn {
            background-color: #00124d; color: white; padding: 10px 15px;
            border: none; border-radius: 5px; cursor: pointer;
        }
        .radio-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }
        .radio-group label {
            min-width: 36px;
            text-align: center;
            display: inline-block;
        }
        input[type="radio"] {
            margin-right: 3px;
            width: 18px;
            height: 18px;
        }
                .performance-button {
            background-color: #00124d;
            border-left: 4px solid #ffffff;
        }

        .rating{ 
                width: 30%;
            }

        .criteria{
            width: 70%;
        }
        /* Pagination */
        .page { display: none; }
        .page.active { display: block; }
        .nav-controls { display: flex; justify-content: space-between; gap: 10px; margin-top: 10px; }
        .btn { background-color: #00124d; color: #ffffff; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn[disabled] { opacity: 0.6; cursor: not-allowed; }
        /* Modal */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal { background: #ffffff; border-radius: 8px; width: 100%; max-width: 520px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; }
        .modal-header { background: #00124d; color: #ffffff; padding: 14px 16px; font-weight: bold; }
        .modal-body { padding: 16px; max-height: 50vh; overflow: auto; }
        .modal-footer { padding: 12px 16px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #eee; }
        .btn-secondary { background-color: #6b7280; }
        .list { margin: 8px 0 0 18px; }
        .list li { margin: 6px 0; display: flex; align-items: center; gap: 8px; }
        .btn-small { padding: 6px 10px; font-size: 12px; border-radius: 4px; }
        .hidden { display: none !important; }
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
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
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
                prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
                nextBtn.style.display = index === pages.length - 1 ? 'none' : 'inline-block';
                submitBtn.style.display = index === pages.length - 1 ? 'inline-block' : 'none';
            }

            prevBtn.addEventListener('click', function () {
                if (currentPageIndex > 0) {
                    currentPageIndex -= 1;
                    showPage(currentPageIndex);
                }
            });

            nextBtn.addEventListener('click', function () {
                // Optionally, ensure current page complete before next
                currentPageIndex = Math.min(currentPageIndex + 1, pages.length - 1);
                showPage(currentPageIndex);
            });

            function openModal() { modalBackdrop.style.display = 'flex'; }
            function closeModal() { modalBackdrop.style.display = 'none'; }

            function getIncompleteSections() {
                const sections = [
                    { key: 'job', label: 'Job Knowledge & Skills', count: 5, pageIndex: 0 },
                    { key: 'class', label: 'Classroom Effectiveness', count: 5, pageIndex: 1 },
                    { key: 'prep', label: 'Preparation & Use of Instructional Materials', count: 5, pageIndex: 2 },
                    { key: 'dependres', label: 'Dependability & Resourcefullness', count: 5, pageIndex: 3 },
                    { key: 'humanrel', label: 'Human Relations', count: (function(){
                        const list = document.querySelectorAll('input[name^="humanrel["]');
                        // Count unique indexes for humanrel
                        const idxs = new Set();
                        list.forEach(inp => {
                            const m = inp.name.match(/humanrel\[(\d+)\]/);
                            if (m) idxs.add(m[1]);
                        });
                        return idxs.size || 0;
                    })(), pageIndex: 4 },
                    { key: 'jobcoop', label: 'Job Attitude/Cooperation', count: 5, pageIndex: 5 },
                    { key: 'personal', label: 'Personal Qualities', count: 5, pageIndex: 6 },
                    { key: 'attendance', label: 'Attendance & Punctuality', count: 5, pageIndex: 7 }
                ];
                const incomplete = [];
                sections.forEach(s => {
                    let selected = 0;
                    for (let i = 0; i < s.count; i++) {
                        if (document.querySelector(`input[name="${s.key}[${i}]"]:checked`)) {
                            selected++;
                        }
                    }
                    if (selected < s.count) incomplete.push({ label: s.label, pageIndex: s.pageIndex });
                });
                return incomplete;
            }

            function showIncompleteModal(missing) {
                modalTitle.textContent = 'Incomplete Evaluation';
                modalMessage.textContent = 'Please complete all ratings. Missing sections:';
                modalList.innerHTML = '';
                missing.forEach(m => {
                    const li = document.createElement('li');
                    const span = document.createElement('span');
                    span.textContent = m.label;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-small';
                    btn.textContent = 'Jump to page';
                    btn.addEventListener('click', function(){
                        currentPageIndex = m.pageIndex;
                        showPage(currentPageIndex);
                        closeModal();
                    });
                    li.appendChild(span);
                    li.appendChild(btn);
                    modalList.appendChild(li);
                });
                modalOk.classList.remove('hidden');
                modalNo.classList.add('hidden');
                modalYes.classList.add('hidden');
                openModal();
            }

            function showConfirmModal() {
                modalTitle.textContent = 'Submit Evaluation';
                modalMessage.textContent = 'Are you sure you want to submit?';
                modalList.innerHTML = '';
                modalOk.classList.add('hidden');
                modalNo.classList.remove('hidden');
                modalYes.classList.remove('hidden');
                openModal();
            }

            modalOk.addEventListener('click', function(){ closeModal(); });
            modalNo.addEventListener('click', function(){ closeModal(); });
            modalYes.addEventListener('click', function(){
                closeModal();
                form.submit();
            });

            form.addEventListener('submit', function (e) {
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
    <div class="header">Employee Evaluation</div><br><br><br>
<div class="content">
    <div class="box-header">
        <h2>Evaluate: <?php 
            echo ($emp['personal_info']['first_name'] ?? '') . " " .
                ($emp['personal_info']['middle_name'] ?? '') . " " .
                ($emp['personal_info']['last_name'] ?? '');
        ?></h2>
                <h2>Department: <?php 
            echo ($emp['department'] ?? '') . " " ;
;
        ?></h2>
    </div>
<div class="box-body">
<form method="POST" id="evalForm">
    <!-- JOB KNOWLEDGE -->
    <div class="page active" data-section="job">
    <h3>Job Knowledge & Skills (20%)</h3>
    <table>
        <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $job_criteria = [
            "Has thorough knowledge and understanding of handled course/s",
            "Brings in useful information connecting lessons to actual experiences",
            "Discusses and analyzes subject matter effectively",
            "Objectives of the course are clearly stated and attained",
            "Prepares well constructed assessment/test items"
        ];
        foreach ($job_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='job[$i]' value='$j' onchange='calcAverage(\\\"job\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="job_avg">0</span> |
            <strong>%:</strong> <span id="job_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="job_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>

    <!-- CLASSROOM EFFECTIVENESS -->
    <div class="page" data-section="class">
    <h3>Classroom Effectiveness (20%)</h3>
    <table>
       <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $class_criteria = [
            "Provides organized delivery of instruction",
            "Elicits participation through critical/logic questions",
            "Uses supplementary materials and varied activities",
            "Provides instruction consistent with course goals",
            "Presents examples and illustrations effectively"
        ];
        foreach ($class_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='class[$i]' value='$j' onchange='calcAverage(\\\"class\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="class_avg">0</span> |
            <strong>%:</strong> <span id="class_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="class_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>

    <!-- PREPARATION -->
    <div class="page" data-section="prep">
    <h3>Preparation & Use of Instructional Materials (15%)</h3>
    <table>
        <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $prep_criteria = [
            "Instructional materials are clear and presentable",
            "Makes effective use of teaching aids",
            "Improves course contents and methods",
            "Revises and updates materials",
            "Coordinates modifications with training manager"
        ];
        foreach ($prep_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='prep[$i]' value='$j' onchange='calcAverage(\\\"prep\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="prep_avg">0</span> |
            <strong>%:</strong> <span id="prep_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="prep_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>
    <!-- CLASSROOM EFFECTIVENESS -->
    <div class="page" data-section="dependres">
    <h3>Dependability & Resourcefullness (10%)</h3>
    <table>
        <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $dependres_criteria = [
            "A self-starter with outstanding initiative. Always makes wothwihile suggestions and ideas",
            "Exerts effort to enhance his knowledge, skills and work methods. ",
            "Makes an attempt to work beyond what is required of him such as helping in 
                the development of manual/hand-outs. ",
            "Highly dependable under most circumstances and performs work 
                assignments without the need for checking. ",
            "Readily accepts additional load or work assignments without complain "
        ];
        foreach ($dependres_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='dependres[$i]' value='$j' onchange='calcAverage(\\\"dependres\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="dependres_avg">0</span> |
            <strong>%:</strong> <span id="dependres_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="dependres_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>
       <!-- CLASSROOM EFFECTIVENESS -->
    <div class="page" data-section="humanrel">
<h3>Human Relations (10%)</h3>
<table>
<tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
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
                echo "<tr><td class='criteria'>$c</td><td class='rating'><div class='radio-group'>";
                for ($j=1; $j<=5; $j++) {
                    echo "<label><input type='radio' name='humanrel[$humanrel_index]' value='$j' onchange='calcAverage(\\\"humanrel\\\",$humanrel_criteria_count)'> $j</label>";
                }
                echo "</div></td></tr>";
                $humanrel_index++;
            }
        }
?>
<tr><td colspan="2">
    <strong>Ave:</strong> <span id="humanrel_avg">0</span> |
    <strong>%:</strong> <span id="humanrel_percent">0</span>
</td></tr>

        <tr><td colspan="2">
            <textarea name="humanrel_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>
           <!-- CLASSROOM EFFECTIVENESS -->
    <div class="page" data-section="jobcoop">
    <h3>Job Attitude/Cooperation (10%)</h3>
        <table>
        <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $jobcoop_criteria = [
            "Gives whole-hearted cooperation with others and his superiors towards the 
                attainment of corporate goal.",
            " Shows active participation in various activities of the Training Centre. ",
            "Shows enthusiasm for teaching the course and doing assigned tasks. ",
            " Shows positive work attitude at all times.",
            "Regularly coordinates and reports necessary information, or inquiries to 
            concerned personnel and/or department. "
        ];
        foreach ($jobcoop_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='jobcoop[$i]' value='$j' onchange='calcAverage(\\\"jobcoop\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="jobcoop_avg">0</span> |
            <strong>%:</strong> <span id="jobcoop_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="jobcoop_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>

    <div class="page" data-section="personal">
    <h3>PERSONAL QUALITIES (10%)</h3>
        <table>
        <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $personal_criteria = [
            "Has very respectable personality and appearance.",
            "Showed evidence of self-confidence when teaching a class. ",
            "Has a high level of patience as an instructor . ",
            "  Shows honesty in all dealings related to his/her work.",
            " Good communication skills and displays reasonable judgment. "
        ];
        foreach ($personal_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='personal[$i]' value='$j' onchange='calcAverage(\\\"personal\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="personal_avg">0</span> |
            <strong>%:</strong> <span id="personal_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="personal_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>

    <div class="page" data-section="attendance">
    <h3>ATTENDANCE & PUNCTUALITY (5%)</h3>
        <table>
        <tr><th class="criteria">Criteria</th><th class="rating">Rating</th></tr>
        <?php 
        $attendance_criteria = [
            "Shows punctuality in observing work hours.",
            "Good attendance record. Rarely absent to work. ",
            "Never leave post without any permission or a substitute co-worker. ",
            "Regularly attends, faculty meetings or other related function of the 
            organization as required by his/her superior .",
            "Promptly return to his/her class after break time. "
        ];
        foreach ($attendance_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='attendance[$i]' value='$j' onchange='calcAverage(\\\"attendance\\\",5)'> $j</label>";
            }
            echo "</td></tr>";
        }
        ?>
        <tr><td colspan="2">
            <strong>Ave:</strong> <span id="attendance_avg">0</span> |
            <strong>%:</strong> <span id="attendance_percent">0</span>
        </td></tr>
        <tr><td colspan="2">
            <textarea name="attendance_comments" rows="3" cols="80" placeholder="Comments..."></textarea>
        </td></tr>
    </table>
    </div>

    <div class="nav-controls">
        <button type="button" id="prevBtn" class="btn">Previous</button>
        <div style="flex:1"></div>
        <button type="button" id="nextBtn" class="btn">Next</button>
        <button type="submit" id="submitBtn" class="btn" style="display:none;">Submit</button>
    </div>
</form>

<!-- Modal Markup -->
<div class="modal-backdrop" id="modalBackdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-header" id="modalTitle">Modal</div>
        <div class="modal-body">
            <div id="modalMessage">Message</div>
            <ul class="list" id="modalList"></ul>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" id="modalYes">Yes</button>
            <button type="button" class="btn btn-secondary" id="modalNo">No</button>
            <button type="button" class="btn" id="modalOk">OK</button>
        </div>
    </div>
    
</div>
    </div>
</div>
</body>
</html>
