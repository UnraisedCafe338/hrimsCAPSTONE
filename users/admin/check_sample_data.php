<?php
// Check Sample Employee Data
// This script checks only our sample employee data in the database

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("applicants");
    
    echo "Connected to MongoDB successfully!\n\n";
    
    // Find only our sample employees (those with @example.com emails)
    $cursor = $collection->find([
        'personal_info.email' => ['$regex' => '.*@example.com$']
    ]);
    
    $sampleEmployees = iterator_to_array($cursor);
    
    echo "Sample employees in database: " . count($sampleEmployees) . "\n\n";
    
    echo "Sample Employee Education Details:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($sampleEmployees as $employee) {
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
    
    // Specific counts for education levels in sample data
    echo "Sample Data Education Level Counts:\n";
    echo str_repeat("-", 35) . "\n";
    
    // Count sample employees with college degrees
    $collegeCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        'education.college.degree' => ['$ne' => '']
    ]);
    echo "Sample employees with College degrees: $collegeCount\n";
    
    // Count sample employees with masteral degrees
    $masteralCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        'education.masteral.degree' => ['$ne' => '']
    ]);
    echo "Sample employees with Masteral degrees: $masteralCount\n";
    
    // Count sample employees with doctoral degrees
    $doctoralCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        'education.doctoral.degree' => ['$ne' => '']
    ]);
    echo "Sample employees with Doctoral degrees: $doctoralCount\n";
    
    // Count sample employees with specific degrees
    echo "\nSample Data Specific Degree Counts:\n";
    echo str_repeat("-", 35) . "\n";
    
    $itCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        '$or' => [
            ['education.college.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
            ['education.masteral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
            ['education.doctoral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']]
        ]
    ]);
    echo "Sample employees with Information Technology degrees: $itCount\n";
    
    $csCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        '$or' => [
            ['education.college.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
            ['education.masteral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
            ['education.doctoral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']]
        ]
    ]);
    echo "Sample employees with Computer Science degrees: $csCount\n";
    
    $isCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        '$or' => [
            ['education.college.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
            ['education.masteral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
            ['education.doctoral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']]
        ]
    ]);
    echo "Sample employees with Information Systems degrees: $isCount\n";
    
    $ceCount = $collection->countDocuments([
        'personal_info.email' => ['$regex' => '.*@example.com$'],
        'education.college.degree' => ['$regex' => 'Computer Engineering', '$options' => 'i']
    ]);
    echo "Sample employees with Computer Engineering degrees: $ceCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>