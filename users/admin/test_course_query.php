<?php
// Test Course Query
// This script tests if the AI system can find employees with specific courses/degrees

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("applicants");
    
    echo "Connected to MongoDB successfully!\n\n";
    
    // Test queries
    $testQueries = [
        "Find all Information Technology graduates",
        "List employees with Masteral degrees",
        "Show me all Computer Science graduates",
        "Find all Information Systems graduates",
        "Who has a Doctoral degree?"
    ];
    
    foreach ($testQueries as $query) {
        echo "Testing query: \"$query\"\n";
        
        // For now, let's just count how many documents match certain criteria
        if (strpos(strtolower($query), 'information technology') !== false) {
            $count = $collection->countDocuments([
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
                    ['education.masteral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
                    ['education.doctoral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']]
                ]
            ]);
            echo "Found $count employees with Information Technology degrees\n\n";
        } 
        elseif (strpos(strtolower($query), 'computer science') !== false) {
            $count = $collection->countDocuments([
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
                    ['education.masteral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
                    ['education.doctoral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']]
                ]
            ]);
            echo "Found $count employees with Computer Science degrees\n\n";
        }
        elseif (strpos(strtolower($query), 'information systems') !== false) {
            $count = $collection->countDocuments([
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
                    ['education.masteral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
                    ['education.doctoral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']]
                ]
            ]);
            echo "Found $count employees with Information Systems degrees\n\n";
        }
        elseif (strpos(strtolower($query), 'masteral') !== false) {
            $count = $collection->countDocuments([
                'education.masteral.degree' => ['$ne' => '']
            ]);
            echo "Found $count employees with Masteral degrees\n\n";
        }
        elseif (strpos(strtolower($query), 'doctoral') !== false) {
            $count = $collection->countDocuments([
                'education.doctoral.degree' => ['$ne' => '']
            ]);
            echo "Found $count employees with Doctoral degrees\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>