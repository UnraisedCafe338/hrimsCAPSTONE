<!DOCTYPE html>
<html>
<head>
    <title>Course Grouping Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
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
        h2 {
            color: #00124d;
            text-align: center;
        }
        .level-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .level-header {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 3px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Course Grouping Test Results</h2>
        <?php
        // Test the course grouping function
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

        // Test cases for different education levels
        $testDegrees = [
            // College level
            ["BS Information Technology", "College"],
            ["BS Computer Science", "College"],
            ["BS Information Systems", "College"],
            ["Bachelor of Secondary Education", "College"],
            ["Bachelor of Elementary Education", "College"],
            ["BS Business Administration", "College"],
            ["BS Civil Engineering", "College"],
            ["BS Nursing", "College"],
            ["BA Communication", "College"],
            ["BS Criminology", "College"],
            
            // Masteral level
            ["Master of Arts in Education", "Masteral"],
            ["Master of Science in Computer Science", "Masteral"],
            ["Master of Business Administration", "Masteral"],
            ["Master of Public Administration", "Masteral"],
            ["Master of Science in Nursing", "Masteral"],
            
            // Doctoral level
            ["Doctor of Philosophy in Education", "Doctoral"],
            ["Doctor of Medicine", "Doctoral"],
            ["Doctor of Education", "Doctoral"],
            ["Doctor of Philosophy in Computer Science", "Doctoral"],
            ["Doctor of Business Administration", "Doctoral"]
        ];

        // Group by education level
        $groupedTests = [
            'College' => [],
            'Masteral' => [],
            'Doctoral' => []
        ];
        
        foreach ($testDegrees as $test) {
            $degree = $test[0];
            $level = $test[1];
            $group = groupCourse($degree);
            $groupedTests[$level][] = ['degree' => $degree, 'group' => $group];
        }

        // Display results by education level
        foreach (['College', 'Masteral', 'Doctoral'] as $level) {
            echo "<div class='level-section'>";
            echo "<div class='level-header'>{$level} Level</div>";
            echo "<table>";
            echo "<tr><th>Degree</th><th>Group</th></tr>";

            foreach ($groupedTests[$level] as $test) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($test['degree']) . "</td>";
                echo "<td>" . htmlspecialchars($test['group']) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>