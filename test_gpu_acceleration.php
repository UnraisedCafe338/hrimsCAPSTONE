<?php
// Test page for NVIDIA GPU acceleration in AI system
require_once 'config/ai_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIMS AI - NVIDIA GPU Acceleration Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f7fa;
            color: #333;
        }
        header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .test-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .test-section h2 {
            color: #2575fc;
            margin-top: 0;
        }
        .btn {
            background: #2575fc;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1a68e8;
        }
        .btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        .result-area {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            min-height: 100px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .info {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>HRIMS AI System</h1>
        <p>NVIDIA GPU Acceleration Test</p>
    </header>
    
    <div class="test-section">
        <h2>System Configuration</h2>
        <p><strong>Model Path:</strong> <?php echo MODEL_PATH; ?></p>
        <p><strong>LLaMA Runner:</strong> <?php echo LLAMA_RUN_PATH; ?></p>
        <p><strong>GPU Layers:</strong> <?php echo GPU_LAYERS; ?></p>
        <p><strong>Context Size:</strong> <?php echo CONTEXT_SIZE; ?></p>
        <p><strong>Threads:</strong> <?php echo THREADS; ?></p>
    </div>
    
    <div class="test-section">
        <h2>GPU Acceleration Tests</h2>
        <button class="btn" onclick="testDirectExecution()">Test Direct LLaMA Execution</button>
        <button class="btn" onclick="testServerStatus()">Check AI Server Status</button>
        <button class="btn" onclick="testStartServer()">Start AI Server</button>
        <button class="btn" onclick="testStopServer()">Stop AI Server</button>
        
        <div class="result-area" id="testResult">
            Click a test button to see results here.
        </div>
    </div>
    
    <script>
        function testDirectExecution() {
            const resultArea = document.getElementById('testResult');
            resultArea.innerHTML = 'Testing direct LLaMA execution with NVIDIA GPU acceleration...\n';
            
            // In a real implementation, this would make an AJAX call to a PHP script
            // that executes the llama-run.exe command
            resultArea.innerHTML += 'This test would execute: llama-run.exe --ngl <?php echo GPU_LAYERS; ?> --context-size <?php echo CONTEXT_SIZE; ?> --threads <?php echo THREADS; ?> [MODEL_PATH] "Hello, NVIDIA GPU!"\n\n';
            resultArea.innerHTML += 'Expected result: Faster response time and GPU utilization in Task Manager\n';
            resultArea.innerHTML += 'Note: This test requires proper file paths and permissions\n';
        }
        
        function testServerStatus() {
            const resultArea = document.getElementById('testResult');
            resultArea.innerHTML = 'Checking AI server status...\n';
            
            fetch('handlers/ai/check_ai_status.php')
                .then(response => response.json())
                .then(data => {
                    let output = 'AI Server Status Check:\n';
                    output += '========================\n';
                    output += 'Status: ' + data.status + '\n';
                    output += 'Message: ' + data.message + '\n';
                    
                    if (data.gpu_layers !== undefined) {
                        output += 'GPU Layers: ' + data.gpu_layers + '\n';
                    }
                    
                    if (data.model) {
                        output += 'Model: ' + data.model + '\n';
                    }
                    
                    if (data.context_size) {
                        output += 'Context Size: ' + data.context_size + '\n';
                    }
                    
                    if (data.threads) {
                        output += 'Threads: ' + data.threads + '\n';
                    }
                    
                    if (data.gpu_info) {
                        output += 'GPU Info: ' + data.gpu_info + '\n';
                    }
                    
                    resultArea.innerHTML = output;
                    
                    if (data.status === 'running') {
                        resultArea.classList.add('success');
                    } else {
                        resultArea.classList.add('error');
                    }
                })
                .catch(error => {
                    resultArea.innerHTML = 'Error checking server status: ' + error.message;
                    resultArea.classList.add('error');
                });
        }
        
        function testStartServer() {
            const resultArea = document.getElementById('testResult');
            resultArea.innerHTML = 'Starting AI server with NVIDIA GPU acceleration...\n';
            resultArea.classList.remove('success', 'error');
            
            fetch('handlers/ai/control_ai.php?action=start')
                .then(response => response.text())
                .then(data => {
                    resultArea.innerHTML = data;
                    resultArea.classList.add('success');
                })
                .catch(error => {
                    resultArea.innerHTML = 'Error starting server: ' + error.message;
                    resultArea.classList.add('error');
                });
        }
        
        function testStopServer() {
            const resultArea = document.getElementById('testResult');
            resultArea.innerHTML = 'Stopping AI server...\n';
            resultArea.classList.remove('success', 'error');
            
            fetch('handlers/ai/control_ai.php?action=stop')
                .then(response => response.text())
                .then(data => {
                    resultArea.innerHTML = data;
                    resultArea.classList.add('info');
                })
                .catch(error => {
                    resultArea.innerHTML = 'Error stopping server: ' + error.message;
                    resultArea.classList.add('error');
                });
        }
    </script>
</body>
</html>