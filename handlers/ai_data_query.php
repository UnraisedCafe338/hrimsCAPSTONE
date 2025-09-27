<?php
header('Content-Type: application/json');
include('../connection.php');

// Get the query parameters
$query_type = $_GET['type'] ?? '';
$search_term = $_GET['search'] ?? '';
$collection_name = $_GET['collection'] ?? 'employee'; // default to employee
$action = $_GET['action'] ?? 'search'; // New parameter for different actions
$context = $_GET['context'] ?? ''; // Context from previous queries

try {
    $collection = $database->selectCollection($collection_name);
    $results = [];
    
    // Handle different types of queries
    switch ($action) {
        case 'percentage':
            // Handle percentage calculations between courses
            $response = calculateCoursePercentages($collection, $search_term);
            echo json_encode($response);
            exit;
            
        case 'person_details':
            // Handle detailed person information queries
            $response = getPersonDetails($collection, $search_term, $collection_name);
            echo json_encode($response);
            exit;
            
        case 'course_comparison':
            // Handle course comparison queries
            $response = compareCourses($collection, $search_term);
            echo json_encode($response);
            exit;
            
        case 'search':
        default:
            // Regular search functionality (existing code)
            break;
    }
    
    switch ($query_type) {
        case 'skills':
            // Search by skills
            $cursor = $collection->find([
                'skills' => ['$regex' => $search_term, '$options' => 'i']
            ]);
            break;
            
        case 'education':
            // Search by education/course/degree
            $cursor = $collection->find([
                '$or' => [
                    ['education.college.degree' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education.college.school' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education.high_school.school' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education.vocational.degree' => ['$regex' => $search_term, '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => $search_term, '$options' => 'i']],
                    ['educational_background.degree' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education' => ['$regex' => $search_term, '$options' => 'i']]
                ]
            ]);
            break;
            
        case 'position':
            // Search by position
            $cursor = $collection->find([
                'position_applied' => ['$regex' => $search_term, '$options' => 'i']
            ]);
            break;
            
        case 'department':
            // Search by department
            $cursor = $collection->find([
                'department' => ['$regex' => $search_term, '$options' => 'i']
            ]);
            break;
            
        case 'general':
        default:
            // General search across multiple fields
            $cursor = $collection->find([
                '$or' => [
                    ['personal_info.first_name' => ['$regex' => $search_term, '$options' => 'i']],
                    ['personal_info.last_name' => ['$regex' => $search_term, '$options' => 'i']],
                    ['position_applied' => ['$regex' => $search_term, '$options' => 'i']],
                    ['department' => ['$regex' => $search_term, '$options' => 'i']],
                    ['skills' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education.college.degree' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education.college.school' => ['$regex' => $search_term, '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => $search_term, '$options' => 'i']],
                    ['educational_background.degree' => ['$regex' => $search_term, '$options' => 'i']],
                    ['education' => ['$regex' => $search_term, '$options' => 'i']]
                ]
            ]);
            break;
    }
    
    // Format results for AI
    foreach ($cursor as $doc) {
        $result = [
            'name' => trim(($doc['personal_info']['first_name'] ?? '') . ' ' . 
                          ($doc['personal_info']['middle_name'] ?? '') . ' ' . 
                          ($doc['personal_info']['last_name'] ?? '')),
            'email' => $doc['personal_info']['email'] ?? '',
            'position' => $doc['position_applied'] ?? '',
            'department' => $doc['department'] ?? '',
            'skills' => $doc['skills'] ?? '',
            'college_degree' => $doc['education']['college']['degree'] ?? '',
            'college_school' => $doc['education']['college']['school'] ?? '',
            'education' => $doc['educational_background']['course'] ?? $doc['education'] ?? '',
            'degree' => $doc['educational_background']['degree'] ?? '',
            'collection' => $collection_name
        ];
        $results[] = $result;
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($results),
        'data' => $results,
        'collection_searched' => $collection_name
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}

// Function to calculate percentage distribution between courses
function calculateCoursePercentages($collection, $search_term) {
    try {
        // Parse courses from search term (e.g., "IS compared to nursing")
        $courses = [];
        $search_lower = strtolower($search_term);
        
        if (strpos($search_lower, 'is') !== false || strpos($search_lower, 'information system') !== false) {
            $courses[] = 'IS';
        }
        if (strpos($search_lower, 'nursing') !== false) {
            $courses[] = 'Nursing';
        }
        if (strpos($search_lower, 'maritime') !== false) {
            $courses[] = 'Maritime';
        }
        if (strpos($search_lower, 'business') !== false) {
            $courses[] = 'Business';
        }
        if (strpos($search_lower, 'criminology') !== false) {
            $courses[] = 'Criminology';
        }
        
        $course_counts = [];
        $total_count = 0;
        
        foreach ($courses as $course) {
            $count = getCourseCount($collection, $course);
            $course_counts[$course] = $count;
            $total_count += $count;
        }
        
        // Calculate percentages
        $percentages = [];
        foreach ($course_counts as $course => $count) {
            $percentage = $total_count > 0 ? round(($count / $total_count) * 100, 1) : 0;
            $percentages[$course] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        
        return [
            'success' => true,
            'action' => 'percentage',
            'total_count' => $total_count,
            'courses' => $percentages,
            'message' => generatePercentageMessage($percentages)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error calculating percentages: ' . $e->getMessage()
        ];
    }
}

// Function to get detailed information about a specific person
function getPersonDetails($collection, $person_name, $collection_name) {
    try {
        // Search for the specific person
        $name_parts = explode(' ', trim($person_name));
        $first_name = $name_parts[0] ?? '';
        $last_name = $name_parts[1] ?? $name_parts[0];
        
        $cursor = $collection->find([
            '$and' => [
                ['personal_info.first_name' => ['$regex' => $first_name, '$options' => 'i']],
                ['personal_info.last_name' => ['$regex' => $last_name, '$options' => 'i']]
            ]
        ]);
        
        $person_data = null;
        foreach ($cursor as $doc) {
            $person_data = [
                'name' => trim(($doc['personal_info']['first_name'] ?? '') . ' ' . 
                             ($doc['personal_info']['middle_name'] ?? '') . ' ' . 
                             ($doc['personal_info']['last_name'] ?? '')),
                'email' => $doc['personal_info']['email'] ?? 'Not provided',
                'position' => $doc['position_applied'] ?? 'Not specified',
                'department' => $doc['department'] ?? 'Not assigned',
                'skills' => $doc['skills'] ?? 'No skills listed',
                'college_degree' => $doc['education']['college']['degree'] ?? 'Not provided',
                'college_school' => $doc['education']['college']['school'] ?? 'Not provided',
                'phone' => $doc['personal_info']['phone'] ?? 'Not provided',
                'address' => $doc['personal_info']['address'] ?? 'Not provided',
                'collection' => $collection_name
            ];
            break; // Take the first match
        }
        
        if ($person_data) {
            return [
                'success' => true,
                'action' => 'person_details',
                'data' => $person_data,
                'message' => generatePersonDetailsMessage($person_data)
            ];
        } else {
            return [
                'success' => false,
                'message' => "No information found for {$person_name} in our database."
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error getting person details: ' . $e->getMessage()
        ];
    }
}

// Function to compare course counts
function compareCourses($collection, $search_term) {
    try {
        $courses = ['IS', 'Nursing', 'Maritime', 'Business', 'Criminology'];
        $course_counts = [];
        
        foreach ($courses as $course) {
            $count = getCourseCount($collection, $course);
            if ($count > 0) {
                $course_counts[$course] = $count;
            }
        }
        
        // Sort by count descending
        arsort($course_counts);
        
        return [
            'success' => true,
            'action' => 'course_comparison',
            'courses' => $course_counts,
            'message' => generateComparisonMessage($course_counts)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error comparing courses: ' . $e->getMessage()
        ];
    }
}

// Helper function to get count for a specific course
function getCourseCount($collection, $course) {
    $query = [];
    
    switch (strtolower($course)) {
        case 'is':
            $query = [
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'information system', '$options' => 'i']],
                    ['education.college.degree' => ['$regex' => 'bsis', '$options' => 'i']],
                    ['education.college.degree' => ['$regex' => 'computer', '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => 'information system', '$options' => 'i']]
                ]
            ];
            break;
        case 'nursing':
            $query = [
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'nursing', '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => 'nursing', '$options' => 'i']]
                ]
            ];
            break;
        case 'maritime':
            $query = [
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'maritime', '$options' => 'i']],
                    ['education.college.degree' => ['$regex' => 'marine', '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => 'maritime', '$options' => 'i']]
                ]
            ];
            break;
        case 'business':
            $query = [
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'business', '$options' => 'i']],
                    ['education.college.degree' => ['$regex' => 'management', '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => 'business', '$options' => 'i']]
                ]
            ];
            break;
        case 'criminology':
            $query = [
                '$or' => [
                    ['education.college.degree' => ['$regex' => 'criminology', '$options' => 'i']],
                    ['educational_background.course' => ['$regex' => 'criminology', '$options' => 'i']]
                ]
            ];
            break;
        default:
            return 0;
    }
    
    return $collection->count($query);
}

// Helper function to generate percentage message
function generatePercentageMessage($percentages) {
    $message = "Course Distribution Analysis:\n\n";
    
    foreach ($percentages as $course => $data) {
        $message .= "• {$course}: {$data['count']} graduates ({$data['percentage']}%)\n";
    }
    
    // Find the highest percentage
    $max_course = '';
    $max_percentage = 0;
    foreach ($percentages as $course => $data) {
        if ($data['percentage'] > $max_percentage) {
            $max_percentage = $data['percentage'];
            $max_course = $course;
        }
    }
    
    if ($max_course) {
        $message .= "\n{$max_course} has the highest representation at {$max_percentage}%.";
    }
    
    return $message;
}

// Helper function to generate person details message
function generatePersonDetailsMessage($person) {
    $message = "📋 Employee Profile: {$person['name']}\n\n";
    $message .= "🏢 Position: {$person['position']}\n";
    
    if ($person['department'] && $person['department'] !== 'Not assigned') {
        $message .= "🏛️ Department: {$person['department']}\n";
    }
    
    $message .= "📧 Email: {$person['email']}\n";
    
    if ($person['phone'] && $person['phone'] !== 'Not provided') {
        $message .= "📞 Phone: {$person['phone']}\n";
    }
    
    $message .= "🎓 Education: {$person['college_degree']}";
    if ($person['college_school'] && $person['college_school'] !== 'Not provided') {
        $message .= " from {$person['college_school']}";
    }
    $message .= "\n";
    
    if ($person['skills'] && $person['skills'] !== 'No skills listed') {
        $message .= "🛠️ Skills: {$person['skills']}\n";
    }
    
    $message .= "\n💼 Status: " . ucfirst($person['collection']);
    
    return $message;
}

// Helper function to generate course comparison message
function generateComparisonMessage($course_counts) {
    $message = "📊 Course Comparison Results:\n\n";
    
    $rank = 1;
    foreach ($course_counts as $course => $count) {
        $message .= "{$rank}. {$course}: {$count} graduates\n";
        $rank++;
    }
    
    if (count($course_counts) > 1) {
        $top_courses = array_keys($course_counts);
        $message .= "\n🏆 {$top_courses[0]} leads with the most graduates.";
    }
    
    return $message;
}
?>