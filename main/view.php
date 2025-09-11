<?php
include('../connection.php');
$employees = $database->selectCollection("employee");
$evaluations = $database->selectCollection("performance_evaluations");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("No employee selected.");
}

$emp = $employees->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
if (!$emp) {
    die("Employee not found.");
}

// If specific evaluation is requested
$evalId = $_GET['eval_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Evaluation</title>
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
            border: 1px solid #ddd;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th { background-color: #f0f0f0; }
        .btn-view {
            padding: 5px 10px;
            background-color: #00124d;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .criteria {
            font-weight: normal;
            padding-left: 20px;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="header">Evaluation Results</div><br><br><br>

<div class="content">
    <h2>Employee: 
        <?php 
            echo ($emp['personal_info']['first_name'] ?? '') . " " .
                 ($emp['personal_info']['middle_name'] ?? '') . " " .
                 ($emp['personal_info']['last_name'] ?? '');
        ?>
    </h2>

    <?php if (!$evalId): ?>
        <!-- Show summary table -->
        <h3>Evaluation Summary</h3>
        <table>
            <tr>
                <th>Rating Period</th>
                <th>Total Rating</th>
                <th>Adjectival Rating</th>
                <th>Purpose</th>
                <th>Action</th>
            </tr>
            <?php
            $cursor = $evaluations->find(['employee_id' => new MongoDB\BSON\ObjectId($id)]);
            foreach ($cursor as $e) {
                $date = $e['evaluated_at']->toDateTime()->format("Y-m-d");
                $rating = $e['rating'];
                $category = $e['category'];
                $purpose = $e['purpose'] ?? "Performance Appraisal";
                echo "<tr>
                        <td>{$date}</td>
                        <td>{$rating}</td>
                        <td>{$category}</td>
                        <td>{$purpose}</td>
                        <td><a class='btn-view' href='view.php?id={$id}&eval_id={$e['_id']}'>View</a></td>
                      </tr>";
            }
            ?>
        </table>

    <?php else: ?>
        <!-- Show detailed evaluation -->
        <?php
        $evaluation = $evaluations->findOne(['_id' => new MongoDB\BSON\ObjectId($evalId)]);
        if (!$evaluation) {
            echo "<p>Evaluation not found.</p>";
        } else {
            echo "<h3>Detailed Evaluation Result</h3>";
            echo "<p><strong>Rating Period:</strong> " . $evaluation['evaluated_at']->toDateTime()->format("Y-m-d") . "</p>";
            echo "<p><strong>Total Rating:</strong> " . $evaluation['rating'] . "</p>";
            echo "<p><strong>Adjectival Rating:</strong> " . $evaluation['category'] . "</p>";
            echo "<p><strong>Purpose:</strong> " . ($evaluation['purpose'] ?? "Performance Appraisal") . "</p>";
            
            // Sections with criteria
            $categories = [
                'job' => "Job Knowledge & Skills",
                'class' => "Classroom Effectiveness",
                'prep' => "Preparation & Use of Instructional Materials",
                'dependres' => "Dependability & Resourcefulness",
                'humanrel' => "Human Relations",
                'jobcoop' => "Job Attitude/Cooperation",
                'personal' => "Personal Qualities",
                'attendance' => "Attendance & Punctuality"
            ];

            $criteriaMap = [
                'job' => [
                    "Has thorough knowledge and understanding of handled course/s",
                    "Brings in useful information connecting lessons to actual experiences",
                    "Discusses and analyzes subject matter effectively",
                    "Objectives of the course are clearly stated and attained",
                    "Prepares well constructed assessment/test items"
                ],
                'class' => [
                    "Provides organized delivery of instruction",
                    "Elicits participation through critical/logic questions",
                    "Uses supplementary materials and varied activities",
                    "Provides instruction consistent with course goals",
                    "Presents examples and illustrations effectively"
                ],
                'prep' => [
                    "Instructional materials are clear and presentable",
                    "Makes effective use of teaching aids",
                    "Improves course contents and methods",
                    "Revises and updates materials",
                    "Coordinates modifications with training manager"
                ],
                'dependres' => [
                    "A self-starter with outstanding initiative.",
                    "Exerts effort to enhance his knowledge, skills and work methods.",
                    "Makes an attempt to work beyond what is required",
                    "Highly dependable under most circumstances",
                    "Readily accepts additional load or work assignments"
                ],
                'humanrel' => [
                    "Relates to trainees in ways which promotes mutual respect",
                    "Has good rapport with trainees.",
                    "Maintains harmonious relationship with co-workers",
                    "Easily deals with people he/she works with",
                    "Shows respect to subordinates, colleagues and superior."
                ],
                'jobcoop' => [
                    "Gives whole-hearted cooperation with others and superiors",
                    "Shows active participation in activities",
                    "Shows enthusiasm for teaching tasks",
                    "Shows positive work attitude",
                    "Regularly coordinates and reports necessary info"
                ],
                'personal' => [
                    "Has very respectable personality and appearance",
                    "Shows evidence of self-confidence",
                    "Has high level of patience",
                    "Shows honesty in all dealings",
                    "Good communication skills and judgment"
                ],
                'attendance' => [
                    "Shows punctuality in observing work hours",
                    "Good attendance record",
                    "Never leaves post without permission",
                    "Attends meetings and functions",
                    "Promptly returns to class after breaks"
                ]
            ];

            foreach ($categories as $key => $label) {
                if (isset($evaluation['sections'][$key])) {
                    $section = $evaluation['sections'][$key];
                    echo "<h4>{$label}</h4>";
                    echo "<table><tr><th>Score</th><th>Criteria</th></tr>";
                    $criteriaList = $criteriaMap[$key] ?? [];
                    foreach ($criteriaList as $index => $criterion) {
                        $score = $section['scores'][$index] ?? "N/A";
                        echo "<tr><td>{$score}</td><td>{$criterion}</td></tr>";
                    }
                    echo "</table>";
                    echo "<p><strong>Average:</strong> " . number_format($section['average'], 2) . 
                        " | <strong>Percent:</strong> " . number_format($section['percent'], 2) . "</p>";
                    echo "<p><strong>Comments:</strong> " . htmlspecialchars($section['comments'] ?? '') . "</p>";
                }
            }
        }
        ?>
        <p><a href="view.php?id=<?php echo $id; ?>">⬅ Back to Summary</a></p>
    <?php endif; ?>
</div>
</body>
</html>
