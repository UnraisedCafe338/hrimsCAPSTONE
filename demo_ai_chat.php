<?php
// This is a demo page to showcase the new AI chat interface
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIMS AI Assistant Demo</title>
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
        }
    </style>
</head>
<body>
    <header>
        <h1>HRIMS AI Assistant</h1>
        <div class="subtitle">Messenger-Style Chat Interface Demo</div>
    </header>
    
    <div class="container">
        <div class="features">
            <div class="feature-card">
                <h3><i class="fas fa-comments"></i> Modern Chat UI</h3>
                <p>Messenger-style interface with distinct message bubbles for user and AI, complete with avatars and timestamps.</p>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-bolt"></i> Real-time Interaction</h3>
                <p>Experience seamless conversation with typing indicators and smooth animations.</p>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-mobile-alt"></i> Fully Responsive</h3>
                <p>Works beautifully on desktop, tablet, and mobile devices with adaptive layout.</p>
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
    <?php include 'aisidebar.php'; ?>
    
    <script>
        // Demo function to open the chat
        function openDemoChat() {
            if (typeof openAIChat === 'function') {
                openAIChat();
            } else {
                alert('AI chat system not properly loaded. Please refresh the page.');
            }
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