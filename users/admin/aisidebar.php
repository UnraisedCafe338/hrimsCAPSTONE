<style>
/* Messenger-style AI Chat UI */
#aiChatModal {
  position: fixed;
  bottom: 80px;
  right: 100px;
  width: 750px; /* Increased width */
  height: 600px; /* Increased height */
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  z-index: 10000;
  transform: translateY(20px);
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

#aiChatModal.active {
  transform: translateY(0);
  opacity: 1;
  visibility: visible;
}

.ai-chat-header {
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  color: white;
  padding: 15px 20px;
  border-radius: 12px 12px 0 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.ai-chat-header .header-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ai-chat-header .avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: blueviolet;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #2575fc;
}

.ai-chat-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.ai-chat-header .controls {
  display: flex;
  gap: 10px;
}

.ai-chat-header .controls button {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.ai-chat-header .controls button:hover {
  background: rgba(255, 255, 255, 0.3);
}

.ai-chat-header .memories-link {
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.08);
  padding: 6px 10px;
  border-radius: 6px;
  color: white;
  text-decoration: none;
  font-size: 13px;
}

#chatMessages {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.chat-message {
  max-width: 85%;
  padding: 12px 16px;
  border-radius: 18px;
  font-size: 14px;
  line-height: 1.4;
  position: relative;
  animation: fadeIn 0.2s ease;
}
.close-button-sidebar {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
  margin-left: 250px;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

.ai-message {
  background: #f0f0f0;
  border-bottom-left-radius: 5px;
  align-self: flex-start;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.user-message {
  background: #2575fc;
  color: white;
  border-bottom-right-radius: 5px;
  align-self: flex-end;
  box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.message-sender {
  font-weight: 600;
  font-size: 12px;
  margin-bottom: 4px;
  display: block;
}

.message-content {
  margin: 0;
  white-space: pre-wrap;
}

.message-time {
  font-size: 10px;
  text-align: right;
  margin-top: 5px;
  opacity: 0.7;
}

.chat-input-container {
  padding: 15px;
  border-top: 1px solid #eee;
  display: flex;
  gap: 10px;
}

.chat-input-container textarea {
  flex: 1;
  border: 1px solid #ddd;
  border-radius: 20px;
  padding: 10px 15px;
  resize: none;
  height: 40px;
  font-family: inherit;
  font-size: 14px;
}

.chat-input-container button {
  background: #2575fc;
  color: white;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.chat-input-container button:hover {
  background: #1a68e8;
}

.chat-input-container button:disabled {
  background: #cccccc;
  cursor: not-allowed;
}

.ai-status {
  padding: 10px 15px;
  background: #f8f9fa;
  border-bottom: 1px solid #eee;
  font-size: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}

.status-indicator.red { background: #ff4d4d; }
.status-indicator.green { background: #4CAF50; }
.status-indicator.orange { background: #FF9800; }

/* Floating AI Button */
.aibutton {
  position: fixed;
  bottom: 40px;
  right: 7px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  color: white;
  font-size: 24px;
  border: none;
  box-shadow: 0 4px 15px rgba(37, 117, 252, 0.4);
  cursor: pointer;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.aibutton:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(37, 117, 252, 0.6);
}

.aibutton img {
  width: 30px;
  height: 30px;
}

/* Typing indicator */
.typing-indicator {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  background: #f0f0f0;
  border-radius: 18px;
  align-self: flex-start;
  width: 80px;
}

.typing-indicator span {
  width: 8px;
  height: 8px;
  background: #999;
  border-radius: 50%;
  display: inline-block;
  margin: 0 2px;
  animation: typing 1s infinite;
}

.typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

/* FAQ Section */
.faq-section {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 15px;
  margin: 10px 0;
}

.faq-section h4 {
  margin-top: 0;
  color: #333;
  font-size: 16px;
}

.faq-buttons {
  /* Two-row horizontal scroller: lay out items in columns and allow horizontal scrolling */
  display: grid;
  grid-auto-flow: column; /* create columns */
  /* fixed column width so multiple columns are visible and scrolling works predictably */
  grid-auto-columns: 220px; /* each column holds up to 2 items stacked */
  grid-template-rows: repeat(2, auto); /* two rows visible */
  gap: 10px;
  margin-top: 10px;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
  width: 100%;
  /* fix height to two rows so it doesn't expand vertically */
  height: 100px; /* increase slightly to accommodate wrapped text */
  padding-bottom: 6px;
  align-items: start;
}

/* Scrollbar styling */
.faq-buttons::-webkit-scrollbar {
  height: 8px;
}
.faq-buttons::-webkit-scrollbar-thumb {
  background: #ced4da;
  border-radius: 4px;
}
.faq-buttons::-webkit-scrollbar-track {
  background: transparent;
}

.faq-button {
  background: #e9ecef;
  border: 1px solid #ced4da;
  border-radius: 20px;
  padding: 6px 12px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}

.faq-button:hover {
  background: #2575fc;
  color: white;
  border-color: #2575fc;
}

/* Make sure each button uses the full column width and text wraps nicely */
.faq-button {
  display: inline-block;
  width: 100%;
  min-height: 36px;
  box-sizing: border-box;
  text-align: center;
  padding: 8px 10px;
  white-space: normal;
  word-break: break-word;
}

/* Chat Sessions */
.chat-sessions {
  display: flex;
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  background: #f8f9fa;
  gap: 10px;
  overflow-x: auto;
}

.session-button {
  background: #e9ecef;
  border: none;
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.session-button.active {
  background: #2575fc;
  color: white;
}

.session-button:hover:not(.active) {
  background: #dee2e6;
}

.new-chat-btn {
  background: #28a745;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.new-chat-btn:hover {
  background: #218838;
}

/* Chat Tabs */
.chat-tabs {
  display: flex;
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  background: #f8f9fa;
  gap: 10px;
}

/* Sessions Sidebar styles */
.session-sidebar {
  position: fixed;
  right: 750px; /* sit to the left of the chat modal (450px width + 20px spacing + 16px buffer) */
  bottom: 80px;
  width: 300px;
  max-height: 560px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 6px 26px rgba(0,0,0,0.2);
  overflow: auto;
  z-index: 10001;
  display: flex;
  flex-direction: column;
  transition: transform 0.18s ease, opacity 0.18s ease;
}

.session-sidebar.collapsed {
  transform: translateX(12px) scale(0.98);
  opacity: 0;
  pointer-events: none;
}

.session-sidebar .sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px;
  border-bottom: 1px solid #eee;
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  color: #fff;
  border-radius: 10px 10px 0 0;
}

.saved-sessions-list {
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.session-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  background: #f8f9fa;
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.session-title {
  flex: 1 1 auto;
  font-size: 13px;
  font-weight: 600;
  color: #222;
  cursor: pointer;
}

.session-meta {
  font-size: 11px;
  color: #666;
  margin-left: 8px;
  white-space: nowrap;
}

.delete-session {
  background: transparent;
  border: none;
  color: #c0392b;
  font-size: 16px;
  cursor: pointer;
  padding: 2px 6px;
}


.tab-button {
  flex: 1;
  background: #e9ecef;
  border: none;
  border-radius: 20px;
  padding: 8px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.tab-button.active {
  background: #2575fc;
  color: white;
}

.tab-button:hover:not(.active) {
  background: #dee2e6;
}

/* Responsive design */
@media (max-width: 768px) {
  #aiChatModal {
    width: calc(100% - 40px);
    height: 70vh;
    right: 20px;
    bottom: 20px;
  }
}
</style>

<!-- AI Chat Modal -->
<div id="aiChatModal">
  <div class="ai-chat-header">
    <div class="header-info">
      <div class="avatar">
        <img src="/hrims/images/PEARL_logo.png" alt="PEARL AI" style="width: 50px; height: 50px; border-radius: 50%;">
      </div>
      <h3>
        PEARL – Personnel & Employee AI Resource Link
      </h3>
    </div>
    <div class="controls">
      <a class="memories-link" href="/hrims/users/admin/ai_memories.php" target="_blank" title="Open AI Memories">Memories</a>
      <button id="closeChat" title="Close">×</button>
    </div>
  </div>
  
  <!-- ai-status removed: AI server status UI removed because local AI server not used -->
  
  <!-- Chat Tabs -->
  <div class="chat-tabs">
    <button class="tab-button active" data-tab="chat">AI Chat</button>
    <button class="tab-button" data-tab="faq">FAQs</button>
  </div>
  
  <!-- Chat Sessions -->
  <div class="chat-sessions">
    <button class="session-button active" data-session="current">Current Chat</button>
    <button class="new-chat-btn" id="newChatBtn">+ New Chat</button>
    <!-- Sessions sidebar toggle -->
    <button class="session-button" id="openSessionsSidebar" title="Open history">☰</button>
  </div>

  <!-- Sessions Sidebar (collapsible) -->
  <div id="sessionSidebar" class="session-sidebar collapsed" aria-hidden="true">
    <div class="sidebar-header">
      <button id="closeSidebar"class="close-button-sidebar" title="Close">×</button>
      <strong>Conversation History</strong>
    </div>
    <div id="savedSessions" class="saved-sessions-list">
      <!-- Session items will be rendered here -->
    </div>
  </div>
  
  <div id="chatMessages">
    <!-- Welcome message -->
    <div class="chat-message ai-message">
      <span class="message-sender">AI Assistant</span>
      <p class="message-content">Hello! I'm PEARL, your personal HR AI Assistant. How can I help you today?</p>
      <div class="message-time">Just now</div>
    </div>
  </div>
  
  <div id="faqContainer" style="display: none; padding: 15px; overflow-y: auto; flex: 1;">
    <h4 style="margin-top: 0; color: #333;">Frequently Asked Questions:</h4>
    <div id="faqMessages" style="margin-bottom:12px; display:flex; flex-direction:column; gap:10px;">
      <!-- FAQ Q/A messages will appear here when a question is selected -->
    </div>
    <div class="faq-buttons" id="faqButtonsTab">
      <!-- FAQ buttons will be loaded here dynamically -->
    </div>
  </div>
  
  <div class="chat-input-container">
    <textarea id="userInput" placeholder="Type your message..."></textarea>
    <button id="sendMessageBtn">➤</button>
  </div>
</div>

<script>
// Conversation Management
let currentSessionId = 'current';
let chatSessions = {
  'current': []
};
let currentTab = 'chat'; // 'chat' or 'faq'

// DOM Elements
const aiChatModal = document.getElementById('aiChatModal');
const closeChat = document.getElementById('closeChat');
const chatMessages = document.getElementById('chatMessages');
const faqContainer = document.getElementById('faqContainer');
const userInput = document.getElementById('userInput');
const sendMessageBtn = document.getElementById('sendMessageBtn');
// AI server status elements removed (no local AI server)
const faqButtonsTab = document.getElementById('faqButtonsTab');
const newChatBtn = document.getElementById('newChatBtn');
const savedSessions = document.getElementById('savedSessions');
const sessionSidebar = document.getElementById('sessionSidebar');
const openSessionsSidebar = document.getElementById('openSessionsSidebar');
const closeSidebar = document.getElementById('closeSidebar');
const tabButtons = document.querySelectorAll('.tab-button');

// Load FAQs and conversation history
document.addEventListener('DOMContentLoaded', function() {
  loadFAQs();
  loadConversationHistory();
  
  // Expose functions globally for the toggle button
  window.openAIChat = function() {
    aiChatModal.classList.add('active');
    userInput.focus();
  };
  
  window.closeAIChat = closeChatModal;
});

// Load FAQs from server — populate only the FAQ tab, not the inline chat area
function loadFAQs() {
  fetch('get_faqs.php')
    .then(response => response.json())
    .then(faqs => {
      // Populate FAQ buttons in FAQ tab only
      faqButtonsTab.innerHTML = '';
      
      faqs.forEach(faq => {
        const btn = document.createElement('button');
        btn.className = 'faq-button';
        btn.textContent = faq.question;
        // When a FAQ is clicked we want to display the selected question as a "You:" message
        // inside the FAQ tab and then show the AI answer below it.
        btn.onclick = () => sendFAQ(faq.question, faq.answer);
        faqButtonsTab.appendChild(btn);
      });
    })
    .catch(error => {
      console.error('Error loading FAQs:', error);
      // Add some default FAQs if loading fails
      const defaultFAQs = [
        { question: "How do I search for employees?", answer: "You can search for employees by name, position, department, or skills. Try asking questions like 'Find employees with programming skills' or 'List all nursing graduates'." },
        { question: "How do I find applicant information?", answer: "To find applicant information, ask specific questions about their qualifications, education, or skills. For example: 'Show me applicants with a BSIS degree' or 'Find applicants with Java skills'." },
        { question: "What kind of data can I query?", answer: "You can query employee and applicant data including: personal information, education history, work experience, skills, position applied for, department, and more. Try asking specific questions about what you're looking for!" }
      ];

      faqButtonsTab.innerHTML = '';
      defaultFAQs.forEach(faq => {
        const btn = document.createElement('button');
        btn.className = 'faq-button';
        btn.textContent = faq.question;
        btn.onclick = () => sendFAQ(faq.question, faq.answer);
        faqButtonsTab.appendChild(btn);
      });
    });
}

// Load conversation history from server
function loadConversationHistory() {
  fetch('get_chat_sessions.php')
    .then(response => response.json())
    .then(sessions => {
      // Render sessions in the sidebar list
      savedSessions.innerHTML = '';
      const seenSessions = new Set(); // To prevent duplicates
      
      sessions.forEach(session => {
        const sessionId = session._id;
        
        // Skip if we've already processed this session
        if (seenSessions.has(sessionId)) {
          return;
        }
        
        seenSessions.add(sessionId);

        // Create session item
        const item = document.createElement('div');
        item.className = 'session-item';
        item.dataset.session = sessionId;

        const title = document.createElement('div');
        title.className = 'session-title';
        title.textContent = session.title || `Chat ${sessionId.substring(0, 8)}`;
        title.onclick = () => {
          loadChatSession(sessionId);
          // close sidebar for clarity
          collapseSidebar();
        };

        const meta = document.createElement('div');
        meta.className = 'session-meta';
        if (session.created_at) {
          try {
            // Handle different date formats from MongoDB
            let date;
            if (typeof session.created_at === 'object' && session.created_at.$date) {
              // Handle $date format
              date = new Date(session.created_at.$date);
            } else if (typeof session.created_at === 'string') {
              // Handle ISO string format
              date = new Date(session.created_at);
            } else if (session.created_at instanceof Date) {
              // Handle Date object
              date = session.created_at;
            } else {
              // Handle numeric timestamp
              date = new Date(session.created_at);
            }
            
            // Check if date is valid
            if (date instanceof Date && !isNaN(date)) {
              meta.textContent = date.toLocaleString();
            } else {
              meta.textContent = 'Unknown date';
            }
          } catch (e) {
            meta.textContent = 'Invalid date';
          }
        } else {
          meta.textContent = 'No date';
        }

        const delBtn = document.createElement('button');
        delBtn.className = 'delete-session';
        delBtn.title = 'Delete conversation';
        delBtn.textContent = '×';
        delBtn.onclick = (ev) => {
          ev.stopPropagation();
          if (confirm('Delete this conversation? This cannot be undone.')) {
            deleteChatSession(sessionId, item);
          }
        };

        item.appendChild(title);
        item.appendChild(meta);
        item.appendChild(delBtn);
        savedSessions.appendChild(item);
      });
    })
    .catch(error => console.error('Error loading chat sessions:', error));
}

// Create a new chat session
function createNewChat() {
  // Save current session if it has messages
  if (chatSessions[currentSessionId] && chatSessions[currentSessionId].length > 0) {
    saveCurrentSession();
  }
  
  // Create new session with a unique identifier
  const timestamp = Date.now();
  const newSessionId = 'session_' + timestamp;
  currentSessionId = newSessionId;
  chatSessions[currentSessionId] = [];
  
  // Clear chat messages except welcome message
  const welcomeMessage = chatMessages.querySelector('.ai-message');
  chatMessages.innerHTML = '';
  if (welcomeMessage) chatMessages.appendChild(welcomeMessage);
  
  updateSessionButtons();
  
  // Add a temporary session button for the new chat
  addSessionButton(newSessionId, 'New Chat');
}

// Add a session button to the UI
function addSessionButton(sessionId, title) {
  // Check if button already exists
  const existingButton = document.querySelector(`.session-button[data-session="${sessionId}"]`);
  if (existingButton) return;
  
  // Create new session button
  const sessionContainer = document.querySelector('.chat-sessions');
  const newButton = document.createElement('button');
  newButton.className = 'session-button';
  newButton.dataset.session = sessionId;
  newButton.textContent = title;
  
  // Insert before the "+ New Chat" button
  const newChatBtn = document.getElementById('newChatBtn');
  if (newChatBtn && sessionContainer) {
    sessionContainer.insertBefore(newButton, newChatBtn);
  }
  
  // Add click event
  newButton.addEventListener('click', () => {
    loadChatSession(sessionId);
  });
}

// Load a specific chat session
function loadChatSession(sessionId) {
  // If it's a temporary session, just switch to it
  if (sessionId === 'current' || sessionId.startsWith('session_')) {
    currentSessionId = sessionId;
    renderChatMessages();
    updateSessionButtons();
    return;
  }
  
  fetch(`get_chat_session.php?session_id=${sessionId}`)
    .then(response => response.json())
    .then(messages => {
      currentSessionId = sessionId;
      chatSessions[currentSessionId] = messages;
      renderChatMessages();
      updateSessionButtons();
    })
    .catch(error => console.error('Error loading chat session:', error));
}

// Update session button UI
function updateSessionButtons() {
  document.querySelectorAll('.session-button').forEach(btn => {
    btn.classList.remove('active');
  });
  
  const activeButton = document.querySelector(`.session-button[data-session="${currentSessionId}"]`);
  if (activeButton) {
    activeButton.classList.add('active');
  } else {
    // If the current session button doesn't exist, make the "Current Chat" active
    document.querySelector('.session-button[data-session="current"]').classList.add('active');
  }
}

// Render chat messages
function renderChatMessages() {
  // Keep welcome message and FAQ section
  const welcomeMessage = chatMessages.querySelector('.ai-message');
  const faqSection = chatMessages.querySelector('.faq-section');
  
  chatMessages.innerHTML = '';
  if (welcomeMessage) chatMessages.appendChild(welcomeMessage);
  if (faqSection) chatMessages.appendChild(faqSection);
  
  // Add conversation messages
  if (chatSessions[currentSessionId]) {
    chatSessions[currentSessionId].forEach(msg => {
      addMessageToChat(msg.sender, msg.content, false, msg.timestamp);
    });
  }
  
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Sidebar controls
function expandSidebar() {
  if (!sessionSidebar) return;
  sessionSidebar.classList.remove('collapsed');
  sessionSidebar.setAttribute('aria-hidden', 'false');
}

function collapseSidebar() {
  if (!sessionSidebar) return;
  sessionSidebar.classList.add('collapsed');
  sessionSidebar.setAttribute('aria-hidden', 'true');
}

openSessionsSidebar.addEventListener('click', () => {
  expandSidebar();
});

closeSidebar.addEventListener('click', () => {
  collapseSidebar();
});

// Delete a chat session via API and remove from UI
function deleteChatSession(sessionId, itemElement) {
  fetch('delete_chat_session.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ session_id: sessionId })
  })
  .then(resp => resp.json())
  .then(result => {
    if (result.status === 'deleted') {
      // remove element from DOM
      if (itemElement && itemElement.parentNode) itemElement.parentNode.removeChild(itemElement);

      // If deleted session is currently loaded, switch back to current
      if (currentSessionId === sessionId) {
        currentSessionId = 'current';
        chatSessions[currentSessionId] = chatSessions['current'] || [];
        renderChatMessages();
        updateSessionButtons();
      }
    } else {
      alert('Failed to delete conversation');
    }
  })
  .catch(err => {
    console.error('Delete error', err);
    alert('Error deleting conversation');
  });
}

// Enhanced function to add message to conversation with better context tracking
function addMessageToChat(sender, message, isTemp = false, timestamp = null) {
  const messageDiv = document.createElement('div');
  messageDiv.className = `chat-message ${sender}-message`;
  
  const time = timestamp ? new Date(timestamp) : new Date();
  const timeString = formatTime(time);
  
  // Clean ANSI escape codes from message
  const cleanMessage = message.replace(/\x1b\[[0-9;]*m/g, '');
  
  messageDiv.innerHTML = `
    <span class="message-sender">${sender === 'ai' ? 'AI Assistant' : 'You'}</span>
    <p class="message-content">${sanitize(cleanMessage)}</p>
    <div class="message-time">${timeString}</div>
  `;
  
  if (isTemp) {
    messageDiv.id = 'temp-message';
  }
  
  // Insert before FAQ section if it exists, otherwise append
  const faqSection = chatMessages.querySelector('.faq-section');
  if (faqSection) {
    chatMessages.insertBefore(messageDiv, faqSection);
  } else {
    chatMessages.appendChild(messageDiv);
  }
  
  chatMessages.scrollTop = chatMessages.scrollHeight;
  
  // Add to session history
  if (!isTemp) {
    if (!chatSessions[currentSessionId]) {
      chatSessions[currentSessionId] = [];
    }
    
    chatSessions[currentSessionId].push({
      sender: sender,
      content: cleanMessage,
      timestamp: time.toISOString()
    });
    
    // Auto-save after each message to prevent data loss
    if (chatSessions[currentSessionId].length % 3 === 0) { // Save every 3 messages
      saveCurrentSession();
    }
  }
  
  return messageDiv;
}

// Format time for messages
function formatTime(date) {
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Show typing indicator
function showTypingIndicator() {
  // Remove existing typing indicator
  hideTypingIndicator();
  
  // Dispatch event to show thinking GIF
  document.dispatchEvent(new CustomEvent('aiThinkingStarted'));
  
  const typingDiv = document.createElement('div');
  typingDiv.className = 'typing-indicator';
  typingDiv.id = 'typing-indicator';
  typingDiv.innerHTML = '<span></span><span></span><span></span>';
  
  // Insert before FAQ section if it exists, otherwise append
  const faqSection = chatMessages.querySelector('.faq-section');
  if (faqSection) {
    chatMessages.insertBefore(typingDiv, faqSection);
  } else {
    chatMessages.appendChild(typingDiv);
  }
  
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Hide typing indicator
function hideTypingIndicator() {
  const typingIndicator = document.getElementById('typing-indicator');
  if (typingIndicator) {
    typingIndicator.remove();
  }
  
  // Dispatch event to hide thinking GIF
  document.dispatchEvent(new CustomEvent('aiThinkingEnded'));
}

// Send message to AI with enhanced conversation context
function sendToAI() {
  const userMessage = userInput.value.trim();
  if (!userMessage) return;
  
  // Add user message to chat
  addMessageToChat('user', userMessage);
  userInput.value = '';
  
  // Show typing indicator
  showTypingIndicator();
  
  // Disable send button
  sendMessageBtn.disabled = true;
  
  // Build context-aware prompt with conversation flow awareness
  const contextualPrompt = buildContextForAI(userMessage);
  
  // Send to AI with conversation context
  fetch("process_ai.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "prompt=" + encodeURIComponent(contextualPrompt)
  })
  .then(response => response.text())
  .then(data => {
    hideTypingIndicator();
    const cleaned = data.replace(/llama_.*?\n/g, "").trim();
    addMessageToChat('ai', cleaned);
    saveCurrentSession(); // Save session after each exchange
    sendMessageBtn.disabled = false;
  })
  .catch(error => {
    hideTypingIndicator();
    addMessageToChat('ai', 'Sorry, I encountered an error. Please try again.');
    sendMessageBtn.disabled = false;
  });
}

// Send FAQ: show the selected question and the answer inside the FAQ tab
function sendFAQ(question, answer) {
  // Switch to FAQ tab
  switchTab('faq');

  // Add the selected question as a 'You' style message in the FAQ messages area
  addFaqMessage('user', question);

  // Add the AI answer as an AI message in the FAQ messages area
  addFaqMessage('ai', answer);
}

// Add a message to the FAQ messages area (separate from the chatMessages stream)
function addFaqMessage(sender, message) {
  const faqMessages = document.getElementById('faqMessages');
  if (!faqMessages) return;

  const messageDiv = document.createElement('div');
  messageDiv.className = `chat-message ${sender}-message`;

  const time = new Date();
  const timeString = formatTime(time);

  // Clean ANSI escape codes from message
  const cleanMessage = message.replace(/\x1b\[[0-9;]*m/g, '');

  messageDiv.innerHTML = `
    <span class="message-sender">${sender === 'ai' ? 'AI Assistant' : 'You'}</span>
    <p class="message-content">${sanitize(cleanMessage)}</p>
    <div class="message-time">${timeString}</div>
  `;

  faqMessages.appendChild(messageDiv);

  // Keep the FAQ messages area scrolled to bottom
  faqMessages.scrollTop = faqMessages.scrollHeight;

  // NOTE: do not add FAQ interactions to chatSessions so FAQ Q/A remain only in the FAQ tab
}

// Switch between tabs
function switchTab(tab) {
  currentTab = tab;
  
  // Update tab buttons
  tabButtons.forEach(btn => {
    btn.classList.remove('active');
  });
  document.querySelector(`.tab-button[data-tab="${tab}"]`).classList.add('active');
  
  // Show/hide appropriate containers
  if (tab === 'chat') {
    chatMessages.style.display = 'flex';
    faqContainer.style.display = 'none';
  } else {
    chatMessages.style.display = 'none';
    faqContainer.style.display = 'block';
  }
}

// Close chat modal
function closeChatModal() {
  // Save current session before closing
  saveCurrentSession();
  aiChatModal.classList.remove('active');
  
  // Remove thinking class from AI button when closing chat
  const aiButton = document.getElementById('aiToggleButton');
  if (aiButton) {
    aiButton.classList.remove('thinking');
  }
}

// Safe HTML escape
function sanitize(input) {
  const div = document.createElement("div");
  div.textContent = input;
  return div.innerHTML;
}

// AI server status UI removed — kept for backward compatibility in older versions

// Save current session to server with better deduplication
function saveCurrentSession() {
  // Only save if this is not the temporary 'current' session and has messages
  if (currentSessionId !== 'current' && 
      chatSessions[currentSessionId] && 
      chatSessions[currentSessionId].length > 0) {
    
    // Generate a more descriptive title based on conversation content
    const title = generateSessionTitle();
    
    fetch('save_chat_session.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        session_id: currentSessionId.replace('session_', ''), // Remove prefix for DB storage
        messages: chatSessions[currentSessionId],
        title: title
      })
    })
    .then(response => response.json())
    .then(result => {
      // Only refresh session list if a new session was created
      if (result.status === 'created') {
        loadConversationHistory(); // Refresh session list
      }
    })
    .catch(error => console.error('Error saving session:', error));
  }
}

// Generate a descriptive title for the session based on conversation content
function generateSessionTitle() {
  if (chatSessions[currentSessionId] && chatSessions[currentSessionId].length > 0) {
    // Look for the first user message that seems like a main topic
    const userMessages = chatSessions[currentSessionId].filter(msg => msg.sender === 'user');
    
    if (userMessages.length > 0) {
      // Take the first message and truncate it appropriately
      let firstMessage = userMessages[0].content;
      
      // Remove common question words for a cleaner title
      firstMessage = firstMessage.replace(/^(what|how|why|when|where|who|can you|please|find|show me|list)\s+/i, '');
      
      // Truncate to reasonable length
      return firstMessage.substring(0, 40) + (firstMessage.length > 40 ? '...' : '');
    }
  }
  return 'Chat Session';
}

// Analyze if current question is a follow-up
function isFollowUpQuestion(currentQuestion) {
  if (!chatSessions[currentSessionId] || chatSessions[currentSessionId].length === 0) return false;
  
  const followUpIndicators = [
    // English follow-up patterns
    /^(what about|how about|and|also|can you|what if|tell me more|continue|go on)/i,
    /(more|another|other|else|next|expand|further|additional)/i,
    /(that|this|it|they|them)\s/i,
    /^(show|list|find)\s+(more|other|another)/i,
    /(compare|versus|vs|difference)/i,
    
    // Filipino/Taglish follow-up patterns
    /^(paano|ano|at|pano|saka|tapos)/i,
    /(pa|din|rin|naman|lang)/i,
    /(yan|yun|iyan|iyon)/i
  ];
  
  return followUpIndicators.some(pattern => pattern.test(currentQuestion.trim()));
}

// Build context for AI with better conversation flow understanding
function buildContextForAI(currentQuestion) {
  if (!chatSessions[currentSessionId] || chatSessions[currentSessionId].length === 0) {
    return currentQuestion;
  }
  
  const isFollowUp = isFollowUpQuestion(currentQuestion);
  
  if (isFollowUp) {
    // Include recent conversation for follow-up questions
    const recentHistory = chatSessions[currentSessionId].slice(-8); // Last 4 exchanges (8 messages)
    let contextPrompt = "Previous conversation context:\n";
    
    recentHistory.forEach((message, index) => {
      const sender = message.sender === 'user' ? 'User' : 'AI';
      contextPrompt += `${sender}: ${message.content}\n`;
    });
    
    contextPrompt += `\nCurrent follow-up question: ${currentQuestion}`;
    return contextPrompt;
  } else {
    // For new questions, provide context summary
    const totalExchanges = Math.ceil(chatSessions[currentSessionId].length/2);
    return `${currentQuestion} (Context: We have discussed ${totalExchanges} topics in our conversation so far)`;
  }
}

// Event Listeners
closeChat.addEventListener('click', function() {
  // Remove thinking class from AI button when closing chat
  const aiButton = document.getElementById('aiToggleButton');
  if (aiButton) {
    aiButton.classList.remove('thinking');
  }
  closeChatModal();
});
newChatBtn.addEventListener('click', createNewChat);
tabButtons.forEach(btn => {
  btn.addEventListener('click', () => switchTab(btn.dataset.tab));
});

sendMessageBtn.addEventListener('click', sendToAI);

userInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendToAI();
  }
});

// Close modal when clicking outside
document.addEventListener('click', (e) => {
  if (aiChatModal.classList.contains('active') && 
      !aiChatModal.contains(e.target) && 
      !document.querySelector('.aibutton').contains(e.target)) {
    // Remove thinking class from AI button when closing chat
    const aiButton = document.getElementById('aiToggleButton');
    if (aiButton) {
      aiButton.classList.remove('thinking');
    }
    closeChatModal();
  }
});
</script>