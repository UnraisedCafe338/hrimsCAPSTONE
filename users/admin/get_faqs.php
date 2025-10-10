<?php
// get_faqs.php - Retrieve FAQs from MongoDB
header('Content-Type: application/json');

try {
    // Include MongoDB connection
    require_once '../../handlers/connection.php';
    
    // Select FAQs collection
    $faqsCollection = $database->selectCollection("faqs");
    
    // Retrieve all FAQs
    $faqs = $faqsCollection->find([], ['sort' => ['_id' => 1]]);
    
    $faqList = [];
    foreach ($faqs as $faq) {
        $faqList[] = [
            'id' => (string)$faq['_id'],
            'question' => $faq['question'],
            'answer' => $faq['answer']
        ];
    }
    
    echo json_encode($faqList);
} catch (Exception $e) {
    // Return default FAQs if there's an error
    $defaultFAQs = [
        [
            'id' => '1',
            'question' => 'How do I search for employees?',
            'answer' => 'You can search for employees by name, position, department, or skills. Try asking questions like "Find employees with programming skills" or "List all nursing graduates".'
        ],
        [
            'id' => '2',
            'question' => 'How do I find applicant information?',
            'answer' => 'To find applicant information, ask specific questions about their qualifications, education, or skills. For example: "Show me applicants with a BSIS degree" or "Find applicants with Java skills".'
        ],
        [
            'id' => '3',
            'question' => 'What kind of data can I query?',
            'answer' => 'You can query employee and applicant data including: personal information, education history, work experience, skills, position applied for, department, and more. Try asking specific questions about what you\'re looking for!'
        ]
    ];
    
    echo json_encode($defaultFAQs);
}
?>