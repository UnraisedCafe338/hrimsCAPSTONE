<?php
// Test the AI data query endpoint
echo "<h2>Testing AI Data Query Endpoint</h2>";

// Test 1: Search for IS graduates
echo "<h3>Test 1: Search for IS graduates</h3>";
$test_url = "http://localhost/hrims/handlers/ai_data_query.php?search=information%20system&type=education&collection=employee";
echo "<p>URL: $test_url</p>";

$response = file_get_contents($test_url);
$data = json_decode($response, true);
echo "<pre>" . print_r($data, true) . "</pre>";

// Test 2: Search for skills
echo "<h3>Test 2: Search for programming skills</h3>";
$test_url2 = "http://localhost/hrims/handlers/ai_data_query.php?search=programming&type=skills&collection=employee";
echo "<p>URL: $test_url2</p>";

$response2 = file_get_contents($test_url2);
$data2 = json_decode($response2, true);
echo "<pre>" . print_r($data2, true) . "</pre>";

// Test 3: General search
echo "<h3>Test 3: General search</h3>";
$test_url3 = "http://localhost/hrims/handlers/ai_data_query.php?search=faculty&type=general&collection=employee";
echo "<p>URL: $test_url3</p>";

$response3 = file_get_contents($test_url3);
$data3 = json_decode($response3, true);
echo "<pre>" . print_r($data3, true) . "</pre>";
?>