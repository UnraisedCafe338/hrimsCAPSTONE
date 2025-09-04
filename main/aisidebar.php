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
  
  <div id="aiStatus" style="margin-bottom: 10px; padding: 8px; background: #f0f0f0; border-radius: 4px; text-align: center; font-weight: bold;">
    🔴 AI Server Not Running
  </div>

  <div style="margin-bottom: 10px;">
    <button onclick="checkAIStatus()" id="refreshBtn" style="background: #FF9800; color: white; padding: 8px 16px; margin-right: 5px;">🔄 Refresh Status</button>
  </div>

  <div id="chatArea"></div>

  <input type="file" id="docUpload" accept=".pdf,.docx" />
  <button onclick="uploadDocument()">Upload Document</button>

  <textarea id="userInput" placeholder="Ask the AI..."></textarea>
  <button onclick="sendToAI()" id="sendBtn">Send</button>
  <button onclick="clearChat()" style="background:#d33;">Clear Chat</button>
</div>

<script>
function startAI() {
  const startBtn = event.target;
  startBtn.disabled = true;
  startBtn.textContent = "Starting...";
  
  // Update status immediately
  document.getElementById("aiStatus").innerHTML = "🟡 Starting AI Server...";
  document.getElementById("aiStatus").style.color = "orange";
  
  fetch("../handlers/control_ai.php?action=start")
    .then(res => res.text())
    .then(msg => {
      alert("✅ " + msg);
      startBtn.textContent = "🚀 Start AI";
      startBtn.disabled = false;
      
      // Check status after a short delay to update the display
      setTimeout(() => {
        checkAIStatus();
      }, 2000);
    })
    .catch(err => {
      alert("❌ Error: " + err);
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
  
  // Update status immediately
  document.getElementById("aiStatus").innerHTML = "🟡 Stopping AI Server...";
  document.getElementById("aiStatus").style.color = "orange";
  
  fetch("../handlers/control_ai.php?action=stop")
    .then(res => res.text())
    .then(msg => {
      alert("✅ " + msg);
      stopBtn.textContent = "⏹️ Stop AI";
      stopBtn.disabled = false;
      
      // Update status immediately after stopping
      document.getElementById("aiStatus").innerHTML = "🔴 AI Server Not Running";
      document.getElementById("aiStatus").style.color = "red";
    })
    .catch(err => {
      alert("❌ Error: " + err);
      stopBtn.textContent = "⏹️ Stop AI";
      stopBtn.disabled = false;
      
      // Check status to see if it actually stopped
      setTimeout(() => {
        checkAIStatus();
      }, 1000);
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
      // Restore refresh button
      refreshBtn.disabled = false;
      refreshBtn.textContent = originalText;
    });
}



// Check status every 30 seconds (less frequent to avoid spam)
setInterval(checkAIStatus, 30000);

// Check status immediately when page loads
document.addEventListener('DOMContentLoaded', function() {
  checkAIStatus();
});

// Also check status when the page becomes visible (user returns to tab)
document.addEventListener('visibilitychange', function() {
  if (!document.hidden) {
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
  const userMessage = `<div><strong>You:</strong> ${sanitize(userInput)}</div>`;
  chatArea.insertAdjacentHTML("beforeend", userMessage);
  chatArea.scrollTop = chatArea.scrollHeight;

  if (prompt === null) userInputBox.value = "";

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
    body: "prompt=" + encodeURIComponent(userInput)
  })
  .then(response => response.text())
  .then(data => {
    const cleaned = data.replace(/llama_.*?\n/g, "").trim();

    const msgEl = document.getElementById(tempMsgId);
    msgEl.innerHTML = `<strong>AI:</strong> <span id="aiTyped-${tempMsgId}"></span>`;
    const target = document.getElementById(`aiTyped-${tempMsgId}`);

    let i = 0;
    const interval = setInterval(() => {
      if (i < cleaned.length) {
        target.innerHTML += sanitize(cleaned.charAt(i));
        i++;
      } else {
        clearInterval(interval);
        sendBtn.disabled = false;
        sendBtn.textContent = "Send";
      }
      chatArea.scrollTop = chatArea.scrollHeight;
    }, 15);
  })
  .catch(error => {
    const msgEl = document.getElementById(tempMsgId);
    msgEl.innerHTML = `<strong>AI:</strong> <span style="color:red;">Error: ${sanitize(error.message || "Something went wrong.")}</span>`;
    sendBtn.disabled = false;
    sendBtn.textContent = "Send";
  });
}

  function clearChat() {
    const chatArea = document.getElementById("chatArea");
    chatArea.innerHTML = "";
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
    chatArea.innerHTML += `<div><strong>You:</strong> Uploaded file: ${sanitize(file.name)}</div>`;
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
        chatArea.innerHTML += `<div><strong>AI:</strong> ${sanitize(data.ai_response)}</div>`;
      } else {
        chatArea.innerHTML += `<div><strong>AI:</strong> ${sanitize(data.message || 'Upload complete.')}</div>`;
      }
      chatArea.scrollTop = chatArea.scrollHeight;
    })
    .catch(error => {
      alert("Error uploading file: " + error);
    });
  }
</script>
