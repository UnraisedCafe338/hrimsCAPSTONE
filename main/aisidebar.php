<style>
#aiSidebar {
  position: fixed;
  top: 0;
  right: -320px; /* Hidden offscreen by default */
  width: 300px;
  height: 100%;
  background: #f1f1f1;
  border-left: 1px solid #ccc;
  padding: 10px;
  display: flex;
  flex-direction: column;
  z-index: 10000;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
  transition: right 0.4s ease-in-out;
}

#aiSidebar.active {
  right: 0;
}

  #chatArea {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 10px;
    padding-right: 10px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.4;
  }

  /* Style for AI responses */
  .ai-response {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    margin: 8px 0;
    border-left: 4px solid #007bff;
    word-wrap: break-word;
    white-space: pre-wrap;
  }

  .user-message {
    background: #e3f2fd;
    padding: 10px;
    border-radius: 8px;
    margin: 8px 0;
    border-left: 4px solid #2196f3;
  }

  /* Numbered list styling */
  .ai-response ol {
    padding-left: 30px;
    margin: 8px 0;
    max-height: 400px;  /* Limit height for very long lists */
    overflow-y: auto;   /* Add scroll for long lists */
  }

  .ai-response li {
    margin: 4px 0;
    padding: 2px 0;
    line-height: 1.3;
  }

  #userInput {
    width: 100%;
    height: 60px;
    margin-top: 10px;
    padding: 10px;
    font-size: 14px;
  }

 
  .header2 {
    font-weight: bold;
    margin-bottom: 10px;
  }
  .header2 img{
    width: 40px;
    height: 30px;
    margin-left: 5px;
    vertical-align: middle;
  }
</style>

<!-- Assistant Sidebar -->
<div id="aiSidebar" class="collapsed">
  <div class="header2"> Qutie AI <img src='../images/cutie.png'></div>
  
  <div style="margin-bottom: 10px;">
    <button onclick="startAI()" style="background: #4CAF50; color: white; padding: 8px 16px; margin-right: 5px;">🚀 Start AI</button>
    <button onclick="stopAI()" style="background: #f44336; color: white; padding: 8px 16px; margin-right: 5px;">⏹️ Stop AI</button>
    <button onclick="testAI()" style="background: #2196F3; color: white; padding: 8px 16px;">🧪 Test Startup</button>
  </div>
  
  <div style="margin-bottom: 10px;">
    <button onclick="clearChat()" style="background: #d33; color: white; padding: 6px 12px; margin-right: 5px;">🗑️ Clear Chat</button>
    <button onclick="saveChatHistory()" style="background: #9C27B0; color: white; padding: 6px 12px; margin-right: 5px;">💾 Save Chat</button>
    <button onclick="loadChatHistory()" style="background: #607D8B; color: white; padding: 6px 12px;">📂 Load Chat</button>
  </div>
  
  <div id="aiStatus" style="margin-bottom: 10px; padding: 8px; background: #f0f0f0; border-radius: 4px; text-align: center; font-weight: bold;">
    🔴 AI Server Not Running
  </div>

  <div style="margin-bottom: 10px;">
    <button onclick="checkAIStatus()" id="refreshBtn" style="background: #FF9800; color: white; padding: 8px 16px; margin-right: 5px;">🔄 Refresh Status</button>
  </div>

  <div id="chatArea"></div>

  <input type="file" id="docUpload" accept=".pdf,.docx" />
  <button onclick="uploadDocument()">Upload Document</button>

  <textarea id="userInput" placeholder="Ask the AI... (I can remember our conversation!)"></textarea>
  <button onclick="sendToAI()" id="sendBtn">Send</button>
  <div style="margin-top: 5px; font-size: 12px; color: #666;">
    <span id="contextStatus">Context: Ready for new conversation</span>
  </div>
</div>

<script>
// Conversation Memory Management
let conversationHistory = [];
let maxHistoryLength = 10; // Keep last 10 exchanges

// Load conversation history from localStorage on page load
function loadConversationFromStorage() {
  try {
    const saved = localStorage.getItem('hrims_ai_conversation');
    if (saved) {
      conversationHistory = JSON.parse(saved);
      updateContextStatus();
    }
  } catch (e) {
    console.error('Error loading conversation:', e);
    conversationHistory = [];
  }
}

// Save conversation history to localStorage
function saveConversationToStorage() {
  try {
    localStorage.setItem('hrims_ai_conversation', JSON.stringify(conversationHistory));
  } catch (e) {
    console.error('Error saving conversation:', e);
  }
}

// Add message to conversation history
function addToHistory(userMessage, aiResponse) {
  conversationHistory.push({
    user: userMessage,
    ai: aiResponse,
    timestamp: new Date().toISOString()
  });
  
  // Keep only recent messages
  if (conversationHistory.length > maxHistoryLength) {
    conversationHistory = conversationHistory.slice(-maxHistoryLength);
  }
  
  saveConversationToStorage();
  updateContextStatus();
}

// Update context status display
function updateContextStatus() {
  const status = document.getElementById('contextStatus');
  if (conversationHistory.length > 0) {
    status.textContent = `Context: ${conversationHistory.length} previous exchanges remembered`;
    status.style.color = '#4CAF50';
  } else {
    status.textContent = 'Context: Ready for new conversation';
    status.style.color = '#666';
  }
}

// Analyze if current question is a follow-up
function isFollowUpQuestion(currentQuestion) {
  if (conversationHistory.length === 0) return false;
  
  const followUpIndicators = [
    // English follow-up patterns
    /^(what about|how about|and|also|can you|what if)/i,
    /(more|another|other|else|next|continue|expand)/i,
    /(that|this|it|they|them)\s/i,
    /^(show|list|find)\s+(more|other|another)/i,
    
    // Filipino/Taglish follow-up patterns
    /^(paano|ano|at|pano|saka|tapos)/i,
    /(pa|din|rin|naman|lang)/i,
    /(yan|yun|iyan|iyon)/i
  ];
  
  return followUpIndicators.some(pattern => pattern.test(currentQuestion.trim()));
}

// Build context for AI
function buildContextForAI(currentQuestion) {
  if (conversationHistory.length === 0) {
    return currentQuestion;
  }
  
  const isFollowUp = isFollowUpQuestion(currentQuestion);
  
  if (isFollowUp) {
    // Include recent conversation for follow-up questions
    const recentHistory = conversationHistory.slice(-3); // Last 3 exchanges
    let contextPrompt = "Previous conversation context:\n";
    
    recentHistory.forEach((exchange, index) => {
      contextPrompt += `User: ${exchange.user}\n`;
      contextPrompt += `AI: ${exchange.ai}\n\n`;
    });
    
    contextPrompt += `Current follow-up question: ${currentQuestion}`;
    return contextPrompt;
  } else {
    // For new questions, just mention we have context available
    return `${currentQuestion} (Note: I have ${conversationHistory.length} previous exchanges in context if relevant)`;
  }
}

// Helper function to reset all button states
function resetButtonStates() {
  // Reset start button
  const startBtn = document.querySelector('button[onclick="startAI()"]');
  if (startBtn) {
    startBtn.disabled = false;
    startBtn.textContent = "🚀 Start AI";
  }
  
  // Reset stop button
  const stopBtn = document.querySelector('button[onclick="stopAI()"]');
  if (stopBtn) {
    stopBtn.disabled = false;
    stopBtn.textContent = "⏹️ Stop AI";
  }
  
  // Reset refresh button
  const refreshBtn = document.getElementById("refreshBtn");
  if (refreshBtn) {
    refreshBtn.disabled = false;
    refreshBtn.textContent = "🔄 Refresh Status";
  }
}

function startAI() {
  const startBtn = event.target;
  startBtn.disabled = true;
  startBtn.textContent = "Starting...";
  
  // Safety timeout to reset button if it gets stuck
  const safetyTimeout = setTimeout(() => {
    startBtn.textContent = "🚀 Start AI";
    startBtn.disabled = false;
  }, 15000); // Reset after 15 seconds if no response
  
  // Update status immediately
  document.getElementById("aiStatus").innerHTML = "🟡 Starting AI Server...";
  document.getElementById("aiStatus").style.color = "orange";
  
  fetch("../handlers/control_ai.php?action=start")
    .then(res => res.text())
    .then(msg => {
      clearTimeout(safetyTimeout); // Clear safety timeout since we got response
      alert("✅ " + msg);
      
      // Reset button state immediately
      startBtn.textContent = "🚀 Start AI";
      startBtn.disabled = false;
      
      // Check status multiple times to ensure it updates properly
      setTimeout(() => checkAIStatus(), 1000);  // Check after 1 second
      setTimeout(() => checkAIStatus(), 3000);  // Check after 3 seconds
      setTimeout(() => checkAIStatus(), 5000);  // Check after 5 seconds
    })
    .catch(err => {
      clearTimeout(safetyTimeout); // Clear safety timeout
      alert("❌ Error: " + err);
      
      // Reset button state on error
      startBtn.textContent = "🚀 Start AI";
      startBtn.disabled = false;
      
      // Reset status on error
      document.getElementById("aiStatus").innerHTML = "🔴 AI Server Not Running";
      document.getElementById("aiStatus").style.color = "red";
    });
}

function stopAI() {
  const stopBtn = event.target;
  stopBtn.disabled = true;
  stopBtn.textContent = "Stopping...";
  
  // Safety timeout to reset button if it gets stuck
  const safetyTimeout = setTimeout(() => {
    stopBtn.textContent = "⏹️ Stop AI";
    stopBtn.disabled = false;
  }, 15000); // Reset after 15 seconds if no response
  
  // Update status immediately
  document.getElementById("aiStatus").innerHTML = "🟡 Stopping AI Server...";
  document.getElementById("aiStatus").style.color = "orange";
  
  fetch("../handlers/control_ai.php?action=stop")
    .then(res => res.text())
    .then(msg => {
      clearTimeout(safetyTimeout); // Clear safety timeout
      alert("✅ " + msg);
      
      // Reset button state immediately
      stopBtn.textContent = "⏹️ Stop AI";
      stopBtn.disabled = false;
      
      // Update status immediately after stopping
      document.getElementById("aiStatus").innerHTML = "🔴 AI Server Not Running";
      document.getElementById("aiStatus").style.color = "red";
      
      // Double-check status after a delay
      setTimeout(() => checkAIStatus(), 2000);
    })
    .catch(err => {
      clearTimeout(safetyTimeout); // Clear safety timeout
      alert("❌ Error: " + err);
      
      // Reset button state on error
      stopBtn.textContent = "⏹️ Stop AI";
      stopBtn.disabled = false;
      
      // Check status to see if it actually stopped
      setTimeout(() => checkAIStatus(), 1000);
    });
}

function testAI() {
  const testBtn = event.target;
  testBtn.disabled = true;
  testBtn.textContent = "Testing...";
  
  // Open test page in new window
  const testWindow = window.open("../handlers/test_ai_start.php", "AI_Test", "width=800,height=600");
  
  setTimeout(() => {
    testBtn.textContent = "🧪 Test Startup";
    testBtn.disabled = false;
  }, 1000);
}

function checkAIStatus() {
  // Show loading state on refresh button
  const refreshBtn = document.getElementById("refreshBtn");
  const originalText = refreshBtn.textContent;
  refreshBtn.disabled = true;
  refreshBtn.textContent = "⏳ Checking...";
  
  // Use server-side status checker to avoid CORS issues
  fetch("../handlers/check_ai_status.php")
    .then(response => response.json())
    .then(data => {
      if (data.status === "running") {
        const gpuInfo = data.gpu_layers > 0 ? ` (GPU: ${data.gpu_layers} layers)` : " (CPU mode)";
        document.getElementById("aiStatus").innerHTML = "🟢 AI Server Running" + gpuInfo;
        document.getElementById("aiStatus").style.color = "green";
      } else if (data.status === "port_open_but_no_response") {
        document.getElementById("aiStatus").innerHTML = "🟡 AI Server Port Open (Starting up...)";
        document.getElementById("aiStatus").style.color = "orange";
      } else if (data.status === "not_responding") {
        document.getElementById("aiStatus").innerHTML = "🔴 AI Server Not Responding";
        document.getElementById("aiStatus").style.color = "red";
      } else {
        document.getElementById("aiStatus").innerHTML = "🔴 AI Server Not Running";
        document.getElementById("aiStatus").style.color = "red";
      }
    })
    .catch(error => {
      document.getElementById("aiStatus").innerHTML = "🔴 Error Checking Status";
      document.getElementById("aiStatus").style.color = "red";
      console.error("Status check error:", error);
    })
    .finally(() => {
      // Always restore refresh button regardless of result
      refreshBtn.disabled = false;
      refreshBtn.textContent = originalText;
    });
}



// Check status every 30 seconds (less frequent to avoid spam)
setInterval(checkAIStatus, 30000);

// Check status immediately when page loads
document.addEventListener('DOMContentLoaded', function() {
  resetButtonStates();  // Ensure buttons are in correct state
  loadConversationFromStorage();  // Load conversation history
  checkAIStatus();
});

// Also check status when the page becomes visible (user returns to tab)
document.addEventListener('visibilitychange', function() {
  if (!document.hidden) {
    resetButtonStates();  // Reset buttons when user returns
    checkAIStatus();
  }
});

function sendToAI(prompt = null) {
  const userInputBox = document.getElementById("userInput");
  const chatArea = document.getElementById("chatArea");
  const sendBtn = document.getElementById("sendBtn");

  const userInput = prompt !== null ? prompt.trim() : userInputBox.value.trim();
  if (!userInput) return;

  // Show user's message
  const userMessage = `<div class="user-message"><strong>You:</strong> ${sanitize(userInput)}</div>`;
  chatArea.insertAdjacentHTML("beforeend", userMessage);
  chatArea.scrollTop = chatArea.scrollHeight;

  if (prompt === null) userInputBox.value = "";

  // Build context-aware prompt
  const contextualPrompt = buildContextForAI(userInput);
  const isFollowUp = isFollowUpQuestion(userInput);
  
  // Update UI to show context status
  if (isFollowUp) {
    const contextIndicator = `<div style="font-size: 11px; color: #9C27B0; margin: 2px 0;"><em>🔗 Follow-up detected - using conversation context</em></div>`;
    chatArea.insertAdjacentHTML("beforeend", contextIndicator);
  }

  // Show AI thinking message
  const tempMsgId = "temp-ai-msg-" + Date.now();
  const thinking = `<div id="${tempMsgId}"><strong>AI:</strong> <em>Thinking...</em></div>`;
  chatArea.insertAdjacentHTML("beforeend", thinking);
  chatArea.scrollTop = chatArea.scrollHeight;

  sendBtn.disabled = true;
  sendBtn.textContent = "Sending...";

  fetch("process_ai.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "prompt=" + encodeURIComponent(contextualPrompt)
  })
  .then(response => response.text())
  .then(data => {
    const cleaned = data.replace(/llama_.*?\n/g, "").trim();

    const msgEl = document.getElementById(tempMsgId);
    
    // Format the response with better styling
    let formattedResponse = cleaned;
    
    // Convert numbered lists to HTML ol/li format
    if (formattedResponse.match(/^\d+\./m)) {
      const lines = formattedResponse.split('\n');
      let htmlContent = '';
      let inList = false;
      
      for (let line of lines) {
        if (line.match(/^\d+\./)) {
          if (!inList) {
            htmlContent += '<ol>';
            inList = true;
          }
          htmlContent += `<li>${line.replace(/^\d+\.\s*/, '')}</li>`;
        } else if (line.trim() === '') {
          if (inList) {
            htmlContent += '</ol>';
            inList = false;
          }
          htmlContent += '<br>';
        } else {
          if (inList) {
            htmlContent += '</ol>';
            inList = false;
          }
          htmlContent += line + '<br>';
        }
      }
      
      if (inList) {
        htmlContent += '</ol>';
      }
      
      msgEl.innerHTML = `<div class="ai-response"><strong>AI:</strong><br>${htmlContent}</div>`;
    } else {
      msgEl.innerHTML = `<div class="ai-response"><strong>AI:</strong> ${sanitize(cleaned)}</div>`;
    }
    
    // Save to conversation history
    addToHistory(userInput, cleaned);
    
    // Always re-enable send button after processing response
    sendBtn.disabled = false;
    sendBtn.textContent = "Send";
    chatArea.scrollTop = chatArea.scrollHeight;
  })
  .catch(error => {
    const msgEl = document.getElementById(tempMsgId);
    msgEl.innerHTML = `<div class="ai-response"><strong>AI:</strong> <span style="color:red;">Error: ${sanitize(error.message || "Something went wrong.")}</span></div>`;
    sendBtn.disabled = false;
    sendBtn.textContent = "Send";
  });
}

  function clearChat() {
    const chatArea = document.getElementById("chatArea");
    chatArea.innerHTML = "";
    
    // Also clear conversation history
    conversationHistory = [];
    saveConversationToStorage();
    updateContextStatus();
    
    // Show confirmation
    chatArea.innerHTML = '<div style="text-align: center; padding: 20px; color: #666; font-style: italic;">Chat cleared. Starting fresh conversation...</div>';
  }
  
  // Save chat history to file
  function saveChatHistory() {
    if (conversationHistory.length === 0) {
      alert('No conversation history to save!');
      return;
    }
    
    const timestamp = new Date().toLocaleString().replace(/[/\\?%*:|"<>]/g, '-');
    const filename = `HRIMS_AI_Chat_${timestamp}.txt`;
    
    let content = `HRIMS AI Conversation History\n`;
    content += `Saved: ${new Date().toLocaleString()}\n`;
    content += `Total Exchanges: ${conversationHistory.length}\n\n`;
    content += '='.repeat(50) + '\n\n';
    
    conversationHistory.forEach((exchange, index) => {
      content += `[${index + 1}] User: ${exchange.user}\n\n`;
      content += `    AI: ${exchange.ai}\n\n`;
      content += '-'.repeat(30) + '\n\n';
    });
    
    // Create and download file
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
    
    alert(`Chat history saved as: ${filename}`);
  }
  
  // Load chat history (restore from localStorage)
  function loadChatHistory() {
    try {
      loadConversationFromStorage();
      
      if (conversationHistory.length === 0) {
        alert('No saved conversation history found.');
        return;
      }
      
      const chatArea = document.getElementById("chatArea");
      chatArea.innerHTML = '<div style="text-align: center; padding: 10px; background: #e3f2fd; border-radius: 5px; margin: 5px 0;"><strong>📂 Loaded conversation history</strong><br>Restored ' + conversationHistory.length + ' previous exchanges</div>';
      
      // Optionally display recent history in chat
      const recentHistory = conversationHistory.slice(-3);
      recentHistory.forEach(exchange => {
        chatArea.innerHTML += `<div class="user-message"><strong>You:</strong> ${sanitize(exchange.user)}</div>`;
        chatArea.innerHTML += `<div class="ai-response"><strong>AI:</strong> ${sanitize(exchange.ai)}</div>`;
      });
      
      chatArea.scrollTop = chatArea.scrollHeight;
      updateContextStatus();
      
      alert(`Loaded ${conversationHistory.length} previous exchanges. I can now continue our conversation!`);
    } catch (e) {
      console.error('Error loading chat history:', e);
      alert('Error loading chat history.');
    }
  }
  
  // Safe HTML escape
  function sanitize(input) {
    const div = document.createElement("div");
    div.textContent = input;
    return div.innerHTML;
  }
  
  function uploadDocument() {
    const fileInput = document.getElementById('docUpload');
    const file = fileInput.files[0];
    const chatArea = document.getElementById("chatArea");

    if (!file) {
      alert("Please select a document.");
      return;
    }

    // ✅ Show filename in chat
    chatArea.innerHTML += `<div class="user-message"><strong>You:</strong> Uploaded file: ${sanitize(file.name)}</div>`;
    chatArea.scrollTop = chatArea.scrollHeight;

    const formData = new FormData();
    formData.append("resume", file);

    // ✅ Upload and immediately summarize
    fetch("../handlers/upload_resume.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.ai_response) {
        chatArea.innerHTML += `<div class="ai-response"><strong>AI:</strong> ${sanitize(data.ai_response)}</div>`;
      } else {
        chatArea.innerHTML += `<div class="ai-response"><strong>AI:</strong> ${sanitize(data.message || 'Upload complete.')}</div>`;
      }
      chatArea.scrollTop = chatArea.scrollHeight;
    })
    .catch(error => {
      alert("Error uploading file: " + error);
    });
  }
</script>
