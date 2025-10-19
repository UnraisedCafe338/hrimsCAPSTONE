// init_mongodb_collections.js - MongoDB script to initialize collections
// This is a MongoDB shell script, not a Node.js script
// Run this in MongoDB shell: mongo hrims_db init_mongodb_collections.js

// Use the hrims_db database
// Note: In MongoDB shell, 'use' command switches database context
// use hrims_db

// For compatibility with linters, we'll use the db variable directly
// assuming we're already in the correct database context

// Create FAQs collection and insert sample data
print("Initializing FAQs collection...");

db.faqs.insertMany([
  {
    question: "How do I search for employees by skills?",
    answer:
      'You can search for employees by specific skills. Try asking: "Find employees with programming skills" or "Show me employees who know Python". The AI will search through employee profiles and return relevant results.',
  },
  {
    question: "How do I find graduates of a specific course?",
    answer:
      'To find graduates of a specific course, ask questions like: "Find IS graduates" or "List all nursing graduates". The system will search both employee and applicant databases for matching educational backgrounds.',
  },
  {
    question: "Can I compare different courses or departments?",
    answer:
      'Yes! You can ask comparative questions like: "How much percentage of IS compared to nursing?" or "What course has more graduates?". The AI will analyze the data and provide comparative statistics.',
  },
  {
    question: "How do I get detailed information about a specific person?",
    answer:
      'To get detailed information about a person, ask: "What is the role of Maria Garcia?" or "What are the skills of Jonathan Santos?". The AI will provide a comprehensive profile of the requested individual.',
  },
  {
    question: "What happens if I ask a follow-up question?",
    answer:
      'The AI remembers the context of your conversation. If you ask a follow-up question like "What about their skills?" after discussing a person, the AI will understand you\'re referring to the previously mentioned person and provide relevant information.',
  },
  {
    question: "How do I start a new conversation?",
    answer:
      'You can start a new conversation by clicking the "+ New Chat" button in the chat interface. This will save your current conversation and start a fresh one.',
  },
  {
    question: "Can I access my previous conversations?",
    answer:
      "Yes, your previous conversations are saved automatically. You can access them through the session buttons that appear at the top of the chat interface.",
  },
]);

print(
  "FAQs collection initialized with " +
    db.faqs.countDocuments() +
    " sample FAQs."
);

// Create indexes for better performance
db.faqs.createIndex({ question: 1 });
print("Created index on FAQs question field.");

// Create chat sessions collection
db.createCollection("ai_chat_sessions");
db.ai_chat_sessions.createIndex({ created_at: -1 });
print("Created ai_chat_sessions collection with index.");

// Create teaching demos collection
db.createCollection("teaching_demos");
db.teaching_demos.createIndex({ applicant_id: 1 });
db.teaching_demos.createIndex({ demo_date: 1 });
print("Created teaching_demos collection with indexes.");

// Create notifications collection
db.createCollection("notifications");
db.notifications.createIndex({ recipient_role: 1 });
db.notifications.createIndex({ is_read: 1 });
db.notifications.createIndex({ created_at: -1 });
print("Created notifications collection with indexes.");

print("Database initialization complete!");
