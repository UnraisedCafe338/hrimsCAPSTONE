<?php
include('../connection.php');
$employees = $database->selectCollection("employees");
$evaluations = $database->selectCollection("performance_evaluations");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("No employee selected.");
}

$emp = $employees->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
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

    // Average per category
    $job_avg = array_sum($job_scores) / count($job_scores);
    $class_avg = array_sum($class_scores) / count($class_scores);
    $prep_avg = array_sum($prep_scores) / count($prep_scores);
    $dependres_avg = array_sum($dependres_scores) / count($dependres_scores);  
    $humanrel_avg = array_sum($humanrel_scores) / count($humanrel_scores);  
    $jobcoop_avg = array_sum($jobcoop_scores) / count($jobcoop_scores); 
    $attendance_avg = array_sum($_POST['attendance'] ?? []) / count($_POST['attendance'] ?? []);
    $personal_avg = array_sum($_POST['personal'] ?? []) / count($_POST['personal'] ?? []);

    
    
    // Weighted scores
    $job_percent = $job_avg * 0.20;
    $class_percent = $class_avg * 0.20;
    $prep_percent = $prep_avg * 0.15;
    $dependres_percent = $dependres_avg * 0.10;
    $humanrel_percent = $humanrel_avg * 0.10;
    $jobcoop_percent = $jobcoop_avg * 0.10;
    $attendance_percent = $attendance_avg * 0.10;
    $personal_percent = $personal_avg * 0.10;

    $total = $job_percent + $class_percent + $prep_percent;

    // Convert numeric rating to category
    if ($total >= 4.5) {
        $category = "Excellent";
    } elseif ($total >= 3.5) {
        $category = "Very Good";
    } elseif ($total >= 2.5) {
        $category = "Good";
    } elseif ($total >= 1.5) {
        $category = "Fair";
    } else {
        $category = "Poor";
    }

    // Save evaluation record in separate collection
    $evaluations->insertOne([
        'employee_id' => new MongoDB\BSON\ObjectId($id),
        'evaluated_at' => new MongoDB\BSON\UTCDateTime(), // timestamp
        'rating' => number_format($total, 2),
        'category' => $category,
        'evaluation' => [
            'job_avg' => $job_avg,
            'job_percent' => $job_percent,
            'class_avg' => $class_avg,
            'class_percent' => $class_percent,
            'prep_avg' => $prep_avg,
            'prep_percent' => $prep_percent,
            'dependres_avg' => $dependres_avg,
            'dependres_percent' => $dependres_percent,
            'humanrel_avg' => $humanrel_avg,
            'humanrel_percent' => $humanrel_percent,
            'jobcoop_avg' => $jobcoop_avg,
            'jobcoop_percent' => $jobcoop_percent,
            'comments' => [
                'job' => $_POST['job_comments'] ?? '',
                'class' => $_POST['class_comments'] ?? '',
                'prep' => $_POST['prep_comments'] ?? '',
                'dependres' => $_POST['dependres_comments'] ?? '',
                'humanrel' => $_POST['humanrel_comments'] ?? '',
                'attendance' => $_POST['attendance_comments'] ?? '',
                'jobcoop' => $_POST['jobcoop_comments'] ?? '',
                'personal' => $_POST['personal_comments'] ?? ''
            ]
        ]
    ]);

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
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #00124d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #00124d; color: white; }
        .submit-btn {
            background-color: #00124d; color: white; padding: 10px 15px;
            border: none; border-radius: 5px; cursor: pointer;
        }
        .radio-group { display: flex; gap: 5px; }
          .performance-button {
    background-color: #00124d;
    border-left: 4px solid #ffffff;
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
                if (section === "attendance") percent = (avg * 0.10).toFixed(2);
                document.getElementById(section + "_percent").innerText = percent;
            }
        }
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
    </div>
<div class="box-body">
<form method="POST">
    <!-- JOB KNOWLEDGE -->
    <h3>Job Knowledge & Skills (20%)</h3>
    <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='job[$i]' value='$j' required onchange='calcAverage(\"job\",5)'> $j</label>";
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

    <!-- CLASSROOM EFFECTIVENESS -->
    <h3>Classroom Effectiveness (20%)</h3>
    <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='class[$i]' value='$j' required onchange='calcAverage(\"class\",5)'> $j</label>";
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

    <!-- PREPARATION -->
    <h3>Preparation & Use of Instructional Materials (15%)</h3>
    <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='prep[$i]' value='$j' required onchange='calcAverage(\"prep\",5)'> $j</label>";
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
    <!-- CLASSROOM EFFECTIVENESS -->
    <h3>Dependability & Resourcefullness (10%)</h3>
    <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='dependres[$i]' value='$j' required onchange='calcAverage(\"dependres\",5)'> $j</label>";
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
       <!-- CLASSROOM EFFECTIVENESS -->
    <h3>Human Relations (10%)</h3>
        <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
        <?php 
        $humanrel_criteria = [
            "1. Trainees/Students",
            "       Relates to trainees in ways which promotes mutual respect ",
            "       Has good rapport with trainees. ",
            "2. Other Employees and Superior",
            "       Maintains harmonious relationship with co-workers or other employees. ",
            "       Easily deals with people with whom he/she works and comes in contact. ",
            "       Shows respect to subordinate, colleagues and superior. "
        ];
        foreach ($humanrel_criteria as $i => $c) {
            echo "<tr><td>$c</td><td class='radio-group'>";
            for ($j=1; $j<=5; $j++) {
                echo "<label><input type='radio' name='humanrel[$i]' value='$j' required onchange='calcAverage(\"humanrel\",5)'> $j</label>";
            }
            echo "</td></tr>";
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
           <!-- CLASSROOM EFFECTIVENESS -->
    <h3>Job Attitude/Cooperation (10%)</h3>
        <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='jobcoop[$i]' value='$j' required onchange='calcAverage(\"jobcoop\",5)'> $j</label>";
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

    <h3>PERSONAL QUALITIES (10%)</h3>
        <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='personal[$i]' value='$j' required onchange='calcAverage(\"personal\",5)'> $j</label>";
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

    <h3>ATTENDANCE & PUNCTUALITY (5%)</h3>
        <table>
        <tr><th>Criteria</th><th>Rating</th></tr>
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
                echo "<label><input type='radio' name='attendance[$i]' value='$j' required onchange='calcAverage(\"attendance\",5)'> $j</label>";
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
    
    <button type="submit" class="submit-btn">Save Evaluation</button>
</form>
    </div>
</div>
</body>
</html>
