<?php
// Test AI System with Sample Data
// This script tests the actual AI system with our sample employee data

echo "<h2>Testing AI System with Sample Employee Data</h2>\n";

// Test queries
$testQueries = [
    "Find all Information Technology graduates",
    "List employees with Masteral degrees", 
    "Show me all Computer Science graduates",
    "Find all Information Systems graduates"
];

foreach ($testQueries as $query) {
    echo "<h3>Testing query: \"$query\"</h3>\n";
    
    // Simulate sending the query to the AI system
    $postData = http_build_query([
        'prompt' => $query
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData
        ]
    ]);
    
    // Try to get response from the AI system
    $response = @file_get_contents('http://localhost/hrims/users/admin/process_ai.php', false, $context);
    
    if ($response !== false) {
        echo "<p><strong>AI Response:</strong></p>\n";
        echo "<pre>" . htmlspecialchars($response) . "</pre>\n";
    } else {
        echo "<p style='color: red;'>Failed to get response from AI system. Make sure the AI server is running.</p>\n";
        
        // Let's also try a direct Python test
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
    }
    
    echo "<hr>\n";
}
?>