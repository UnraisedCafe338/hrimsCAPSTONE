<!DOCTYPE html>
<html>
<head>
    <title>Test New AI Features</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .feature { margin: 20px 0; padding: 15px; border-left: 4px solid #2575fc; background: #f8f9fa; }
        .feature h3 { margin-top: 0; color: #333; }
        .btn { background: #2575fc; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #1a68e8; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test New AI Features</h1>
        <p>This page tests the new AI chat features implementation.</p>
        
        <div class="feature">
            <h3>1. Enhanced UI/UX</h3>
            <p>The AI chat interface has been redesigned with a messenger-style layout:</p>
            <ul>
                <li>Modern chat bubbles for user and AI messages</li>
                <li>Improved positioning and sizing</li>
                <li>Better visual hierarchy with avatars and status indicators</li>
                <li>Responsive design for different screen sizes</li>
            </ul>
        </div>
        
        <div class="feature">
            <h3>2. FAQ System</h3>
            <p>Dynamic FAQ buttons are loaded from the database:</p>
            <button class="btn" onclick="testFAQs()">Test FAQ Loading</button>
            <div id="faqResults"></div>
        </div>
        
        <div class="feature">
            <h3>3. Conversation History</h3>
            <p>Chat sessions are saved and can be accessed later:</p>
            <button class="btn" onclick="testSessions()">Test Session Loading</button>
            <div id="sessionResults"></div>
        </div>
        
        <div class="feature">
            <h3>4. Database Collections</h3>
            <p>The system uses two new MongoDB collections:</p>
            <pre>
faqs - Stores frequently asked questions and answers
ai_chat_sessions - Stores conversation history
            </pre>
        </div>
    </div>

    <script>
        function testFAQs() {
            fetch('users/admin/get_faqs.php')
                .then(response => response.json())
                .then(data => {
                    const results = document.getElementById('faqResults');
                    if (data.length > 0) {
                        results.innerHTML = '<p style="color: green;">✓ FAQs loaded successfully (' + data.length + ' FAQs found)</p>';
                        results.innerHTML += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    } else {
                        results.innerHTML = '<p style="color: orange;">⚠ No FAQs found (this is normal if not initialized yet)</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('faqResults').innerHTML = '<p style="color: red;">✗ Error loading FAQs: ' + error.message + '</p>';
                });
        }
        
        function testSessions() {
            fetch('users/admin/get_chat_sessions.php')
                .then(response => response.json())
                .then(data => {
                    const results = document.getElementById('sessionResults');
                    if (data.length >= 0) {
                        results.innerHTML = '<p style="color: green;">✓ Chat sessions endpoint working (found ' + data.length + ' sessions)</p>';
                    } else {
                        results.innerHTML = '<p style="color: orange;">⚠ No sessions found (this is normal for new installations)</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('sessionResults').innerHTML = '<p style="color: red;">✗ Error loading sessions: ' + error.message + '</p>';
                });
        }
    </script>
</body>
</html>