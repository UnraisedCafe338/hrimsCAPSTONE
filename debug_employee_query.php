<?php
// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

// Use the correct MongoDB namespace
use MongoDB\Client;

try {
    $client = new Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    
    // Query for employee with name "Princes Lyka M Santos"
    $filter = [
        '$or' => [
            ['personal_info.first_name' => ['$regex' => 'Princes', '$options' => 'i']],
            ['personal_info.middle_name' => ['$regex' => 'Lyka', '$options' => 'i']],
            ['personal_info.last_name' => ['$regex' => 'Santos', '$options' => 'i']],
            ['personal_info.first_name' => ['$regex' => 'Princes Lyka M Santos', '$options' => 'i']],
        ]
    ];

    echo "Searching for: Princes Lyka M Santos\n";

    // Check employee collection
    echo "\n--- Checking employee collection ---\n";
    $cursor = $database->selectCollection("employee")->find($filter);
    $results = [];
    foreach ($cursor as $document) {
        $results[] = $document;
        echo "Found: " . $document['personal_info']['first_name'] . " " . 
             $document['personal_info']['middle_name'] . " " . 
             $document['personal_info']['last_name'] . "\n";
        echo "Full document: " . json_encode($document, JSON_PRETTY_PRINT) . "\n\n";
    }

    if (empty($results)) {
        echo "No results found in employee collection\n";
    }

    // Check applicants collection
    echo "\n--- Checking applicants collection ---\n";
    $cursor = $database->selectCollection("applicants")->find($filter);
    $results = [];
    foreach ($cursor as $document) {
        $results[] = $document;
        echo "Found: " . $document['personal_info']['first_name'] . " " . 
             $document['personal_info']['middle_name'] . " " . 
             $document['personal_info']['last_name'] . "\n";
        echo "Full document: " . json_encode($document, JSON_PRETTY_PRINT) . "\n\n";
    }

    if (empty($results)) {
        echo "No results found in applicants collection\n";
    }

    // Try a broader search
    echo "\n--- Broad search in employee collection ---\n";
    $broadFilter = [
        '$text' => ['$search' => 'Princes Lyka']
    ];
    try {
        $cursor = $database->selectCollection("employee")->find($broadFilter);
        foreach ($cursor as $document) {
            echo "Found with text search: " . json_encode($document, JSON_PRETTY_PRINT) . "\n\n";
        }
    } catch (Exception $e) {
        echo "Text search failed: " . $e->getMessage() . "\n";
    }

    echo "\n--- Broad search in applicants collection ---\n";
    try {
        $cursor = $database->selectCollection("applicants")->find($broadFilter);
        foreach ($cursor as $document) {
            echo "Found with text search: " . json_encode($document, JSON_PRETTY_PRINT) . "\n\n";
        }
    } catch (Exception $e) {
        echo "Text search failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error connecting to MongoDB: " . $e->getMessage() . "\n";
}
?>