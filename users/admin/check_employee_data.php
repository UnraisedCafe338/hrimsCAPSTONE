<?php
// Check Employee Data
// This script checks the actual employee data in the database

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("applicants");
    
    echo "Connected to MongoDB successfully!\n\n";
    
    // Count total employees
    $totalCount = $collection->countDocuments();
    echo "Total employees in database: $totalCount\n\n";
    
    // Find all employees and display their education info
    $cursor = $collection->find([], ['limit' => 20]);
    
    echo "Employee Education Details:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($cursor as $employee) {
        $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
        $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
        
        echo "Name: $firstName $lastName\n";
        
        // Check college education
        if (isset($employee['education']['college']['degree']) && !empty($employee['education']['college']['degree'])) {
            $collegeDegree = $employee['education']['college']['degree'];
            $collegeSchool = $employee['education']['college']['school'] ?? 'N/A';
            echo "  College: $collegeDegree from $collegeSchool\n";
        }
        
        // Check masteral education
        if (isset($employee['education']['masteral']['degree']) && !empty($employee['education']['masteral']['degree'])) {
            $masteralDegree = $employee['education']['masteral']['degree'];
            $masteralSchool = $employee['education']['masteral']['school'] ?? 'N/A';
            echo "  Masteral: $masteralDegree from $masteralSchool\n";
        }
        
        // Check doctoral education
        if (isset($employee['education']['doctoral']['degree']) && !empty($employee['education']['doctoral']['degree'])) {
            $doctoralDegree = $employee['education']['doctoral']['degree'];
            $doctoralSchool = $employee['education']['doctoral']['school'] ?? 'N/A';
            echo "  Doctoral: $doctoralDegree from $doctoralSchool\n";
        }
        
        echo "\n";
    }
    
    // Specific counts for education levels
    echo "Specific Education Level Counts:\n";
    echo str_repeat("-", 30) . "\n";
    
    // Count employees with college degrees
    $collegeCount = $collection->countDocuments([
        'education.college.degree' => ['$ne' => '']
    ]);
    echo "Employees with College degrees: $collegeCount\n";
    
    // Count employees with masteral degrees
    $masteralCount = $collection->countDocuments([
        'education.masteral.degree' => ['$ne' => '']
    ]);
    echo "Employees with Masteral degrees: $masteralCount\n";
    
    // Count employees with doctoral degrees
    $doctoralCount = $collection->countDocuments([
        'education.doctoral.degree' => ['$ne' => '']
    ]);
    echo "Employees with Doctoral degrees: $doctoralCount\n";
    
    // Count employees with specific degrees
    echo "\nSpecific Degree Counts:\n";
    echo str_repeat("-", 25) . "\n";
    
    $itCount = $collection->countDocuments([
        '$or' => [
            ['education.college.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
            ['education.masteral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
            ['education.doctoral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']]
        ]
    ]);
    echo "Information Technology degrees: $itCount\n";
    
    $csCount = $collection->countDocuments([
        '$or' => [
            ['education.college.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
            ['education.masteral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
            ['education.doctoral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']]
        ]
    ]);
    echo "Computer Science degrees: $csCount\n";
    
    $isCount = $collection->countDocuments([
        '$or' => [
            ['education.college.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
            ['education.masteral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
            ['education.doctoral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']]
        ]
    ]);
    echo "Information Systems degrees: $isCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>