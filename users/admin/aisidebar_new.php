<style>
/* Messenger-style AI Chat UI */
#aiChatModal {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 380px;
  height: 500px;
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
  max-width: 80%;
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
  bottom: 30px;
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

/* Responsive design */
@media (max-width: 768px) {
  #aiChatModal {
    width: calc(100% - 40px);
    height: 70vh;
  }
}
</style>

<!-- Floating AI Button -->
<button class="aibutton" id="aiToggleButton">
  <img src='../images/cutie.png' alt="AI">
</button>

<!-- AI Chat Modal -->
<div id="aiChatModal">
  <div class="ai-chat-header">
    <div class="header-info">
      <div class="avatar">AI</div>
      <h3>Qutie AI Assistant</h3>
    </div>
    <div class="controls">
      <button id="minimizeChat" title="Minimize">−</button>
      <button id="closeChat" title="Close">×</button>
    </div>
  </div>
  
  <div class="ai-status">
    <span class="status-indicator red" id="aiStatusIndicator"></span>
    <span id="aiStatusText">AI Server Not Running</span>
  </div>
  
  <div id="chatMessages">
    <div class="chat-message ai-message">
      <span class="message-sender">AI Assistant</span>
      <p class="message-content">Hello! I'm your HRIMS AI assistant. How can I help you today?</p>
      <div class="message-time">Just now</div>
    </div>
  </div>
  
  <div class="chat-input-container">
    <textarea id="userInput" placeholder="Type your message..."></textarea>
    <button id="sendMessageBtn">➤</button>
  </div>
</div>

<script>
// Conversation Memory Management
let conversationHistory = [];
let maxHistoryLength = 10;

// DOM Elements
const aiToggleButton = document.getElementById('aiToggleButton');
const aiChatModal = document.getElementById('aiChatModal');
const closeChat = document.getElementById('closeChat');
const minimizeChat = document.getElementById('minimizeChat');
const chatMessages = document.getElementById('chatMessages');
const userInput = document.getElementById('userInput');
const sendMessageBtn = document.getElementById('sendMessageBtn');
const aiStatusIndicator = document.getElementById('aiStatusIndicator');
const aiStatusText = document.getElementById('aiStatusText');

// Load conversation history from localStorage
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
  
  if (conversationHistory.length > maxHistoryLength) {
    conversationHistory = conversationHistory.slice(-maxHistoryLength);
  }
  
  saveConversationToStorage();
}

// Format time for messages
function formatTime(date) {
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Add message to chat display
function addMessageToChat(sender, message, isTemp = false) {
  const messageDiv = document.createElement('div');
  messageDiv.className = `chat-message ${sender}-message`;
  
  const now = new Date();
  const timeString = formatTime(now);
  
  messageDiv.innerHTML = `
    <span class="message-sender">${sender === 'ai' ? 'AI Assistant' : 'You'}</span>
    <p class="message-content">${sanitize(message)}</p>
    <div class="message-time">${timeString}</div>
  `;
  
  if (isTemp) {
    messageDiv.id = 'temp-message';
  }
  
  chatMessages.appendChild(messageDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;
  return messageDiv;
}

// Show typing indicator
function showTypingIndicator() {
  const typingDiv = document.createElement('div');
  typingDiv.className = 'typing-indicator';
  typingDiv.id = 'typing-indicator';
  typingDiv.innerHTML = '<span></span><span></span><span></span>';
  chatMessages.appendChild(typingDiv);
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
  
  // In a real implementation, this would call your AI endpoint
  // For now, we'll simulate a response
  setTimeout(() => {
    hideTypingIndicator();
    const aiResponse = "I'm your HRIMS AI assistant. I can help you with employee records, applicant management, and other HR tasks. How can I assist you today?";
    addMessageToChat('ai', aiResponse);
    addToHistory(userMessage, aiResponse);
    sendMessageBtn.disabled = false;
  }, 1500);
}

// Toggle chat modal
function toggleChatModal() {
  if (aiChatModal.classList.contains('active')) {
    aiChatModal.classList.remove('active');
  } else {
    aiChatModal.classList.add('active');
    userInput.focus();
  }
}

// Close chat modal
function closeChatModal() {
  aiChatModal.classList.remove('active');
}

// Minimize chat modal
function minimizeChatModal() {
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

// Event Listeners
aiToggleButton.addEventListener('click', toggleChatModal);
closeChat.addEventListener('click', closeChatModal);
minimizeChat.addEventListener('click', minimizeChatModal);

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
      !aiToggleButton.contains(e.target)) {
    closeChatModal();
  }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  loadConversationFromStorage();
  updateAIStatus('not_running'); // Default status
});
</script>