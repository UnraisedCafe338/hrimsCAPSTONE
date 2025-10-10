<?php
// This is a complete test page for the new AI chat interface with full functionality
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIMS AI Assistant - Full Test</title>
    <link rel="stylesheet" href="/hrims/assets/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-top: 10px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        
        .feature-card h3 {
            color: #2575fc;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-card i {
            font-size: 1.5rem;
        }
        
        .instructions {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .instructions h2 {
            color: #6a11cb;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .instructions ol {
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(37, 117, 252, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 117, 252, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .demo-area {
            text-align: center;
            margin: 40px 0;
        }
        
        .test-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .test-section h2 {
            color: #6a11cb;
            margin-top: 0;
        }
        
        .test-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
        }
        
        .test-btn {
            background: #2575fc;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .test-btn:hover {
            background: #1a68e8;
        }
        
        .response-area {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            min-height: 100px;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
            
            .test-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>HRIMS AI Assistant</h1>
        <div class="subtitle">Full Functionality Test</div>
    </header>
    
    <div class="container">
        <div class="features">
            <div class="feature-card">
                <h3><i class="fas fa-comments"></i> Modern Chat UI</h3>
                <p>Messenger-style interface with distinct message bubbles for user and AI, complete with avatars and timestamps.</p>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-database"></i> Database Integration</h3>
                <p>Direct integration with HRIMS database to find employees, applicants, and other HR data.</p>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-brain"></i> AI-Powered Responses</h3>
                <p>Powered by LLM technology to provide intelligent, contextual responses to HR queries.</p>
            </div>
        </div>
        
        <div class="instructions">
            <h2>How to Use the AI Assistant</h2>
            <ol>
                <li>Look for the floating AI button in the bottom-right corner of your screen</li>
                <li>Click the button to open the AI chat interface</li>
                <li>Type your question in the message input field</li>
                <li>Press Enter or click the send button to submit your message</li>
                <li>Watch for the typing indicator while the AI formulates a response</li>
                <li>Click the X button or outside the chat window to close the interface</li>
            </ol>
        </div>
        
        <div class="test-section">
            <h2>Backend Functionality Tests</h2>
            <p>Test the backend components of the AI system:</p>
            
            <div class="test-buttons">
                <button class="test-btn" onclick="testAIStatus()">Check AI Status</button>
                <button class="test-btn" onclick="testStartAI()">Start AI Server</button>
                <button class="test-btn" onclick="testStopAI()">Stop AI Server</button>
                <button class="test-btn" onclick="testEmployeeQuery()">Query Employees</button>
                <button class="test-btn" onclick="testGraduateQuery()">Query Graduates</button>
            </div>
            
            <div class="response-area" id="testResponse">
                Click a test button to see results here.
            </div>
        </div>
        
        <div class="demo-area">
            <h2>Experience the New Interface</h2>
            <p>Click the AI button in the bottom-right corner to try the new messenger-style chat</p>
            <button class="btn" onclick="openDemoChat()">Open Demo Chat</button>
        </div>
    </div>
    
    <footer>
        <p>HRIMS AI Assistant &copy; 2025 | Human Resources Information Management System</p>
    </footer>

    <!-- Include the AI chat interface -->
    <?php include 'users/admin/aisidebar.php'; ?>
    
    <script>
        // Demo function to open the chat
        function openDemoChat() {
            if (typeof openAIChat === 'function') {
                openAIChat();
            } else {
                alert('AI chat system not properly loaded. Please refresh the page.');
            }
        }
        
        // Test functions
        function testAIStatus() {
            fetch('handlers/ai/check_ai_status.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>AI Status Check Result:</h3>' +
                        '<p><strong>Status:</strong> ' + data.status + '</p>' +
                        (data.gpu_layers !== undefined ? '<p><strong>GPU Layers:</strong> ' + data.gpu_layers + '</p>' : '') +
                        (data.message ? '<p><strong>Message:</strong> ' + data.message + '</p>' : '');
                })
                .catch(error => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Error:</h3><p>' + error.message + '</p>';
                });
        }
        
        function testStartAI() {
            fetch('handlers/ai/control_ai.php?action=start')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Start AI Server Result:</h3><p>' + data + '</p>';
                })
                .catch(error => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Error:</h3><p>' + error.message + '</p>';
                });
        }
        
        function testStopAI() {
            fetch('handlers/ai/control_ai.php?action=stop')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Stop AI Server Result:</h3><p>' + data + '</p>';
                })
                .catch(error => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Error:</h3><p>' + error.message + '</p>';
                });
        }
        
        function testEmployeeQuery() {
            fetch('handlers/ai/ai_data_query.php?search=engineer&type=general&collection=employee')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<h3>Employee Query Result:</h3>';
                        html += '<p>Found ' + data.count + ' employees</p>';
                        html += '<ul>';
                        data.data.slice(0, 5).forEach(employee => {
                            html += '<li>' + employee.name + ' - ' + employee.position + '</li>';
                        });
                        html += '</ul>';
                        document.getElementById('testResponse').innerHTML = html;
                    } else {
                        document.getElementById('testResponse').innerHTML = 
                            '<h3>Query Error:</h3><p>' + data.error + '</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Error:</h3><p>' + error.message + '</p>';
                });
        }
        
        function testGraduateQuery() {
            fetch('handlers/ai/ai_data_query.php?search=information system&type=education&collection=employee')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<h3>Graduate Query Result:</h3>';
                        html += '<p>Found ' + data.count + ' graduates</p>';
                        html += '<ul>';
                        data.data.slice(0, 5).forEach(employee => {
                            html += '<li>' + employee.name + ' - ' + employee.college_degree + '</li>';
                        });
                        html += '</ul>';
                        document.getElementById('testResponse').innerHTML = html;
                    } else {
                        document.getElementById('testResponse').innerHTML = 
                            '<h3>Query Error:</h3><p>' + data.error + '</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('testResponse').innerHTML = 
                        '<h3>Error:</h3><p>' + error.message + '</p>';
                });
        }
        
        // Add a demo message when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Expose the openAIChat function if not already exposed
            if (typeof openAIChat !== 'function') {
                window.openAIChat = function() {
                    document.getElementById('aiChatModal').classList.add('active');
                };
            }
        });
    </script>
</body>
</html>