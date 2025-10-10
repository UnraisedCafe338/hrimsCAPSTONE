# MongoDB Database Initialization Instructions

This document provides instructions for initializing the MongoDB collections needed for the enhanced AI chat features.

## Prerequisites

1. MongoDB server running
2. Access to MongoDB shell

## Collections to Create

### 1. FAQs Collection (`faqs`)

This collection stores frequently asked questions and their answers.

To create and populate the FAQs collection, run the following commands in the MongoDB shell:

```javascript
// Switch to the hrims_db database
use hrims_db

// Create and populate the FAQs collection
db.faqs.insertMany([
  {
    "question": "How do I search for employees by skills?",
    "answer": "You can search for employees by specific skills. Try asking: \"Find employees with programming skills\" or \"Show me employees who know Python\". The AI will search through employee profiles and return relevant results."
  },
  {
    "question": "How do I find graduates of a specific course?",
    "answer": "To find graduates of a specific course, ask questions like: \"Find IS graduates\" or \"List all nursing graduates\". The system will search both employee and applicant databases for matching educational backgrounds."
  },
  {
    "question": "Can I compare different courses or departments?",
    "answer": "Yes! You can ask comparative questions like: \"How much percentage of IS compared to nursing?\" or \"What course has more graduates?\". The AI will analyze the data and provide comparative statistics."
  },
  {
    "question": "How do I get detailed information about a specific person?",
    "answer": "To get detailed information about a person, ask: \"What is the role of Maria Garcia?\" or \"What are the skills of Jonathan Santos?\". The AI will provide a comprehensive profile of the requested individual."
  },
  {
    "question": "What happens if I ask a follow-up question?",
    "answer": "The AI remembers the context of your conversation. If you ask a follow-up question like \"What about their skills?\" after discussing a person, the AI will understand you're referring to the previously mentioned person and provide relevant information."
  },
  {
    "question": "How do I start a new conversation?",
    "answer": "You can start a new conversation by clicking the \"+ New Chat\" button in the chat interface. This will save your current conversation and start a fresh one."
  },
  {
    "question": "Can I access my previous conversations?",
    "answer": "Yes, your previous conversations are saved automatically. You can access them through the session buttons that appear at the top of the chat interface."
  }
])

// Create index for better performance
db.faqs.createIndex({ "question": 1 })
```

### 2. Chat Sessions Collection (`ai_chat_sessions`)

This collection stores conversation history.

To create the chat sessions collection, run the following commands in the MongoDB shell:

```javascript
// Switch to the hrims_db database (if not already)
use hrims_db

// Create the chat sessions collection
db.createCollection("ai_chat_sessions")

// Create index for better performance
db.ai_chat_sessions.createIndex({ "created_at": -1 })
```

## Verification

After running the commands, you can verify the collections were created successfully:

```javascript
// Check FAQs collection
db.faqs.countDocuments();

// Check chat sessions collection
db.ai_chat_sessions.countDocuments();

// View sample FAQ
db.faqs.findOne();
```

## Notes

1. The collections will be created automatically when the application tries to access them, but initializing them with sample data provides a better user experience.
2. You can modify the FAQ questions and answers at any time through the MongoDB shell or a database management tool.
3. The chat sessions are automatically saved as conversations occur in the application.
