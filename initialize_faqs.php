<?php
// initialize_faqs.php - Initialize FAQs collection with sample data
// This script can be run from the command line or web browser

// Function to create FAQs collection manually if MongoDB is not available
function createFAQsManually() {
    $sampleFAQs = [
        [
            'question' => 'How do I search for employees by skills?',
            'answer' => 'You can search for employees by specific skills. Try asking: "Find employees with programming skills" or "Show me employees who know Python". The AI will search through employee profiles and return relevant results.'
        ],
        [
            'question' => 'How do I find graduates of a specific course?',
            'answer' => 'To find graduates of a specific course, ask questions like: "Find IS graduates" or "List all nursing graduates". The system will search both employee and applicant databases for matching educational backgrounds.'
        ],
        [
            'question' => 'Can I compare different courses or departments?',
            'answer' => 'Yes! You can ask comparative questions like: "How much percentage of IS compared to nursing?" or "What course has more graduates?". The AI will analyze the data and provide comparative statistics.'
        ],
        [
            'question' => 'How do I get detailed information about a specific person?',
            'answer' => 'To get detailed information about a person, ask: "What is the role of Maria Garcia?" or "What are the skills of Jonathan Santos?". The AI will provide a comprehensive profile of the requested individual.'
        ],
        [
            'question' => 'What happens if I ask a follow-up question?',
            'answer' => 'The AI remembers the context of your conversation. If you ask a follow-up question like "What about their skills?" after discussing a person, the AI will understand you\'re referring to the previously mentioned person and provide relevant information.'
        ],
        [
            'question' => 'How do I start a new conversation?',
            'answer' => 'You can start a new conversation by clicking the "+ New Chat" button in the chat interface. This will save your current conversation and start a fresh one.'
        ],
        [
            'question' => 'Can I access my previous conversations?',
            'answer' => 'Yes, your previous conversations are saved automatically. You can access them through the session buttons that appear at the top of the chat interface.'
        ]
    ];
    
    // Display the FAQs in a format that can be manually inserted into MongoDB
    echo "Sample FAQs to insert into MongoDB 'faqs' collection:\n\n";
    foreach ($sampleFAQs as $index => $faq) {
        echo ($index + 1) . ". Question: " . $faq['question'] . "\n";
        echo "   Answer: " . $faq['answer'] . "\n\n";
    }
    
    echo "To manually insert these into MongoDB, use the following commands:\n";
    echo "use hrims_db\n";
    echo "db.faqs.insertMany([\n";
    
    foreach ($sampleFAQs as $index => $faq) {
        echo "  {\n";
        echo "    \"question\": \"" . addslashes($faq['question']) . "\",\n";
        echo "    \"answer\": \"" . addslashes($faq['answer']) . "\"\n";
        echo "  }" . ($index < count($sampleFAQs) - 1 ? "," : "") . "\n";
    }
    
    echo "])\n";
}

// Try to connect to MongoDB if possible
try {
    // Check if we're running from command line
    if (php_sapi_name() === 'cli') {
        echo "Running from command line...\n";
        // Try to include MongoDB connection
        if (file_exists('handlers/connection.php')) {
            require_once 'handlers/connection.php';
            
            // Check if MongoDB classes exist
            if (class_exists('MongoDB\Client')) {
                // Select FAQs collection
                $faqsCollection = $database->selectCollection("faqs");
                
                // Check if FAQs already exist
                $count = $faqsCollection->countDocuments();
                
                if ($count == 0) {
                    // Sample FAQs data
                    $sampleFAQs = [
                        [
                            'question' => 'How do I search for employees by skills?',
                            'answer' => 'You can search for employees by specific skills. Try asking: "Find employees with programming skills" or "Show me employees who know Python". The AI will search through employee profiles and return relevant results.'
                        ],
                        [
                            'question' => 'How do I find graduates of a specific course?',
                            'answer' => 'To find graduates of a specific course, ask questions like: "Find IS graduates" or "List all nursing graduates". The system will search both employee and applicant databases for matching educational backgrounds.'
                        ],
                        [
                            'question' => 'Can I compare different courses or departments?',
                            'answer' => 'Yes! You can ask comparative questions like: "How much percentage of IS compared to nursing?" or "What course has more graduates?". The AI will analyze the data and provide comparative statistics.'
                        ],
                        [
                            'question' => 'How do I get detailed information about a specific person?',
                            'answer' => 'To get detailed information about a person, ask: "What is the role of Maria Garcia?" or "What are the skills of Jonathan Santos?". The AI will provide a comprehensive profile of the requested individual.'
                        ],
                        [
                            'question' => 'What happens if I ask a follow-up question?',
                            'answer' => 'The AI remembers the context of your conversation. If you ask a follow-up question like "What about their skills?" after discussing a person, the AI will understand you\'re referring to the previously mentioned person and provide relevant information.'
                        ],
                        [
                            'question' => 'How do I start a new conversation?',
                            'answer' => 'You can start a new conversation by clicking the "+ New Chat" button in the chat interface. This will save your current conversation and start a fresh one.'
                        ],
                        [
                            'question' => 'Can I access my previous conversations?',
                            'answer' => 'Yes, your previous conversations are saved automatically. You can access them through the session buttons that appear at the top of the chat interface.'
                        ]
                    ];
                    
                    // Insert sample FAQs
                    foreach ($sampleFAQs as $faq) {
                        $faqsCollection->insertOne($faq);
                    }
                    
                    echo "FAQs collection initialized with " . count($sampleFAQs) . " sample FAQs.\n";
                } else {
                    echo "FAQs collection already contains data ($count FAQs).\n";
                }
            } else {
                echo "MongoDB extension not available. Here are the sample FAQs:\n\n";
                createFAQsManually();
            }
        } else {
            echo "MongoDB connection file not found. Here are the sample FAQs:\n\n";
            createFAQsManually();
        }
    } else {
        // Running from web browser
        echo "<h2>FAQ Initialization Instructions</h2>";
        echo "<p>Here are the sample FAQs that can be added to your MongoDB database:</p>";
        echo "<pre>";
        createFAQsManually();
        echo "</pre>";
        echo "<p>To manually insert these into MongoDB, use the MongoDB shell commands shown above.</p>";
    }
} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "Error initializing FAQs: " . $e->getMessage() . "\n";
        echo "Displaying manual instructions instead:\n\n";
        createFAQsManually();
    } else {
        echo "<h2>Error</h2>";
        echo "<p>Error initializing FAQs: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<h3>Manual Instructions</h3>";
        echo "<pre>";
        createFAQsManually();
        echo "</pre>";
    }
}
?>