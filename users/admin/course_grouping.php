<?php
include('../../handlers/connection.php');

// Function to group courses into main categories based on the detailed breakdown
function groupCourse($degree) {
    $degree = strtolower($degree);
    
    // Information Technology / Computing / Programming
    if (preg_match('/\b(information\s*technology|bsit|information\s*systems|bsis|computer\s*science|bscs|computer\s*engineering|bscpe|data\s*science|software\s*engineering|cybersecurity|multimedia\s*arts|entertainment\s*and\s*multimedia\s*computing|computer\s*studies)\b/', $degree)) {
        return 'Information Technology / Computing / Programming';
    }
    
    // Education / Teaching Field
    if (preg_match('/\b(secondary\s*education|elementary\s*education|technical\s*teacher\s*education|early\s*childhood\s*education|physical\s*education|special\s*needs\s*education|technical\-vocational\s*teacher\s*education|bte?d|bsed|beed|btte|bece?d|bped|bsned|btvte?d)\b/', $degree)) {
        return 'Education / Teaching Field';
    }
    
    // Business / Management / Office Administration
    if (preg_match('/\b(business\s*administration|office\s*administration|accountancy|management\s*accounting|entrepreneurship|economics|marketing\s*management|human\s*resource\s*management|financial\s*management|bsba|bsoa|bsa|bsma|bse?ntrep|bsecon|bsfm)\b/', $degree)) {
        return 'Business / Management / Office Administration';
    }
    
    // Engineering / Technical / Architecture
    if (preg_match('/\b(civil\s*engineering|electrical\s*engineering|mechanical\s*engineering|electronics\s*and\s*communications\s*engineering|industrial\s*engineering|architecture|automotive\s*technology|mechatronics\s*engineering|bsce|bsee|bsme|bsece|bsie|bsarch|bsae)\b/', $degree)) {
        return 'Engineering / Technical / Architecture';
    }
    
    // Science / Research / Laboratory
    if (preg_match('/\b(biology|chemistry|physics|environmental\s*science|biotechnology|marine\s*biology|applied\s*science)\b/', $degree)) {
        return 'Science / Research / Laboratory';
    }
    
    // Health / Medical / Allied Sciences
    if (preg_match('/\b(nursing|medical\s*technology|pharmacy|physical\s*therapy|radiologic\s*technology|nutrition\s*and\s*dietetics|midwifery|bsn|bsmt|bspt|bsrt|bsnd)\b/', $degree)) {
        return 'Health / Medical / Allied Sciences';
    }
    
    // Arts / Media / Communication
    if (preg_match('/\b(communication|journalism|broadcasting|fine\s*arts|english|literature|film\s*and\s*media\s*studies)\b/', $degree)) {
        return 'Arts / Media / Communication';
    }
    
    // Social Sciences / Humanities / Law
    if (preg_match('/\b(political\s*science|psychology|criminology|social\s*work|philosophy|public\s*administration|legal\s*management)\b/', $degree)) {
        return 'Social Sciences / Humanities / Law';
    }
    
    // Agriculture / Fisheries / Veterinary
    if (preg_match('/\b(agriculture|fisheries|veterinary\s*medicine|agricultural\s*engineering|bsa|bsvm|bsae)\b/', $degree)) {
        return 'Agriculture / Fisheries / Veterinary';
    }
    
    // Tourism / Hospitality / Culinary
    if (preg_match('/\b(hotel\s*and\s*restaurant\s*management|hospitality\s*management|tourism\s*management|travel\s*management|culinary\s*management|bshrm|bshm|bstm|bstrm)\b/', $degree)) {
        return 'Tourism / Hospitality / Culinary';
    }
    
    // If no match, return a generic category
    return 'Other';
}

// Function to get education information (handles college, masteral, doctoral)
function getEducationInfo($doc) {
    $educationInfo = [];
    
    // Check college education
    if (isset($doc['education']['college']['degree']) && !empty($doc['education']['college']['degree'])) {
        $educationInfo[] = [
            'level' => 'College',
            'degree' => $doc['education']['college']['degree'],
            'school' => $doc['education']['college']['school'] ?? ''
        ];
    }
    
    // Check masteral education
    if (isset($doc['education']['masteral']['degree']) && !empty($doc['education']['masteral']['degree'])) {
        $educationInfo[] = [
            'level' => 'Masteral',
            'degree' => $doc['education']['masteral']['degree'],
            'school' => $doc['education']['masteral']['school'] ?? ''
        ];
    }
    
    // Check doctoral education
    if (isset($doc['education']['doctoral']['degree']) && !empty($doc['education']['doctoral']['degree'])) {
        $educationInfo[] = [
            'level' => 'Doctoral',
            'degree' => $doc['education']['doctoral']['degree'],
            'school' => $doc['education']['doctoral']['school'] ?? ''
        ];
    }
    
    return $educationInfo;
}

// Function to count and group degrees from a collection
function countAndGroupDegrees($collection, &$allDegrees) {
    $cursor = $collection->find([]);
    $courseCounts = [
        'College' => [],
        'Masteral' => [],
        'Doctoral' => []
    ];
    
    foreach ($cursor as $doc) {
        // Get all education information
        $educationInfo = getEducationInfo($doc);
        
        // Process each education level
        foreach ($educationInfo as $edu) {
            $degree = $edu['degree'];
            $level = $edu['level'];
            
            // Only count if degree is not empty
            if (!empty($degree)) {
                $group = groupCourse($degree);
                
                if (!isset($courseCounts[$level][$group])) {
                    $courseCounts[$level][$group] = 0;
                }
                $courseCounts[$level][$group]++;
                
                // Store for raw data display
                $name = ($doc['personal_info']['first_name'] ?? '') . ' ' . ($doc['personal_info']['last_name'] ?? '');
                $allDegrees[] = [
                    'name' => $name,
                    'degree' => $degree,
                    'school' => $edu['school'],
                    'group' => $group,
                    'level' => $level,
                    'source' => $collection->getCollectionName()
                ];
            }
        }
    }
    
    return $courseCounts;
}

// Get the collections
$employeeCollection = $database->selectCollection("employee");
$applicantCollection = $database->selectCollection("applicants");

// Count and group degrees from both collections
$allDegrees = [];
$employeeCourseCounts = countAndGroupDegrees($employeeCollection, $allDegrees);
$applicantCourseCounts = countAndGroupDegrees($applicantCollection, $allDegrees);

// Merge counts from both collections
$courseCounts = [
    'College' => [],
    'Masteral' => [],
    'Doctoral' => []
];

foreach (['College', 'Masteral', 'Doctoral'] as $level) {
    // Merge employee and applicant counts
    foreach ($employeeCourseCounts[$level] as $course => $count) {
        if (!isset($courseCounts[$level][$course])) {
            $courseCounts[$level][$course] = 0;
        }
        $courseCounts[$level][$course] += $count;
    }
    
    foreach ($applicantCourseCounts[$level] as $course => $count) {
        if (!isset($courseCounts[$level][$course])) {
            $courseCounts[$level][$course] = 0;
        }
        $courseCounts[$level][$course] += $count;
    }
    
    // Sort by count in descending order
    arsort($courseCounts[$level]);
}

// Calculate totals for each level
$levelTotals = [];
foreach (['College', 'Masteral', 'Doctoral'] as $level) {
    $levelTotals[$level] = array_sum($courseCounts[$level]);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Grouping Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .content {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #00124d;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h2, h3 {
            color: #00124d;
        }
        .summary {
            background-color: #e9f7ef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .header {
            background-color: #00124d;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .box-header {
            margin-bottom: 20px;
        }
        .total-count {
            font-size: 1.2em;
            font-weight: bold;
            color: #00124d;
            margin: 10px 0;
        }
        .level-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .level-header {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="header">Course Grouping Report</div>
    <div class="content">
        <div class="box-header">
            <h2>Degree Count by Course Category</h2>
            <div class="summary">
                <p>This report shows the count of degrees grouped by course categories across all employees and applicants, including College, Masteral, and Doctoral levels.</p>
                <div class="total-count">
                    Total Graduates: 
                    College: <?php echo $levelTotals['College']; ?>, 
                    Masteral: <?php echo $levelTotals['Masteral']; ?>, 
                    Doctoral: <?php echo $levelTotals['Doctoral']; ?>
                </div>
            </div>
        </div>
        <div class="box-body">
            <?php foreach (['College', 'Masteral', 'Doctoral'] as $level): ?>
            <div class="level-section">
                <div class="level-header"><?php echo htmlspecialchars($level); ?> Level (Total: <?php echo $levelTotals[$level]; ?>)</div>
                <table>
                    <tr>
                        <th>Course Category</th>
                        <th>Number of Graduates</th>
                    </tr>
                    <?php foreach ($courseCounts[$level] as $course => $count): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course); ?></td>
                        <td><?php echo htmlspecialchars($count); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endforeach; ?>

            <h3>Detailed Graduate List</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Degree</th>
                    <th>School</th>
                    <th>Group</th>
                    <th>Level</th>
                    <th>Source</th>
                </tr>
                <?php foreach ($allDegrees as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['name']); ?></td>
                    <td><?php echo htmlspecialchars($record['degree']); ?></td>
                    <td><?php echo htmlspecialchars($record['school']); ?></td>
                    <td><?php echo htmlspecialchars($record['group']); ?></td>
                    <td><?php echo htmlspecialchars($record['level']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($record['source'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>