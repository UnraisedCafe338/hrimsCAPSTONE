<style>
/* Messenger-style AI Chat UI */
#aiChatModal {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 450px; /* Increased width */
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
  background: #fff;
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
  bottom: 20px;
  right: 20px;
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
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
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
      <div class="avatar">AI</div>
      <h3>Qutie AI Assistant</h3>
    </div>
    <div class="controls">
      <button id="closeChat" title="Close">×</button>
    </div>
  </div>
  
  <div class="ai-status">
    <span class="status-indicator red" id="aiStatusIndicator"></span>
    <span id="aiStatusText">AI Server Not Running</span>
  </div>
  
  <!-- Chat Tabs -->
  <div class="chat-tabs">
    <button class="tab-button active" data-tab="chat">AI Chat</button>
    <button class="tab-button" data-tab="faq">FAQs</button>
  </div>
  
  <!-- Chat Sessions -->
  <div class="chat-sessions">
    <button class="session-button active" data-session="current">Current Chat</button>
    <button class="new-chat-btn" id="newChatBtn">+ New Chat</button>
    <div id="savedSessions"></div>
  </div>
  
  <div id="chatMessages">
    <!-- Welcome message with FAQs -->
    <div class="chat-message ai-message">
      <span class="message-sender">AI Assistant</span>
      <p class="message-content">Hello! I'm your HRIMS AI assistant. How can I help you today?</p>
      <div class="message-time">Just now</div>
    </div>
    
    <!-- FAQ Section -->
    <div class="faq-section">
      <h4>Popular HR Queries:</h4>
      <div class="faq-buttons" id="faqButtons">
        <!-- FAQ buttons will be loaded here dynamically -->
      </div>
    </div>
  </div>
  
  <div id="faqContainer" style="display: none; padding: 15px; overflow-y: auto; flex: 1;">
    <h4 style="margin-top: 0; color: #333;">Frequently Asked Questions:</h4>
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
const aiStatusIndicator = document.getElementById('aiStatusIndicator');
const aiStatusText = document.getElementById('aiStatusText');
const faqButtons = document.getElementById('faqButtons');
const faqButtonsTab = document.getElementById('faqButtonsTab');
const newChatBtn = document.getElementById('newChatBtn');
const savedSessions = document.getElementById('savedSessions');
const tabButtons = document.querySelectorAll('.tab-button');

// Load FAQs and conversation history
document.addEventListener('DOMContentLoaded', function() {
  loadFAQs();
  loadConversationHistory();
  updateAIStatus('not_running');
  
  // Expose functions globally for the toggle button
  window.openAIChat = function() {
    aiChatModal.classList.add('active');
    userInput.focus();
  };
  
  window.closeAIChat = closeChatModal;
});

// Load FAQs from server
function loadFAQs() {
  fetch('get_faqs.php')
    .then(response => response.json())
    .then(faqs => {
      // Populate FAQ buttons in chat
      faqButtons.innerHTML = '';
      // Populate FAQ buttons in FAQ tab
      faqButtonsTab.innerHTML = '';
      
      faqs.forEach(faq => {
        // Create button for chat FAQ section
        const button1 = document.createElement('button');
        button1.className = 'faq-button';
        button1.textContent = faq.question;
        button1.onclick = () => sendFAQ(faq.answer);
        faqButtons.appendChild(button1);
        
        // Create button for FAQ tab
        const button2 = document.createElement('button');
        button2.className = 'faq-button';
        button2.textContent = faq.question;
        button2.onclick = () => sendFAQ(faq.answer);
        faqButtonsTab.appendChild(button2);
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
      
      // Populate FAQ buttons in chat
      faqButtons.innerHTML = '';
      // Populate FAQ buttons in FAQ tab
      faqButtonsTab.innerHTML = '';
      
      defaultFAQs.forEach(faq => {
        // Create button for chat FAQ section
        const button1 = document.createElement('button');
        button1.className = 'faq-button';
        button1.textContent = faq.question;
        button1.onclick = () => sendFAQ(faq.answer);
        faqButtons.appendChild(button1);
        
        // Create button for FAQ tab
        const button2 = document.createElement('button');
        button2.className = 'faq-button';
        button2.textContent = faq.question;
        button2.onclick = () => sendFAQ(faq.answer);
        faqButtonsTab.appendChild(button2);
      });
    });
}

// Load conversation history from server
function loadConversationHistory() {
  fetch('get_chat_sessions.php')
    .then(response => response.json())
    .then(sessions => {
      // Clear existing saved sessions to prevent duplication
      savedSessions.innerHTML = '';
      
      // Keep track of added session IDs to prevent duplication
      const addedSessionIds = new Set();
      
      sessions.forEach(session => {
        // Check if session ID already exists
        if (!addedSessionIds.has(session._id)) {
          addedSessionIds.add(session._id);
          
          const button = document.createElement('button');
          button.className = 'session-button';
          button.textContent = session.title || `Chat ${session._id.substring(0, 8)}`;
          button.dataset.session = session._id;
          button.onclick = () => loadChatSession(session._id);
          savedSessions.appendChild(button);
        }
      });
    })
    .catch(error => console.error('Error loading chat sessions:', error));
}

// Load a specific chat session
function loadChatSession(sessionId) {
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

// Create a new chat session
function createNewChat() {
  // Save current session if it has messages
  if (chatSessions[currentSessionId] && chatSessions[currentSessionId].length > 0) {
    saveCurrentSession();
  }
  
  // Create new session
  const newSessionId = 'session_' + Date.now();
  currentSessionId = newSessionId;
  chatSessions[currentSessionId] = [];
  
  // Clear chat messages except welcome message and FAQ section
  const welcomeMessage = chatMessages.querySelector('.ai-message');
  const faqSection = chatMessages.querySelector('.faq-section');
  
  chatMessages.innerHTML = '';
  if (welcomeMessage) chatMessages.appendChild(welcomeMessage);
  if (faqSection) chatMessages.appendChild(faqSection);
  
  updateSessionButtons();
}

// Save current session to server
function saveCurrentSession() {
  if (chatSessions[currentSessionId] && chatSessions[currentSessionId].length > 0) {
    // Only save if this is not the temporary 'current' session
    if (currentSessionId !== 'current') {
      fetch('save_chat_session.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          session_id: currentSessionId,
          messages: chatSessions[currentSessionId],
          title: getSessionTitle()
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
}

// Get a title for the current session based on first message
function getSessionTitle() {
  if (chatSessions[currentSessionId] && chatSessions[currentSessionId].length > 0) {
    const firstUserMessage = chatSessions[currentSessionId].find(msg => msg.sender === 'user');
    if (firstUserMessage) {
      return firstUserMessage.content.substring(0, 30) + (firstUserMessage.content.length > 30 ? '...' : '');
    }
  }
  return 'New Chat';
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

// Add message to conversation
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
}

// Send message to AI
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
  
  // Build context-aware prompt
  const contextualPrompt = buildContextForAI(userMessage);
  
  // In a real implementation, this would call your AI endpoint
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

// Send FAQ response directly
function sendFAQ(answer) {
  addMessageToChat('ai', answer);
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
}

// Safe HTML escape
function sanitize(input) {
  const div = document.createElement("div");
  div.textContent = input;
  return div.innerHTML;
}

// Update AI status
function updateAIStatus(status) {
  switch(status) {
    case 'running':
      aiStatusIndicator.className = 'status-indicator green';
      aiStatusText.textContent = 'AI Server Running';
      break;
    case 'starting':
      aiStatusIndicator.className = 'status-indicator orange';
      aiStatusText.textContent = 'AI Server Starting...';
      break;
    case 'stopping':
      aiStatusIndicator.className = 'status-indicator orange';
      aiStatusText.textContent = 'AI Server Stopping...';
      break;
    default:
      aiStatusIndicator.className = 'status-indicator red';
      aiStatusText.textContent = 'AI Server Not Running';
  }
}

// Analyze if current question is a follow-up
function isFollowUpQuestion(currentQuestion) {
  if (!chatSessions[currentSessionId] || chatSessions[currentSessionId].length === 0) return false;
  
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
  if (!chatSessions[currentSessionId] || chatSessions[currentSessionId].length === 0) {
    return currentQuestion;
  }
  
  const isFollowUp = isFollowUpQuestion(currentQuestion);
  
  if (isFollowUp) {
    // Include recent conversation for follow-up questions
    const recentHistory = chatSessions[currentSessionId].slice(-6); // Last 3 exchanges (6 messages)
    let contextPrompt = "Previous conversation context:\n";
    
    recentHistory.forEach((message, index) => {
      const sender = message.sender === 'user' ? 'User' : 'AI';
      contextPrompt += `${sender}: ${message.content}\n`;
    });
    
    contextPrompt += `\nCurrent follow-up question: ${currentQuestion}`;
    return contextPrompt;
  } else {
    // For new questions, just mention we have context available
    return `${currentQuestion} (Note: I have ${Math.ceil(chatSessions[currentSessionId].length/2)} previous exchanges in context if relevant)`;
  }
}

// Event Listeners
closeChat.addEventListener('click', closeChatModal);
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
    closeChatModal();
  }
});
</script>