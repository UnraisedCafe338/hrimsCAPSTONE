<?php
header('Content-Type: application/json');

function checkAIServer() {
    $url = 'http://127.0.0.1:8000/status';
    
    // Create stream context with timeout
    $context = stream_context_create([
        'http' => [
            'timeout' => 3,
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    // Try to get the status
    $result = @file_get_contents($url, false, $context);
    
    if ($result !== false) {
        $data = json_decode($result, true);
        if ($data && isset($data['status']) && $data['status'] === 'running') {
            return [
                'status' => 'running',
                'gpu_layers' => $data['gpu_layers'] ?? 0,
                'model' => $data['model'] ?? 'unknown'
            ];
        } else {
            return ['status' => 'not_responding'];
        }
    } else {
        // Check if port is open by trying to connect
        $connection = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 3);
        if ($connection) {
            fclose($connection);
            return ['status' => 'port_open_but_no_response'];
        } else {
            return ['status' => 'not_running'];
        }
    }
}

$status = checkAIServer();
echo json_encode($status);
?>
