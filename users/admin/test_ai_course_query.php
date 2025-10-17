<?php
// Test AI Course Query
// This script tests if the AI system can find employees with specific courses/degrees

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("applicants");
    
    echo "Connected to MongoDB successfully!\n\n";
    
    // Test queries that the AI system should be able to handle
    $testQueries = [
        "Find all Information Technology graduates",
        "List employees with Masteral degrees",
        "Show me all Computer Science graduates",
        "Find all Information Systems graduates",
        "Who has a Doctoral degree?",
        "Find all Computer Engineering graduates"
    ];
    
    foreach ($testQueries as $query) {
        echo "Testing query: \"$query\"\n";
        
        // Process the query similar to how the AI system would
        processQuery($query, $collection);
        
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

function processQuery($query, $collection) {
    $queryLower = strtolower($query);
    
    // Check for specific degree queries
    if (strpos($queryLower, 'information technology') !== false) {
        $cursor = $collection->find([
            'personal_info.email' => ['$regex' => '.*@example.com$'],
            '$or' => [
                ['education.college.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
                ['education.masteral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']],
                ['education.doctoral.degree' => ['$regex' => 'Information Technology', '$options' => 'i']]
            ]
        ]);
        
        $results = iterator_to_array($cursor);
        echo "Found " . count($results) . " Information Technology graduates:\n";
        
        foreach ($results as $employee) {
            $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
            $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
            echo "  - $firstName $lastName\n";
        }
    } 
    elseif (strpos($queryLower, 'computer science') !== false) {
        $cursor = $collection->find([
            'personal_info.email' => ['$regex' => '.*@example.com$'],
            '$or' => [
                ['education.college.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
                ['education.masteral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']],
                ['education.doctoral.degree' => ['$regex' => 'Computer Science', '$options' => 'i']]
            ]
        ]);
        
        $results = iterator_to_array($cursor);
        echo "Found " . count($results) . " Computer Science graduates:\n";
        
        foreach ($results as $employee) {
            $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
            $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
            echo "  - $firstName $lastName\n";
        }
    }
    elseif (strpos($queryLower, 'information systems') !== false) {
        $cursor = $collection->find([
            'personal_info.email' => ['$regex' => '.*@example.com$'],
            '$or' => [
                ['education.college.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
                ['education.masteral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']],
                ['education.doctoral.degree' => ['$regex' => 'Information Systems', '$options' => 'i']]
            ]
        ]);
        
        $results = iterator_to_array($cursor);
        echo "Found " . count($results) . " Information Systems graduates:\n";
        
        foreach ($results as $employee) {
            $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
            $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
            echo "  - $firstName $lastName\n";
        }
    }
    elseif (strpos($queryLower, 'computer engineering') !== false) {
        $cursor = $collection->find([
            'personal_info.email' => ['$regex' => '.*@example.com$'],
            'education.college.degree' => ['$regex' => 'Computer Engineering', '$options' => 'i']
        ]);
        
        $results = iterator_to_array($cursor);
        echo "Found " . count($results) . " Computer Engineering graduates:\n";
        
        foreach ($results as $employee) {
            $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
            $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
            echo "  - $firstName $lastName\n";
        }
    }
    // Check for education level queries
    elseif (strpos($queryLower, 'masteral') !== false) {
        $cursor = $collection->find([
            'personal_info.email' => ['$regex' => '.*@example.com$'],
            'education.masteral.degree' => ['$ne' => '']
        ]);
        
        $results = iterator_to_array($cursor);
        echo "Found " . count($results) . " employees with Masteral degrees:\n";
        
        foreach ($results as $employee) {
            $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
            $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
            $degree = $employee['education']['masteral']['degree'] ?? 'N/A';
            echo "  - $firstName $lastName ($degree)\n";
        }
    }
    elseif (strpos($queryLower, 'doctoral') !== false) {
        $cursor = $collection->find([
            'personal_info.email' => ['$regex' => '.*@example.com$'],
            'education.doctoral.degree' => ['$ne' => '']
        ]);
        
        $results = iterator_to_array($cursor);
        echo "Found " . count($results) . " employees with Doctoral degrees:\n";
        
        foreach ($results as $employee) {
            $firstName = $employee['personal_info']['first_name'] ?? 'N/A';
            $lastName = $employee['personal_info']['last_name'] ?? 'N/A';
            $degree = $employee['education']['doctoral']['degree'] ?? 'N/A';
            echo "  - $firstName $lastName ($degree)\n";
        }
    }
    else {
        echo "Query not recognized. Please try a different query.\n";
    }
}
?>