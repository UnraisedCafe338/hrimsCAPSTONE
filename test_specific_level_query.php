<!DOCTYPE html>
<html>
<head>
    <title>Specific Level Query Test</title>
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
        h2 {
            color: #00124d;
            text-align: center;
        }
        .example {
            background-color: #f0f8ff;
            border-left: 4px solid #2575fc;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .example h3 {
            margin-top: 0;
            color: #00124d;
        }
        code {
            background-color: #f5f5f5;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Specific Education Level Query Test</h2>
        
        <div class="example">
            <h3>How the System Handles Specific Level Queries</h3>
            <p>The AI system can now process specific education level queries:</p>
            <ul>
                <li><code>"count all degrees in masteral and group them"</code> - Processes only masteral degrees</li>
                <li><code>"count all degrees in doctoral and group them"</code> - Processes only doctoral degrees</li>
                <li><code>"count all degrees in college and group them"</code> - Processes only college/bachelor degrees</li>
                <li><code>"count all degrees and group them"</code> - Processes all education levels</li>
            </ul>
        </div>
        
        <div class="example">
            <h3>Example Responses</h3>
            <p><strong>Query:</strong> "count all degrees in masteral and group them"</p>
            <p><strong>Response:</strong></p>
            <pre>
Found 12 masteral degrees grouped by course categories:
- 5 in Education / Teaching Field
- 3 in Information Technology / Computing / Programming
- 2 in Business / Management / Office Administration
- 1 in Health / Medical / Allied Sciences
- 1 in Social Sciences / Humanities / Law
            </pre>
        </div>
        
        <div class="example">
            <h3>Database Field Mapping</h3>
            <p>The system searches in these specific database fields:</p>
            <ul>
                <li><strong>College:</strong> education.college.degree, education.college.school</li>
                <li><strong>Masteral:</strong> education.masteral.degree, education.masteral.school</li>
                <li><strong>Doctoral:</strong> education.doctoral.degree, education.doctoral.school</li>
            </ul>
        </div>
        
        <div class="example">
            <h3>Course Grouping Categories</h3>
            <p>All degrees are grouped into these 10 comprehensive categories:</p>
            <ol>
                <li>Information Technology / Computing / Programming</li>
                <li>Education / Teaching Field</li>
                <li>Business / Management / Office Administration</li>
                <li>Engineering / Technical / Architecture</li>
                <li>Science / Research / Laboratory</li>
                <li>Health / Medical / Allied Sciences</li>
                <li>Arts / Media / Communication</li>
                <li>Social Sciences / Humanities / Law</li>
                <li>Agriculture / Fisheries / Veterinary</li>
                <li>Tourism / Hospitality / Culinary</li>
            </ol>
        </div>
    </div>
</body>
</html>