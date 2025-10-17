<?php
// Check Employee Collection Structure
// This script checks the structure of existing employee documents

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("employees");
    
    echo "Connected to MongoDB successfully!\n\n";
    
    // Count total employees
    $totalCount = $collection->countDocuments();
    echo "Total employees in database: $totalCount\n\n";
    
    // Find a few employees to understand the structure
    $cursor = $collection->find([], ['limit' => 2]);
    
    echo "Employee Document Structure:\n";
    echo str_repeat("=", 50) . "\n";
    
    foreach ($cursor as $employee) {
        echo "Employee ID: " . $employee['_id'] . "\n";
        echo "First Name: " . ($employee['personal_info']['first_name'] ?? 'N/A') . "\n";
        echo "Last Name: " . ($employee['personal_info']['last_name'] ?? 'N/A') . "\n";
        echo "Email: " . ($employee['personal_info']['email'] ?? 'N/A') . "\n";
        
        // Education information
        echo "Education:\n";
        if (isset($employee['education']['college']['degree'])) {
            echo "  College Degree: " . $employee['education']['college']['degree'] . "\n";
        }
        if (isset($employee['education']['college']['school'])) {
            echo "  College School: " . $employee['education']['college']['school'] . "\n";
        }
        
        echo "\n" . str_repeat("-", 50) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>