<?php
include('../connection.php');
$collection = $database->selectCollection("employees");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("No employee selected.");
}

$emp = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
if (!$emp) {
    die("Employee not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect scores
    $job_scores = $_POST['job'] ?? [];
    $class_scores = $_POST['class'] ?? [];
    $prep_scores = $_POST['prep'] ?? [];

    // Average per category
    $job_avg = array_sum($job_scores) / count($job_scores);
    $class_avg = array_sum($class_scores) / count($class_scores);
    $prep_avg = array_sum($prep_scores) / count($prep_scores);

    // Weighted scores
    $job_percent = $job_avg * 0.20;
    $class_percent = $class_avg * 0.20;
    $prep_percent = $prep_avg * 0.15;

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

    // Save to DB
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => [
            'evaluated' => true,
            'rating' => number_format($total, 2),
            'category' => $category,
            'evaluation' => [
                'job_avg' => $job_avg,
                'job_percent' => $job_percent,
                'class_avg' => $class_avg,
                'class_percent' => $class_percent,
                'prep_avg' => $prep_avg,
                'prep_percent' => $prep_percent,
                'comments' => [
                    'job' => $_POST['job_comments'] ?? '',
                    'class' => $_POST['class_comments'] ?? '',
                    'prep' => $_POST['prep_comments'] ?? ''
                ]
            ]
        ]]
    );

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
                document.getElementById(section + "_percent").innerText = percent;
            }
        }
    </script>
</head>
<body>

<h2>Evaluate: <?php 
    echo ($emp['personal_info']['first_name'] ?? '') . " " .
         ($emp['personal_info']['middle_name'] ?? '') . " " .
         ($emp['personal_info']['last_name'] ?? '');
?></h2>

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

    <button type="submit" class="submit-btn">Save Evaluation</button>
</form>

</body>
</html>
