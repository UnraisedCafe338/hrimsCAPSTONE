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
  
  <div id="chatArea"></div>

  <input type="file" id="docUpload" accept=".pdf,.docx" />
  <button onclick="uploadDocument()">Upload Document</button>

  <textarea id="userInput" placeholder="Ask the AI..."></textarea>
  <button onclick="sendToAI()" id="sendBtn">Send</button>
  <button onclick="clearChat()" style="background:#d33;">Clear Chat</button>
</div>

<script>

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

  function sanitize(input) {
    const element = document.createElement('div');
    element.innerText = input;
    return element.innerHTML;
  }
</script>
