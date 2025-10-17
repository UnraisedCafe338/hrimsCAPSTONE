<?php
// Test AI Employee Query
// This script tests if the AI system can find employees with specific courses/degrees

echo "<h2>Testing AI System with Sample Employee Data</h2>\n";

// Test queries
$testQueries = [
    "Find all Information Technology employees",
    "List employees with Masteral degrees", 
    "Show me all Computer Science employees",
    "Find all Information Systems employees"
];

foreach ($testQueries as $query) {
    echo "<h3>Testing query: \"$query\"</h3>\n";
    
    // Try a direct Python test
    echo "<p><strong>Trying direct Python test...</strong></p>\n";
    
    $pythonPath = 'C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
    $aiScript = __DIR__ . '/ai_script.py';
    
    if (file_exists($aiScript)) {
        $command = "\"$pythonPath\" \"$aiScript\" \"" . addslashes($query) . "\" 2>&1";
        echo "<p>Command: $command</p>\n";
        
        $output = shell_exec($command);
        if ($output) {
            echo "<p><strong>Python Output:</strong></p>\n";
            echo "<pre>" . htmlspecialchars($output) . "</pre>\n";
        } else {
            echo "<p>No output from Python script.</p>\n";
        }
    } else {
        echo "<p>AI script not found at: $aiScript</p>\n";
    }
    
    echo "<hr>\n";
}
?>