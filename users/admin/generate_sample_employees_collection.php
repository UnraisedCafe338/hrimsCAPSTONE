<?php
// Generate Sample Employees Collection Data
// This script creates sample employee data with various education backgrounds for the employees collection

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("employee");
    
    echo "Connected to MongoDB successfully!\n";
    
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// First, let's clear any existing sample employee data (documents with our sample email pattern)
echo "Clearing existing sample employee data...\n";
$deleteResult = $collection->deleteMany([
    'personal_info.email' => ['$regex' => '.*@company.com$']
]);
echo "Deleted " . $deleteResult->getDeletedCount() . " existing sample employees\n\n";

// Sample employee data with various education backgrounds
$sampleEmployees = [
    [
        "personal_info" => [
            "last_name" => "Santos",
            "first_name" => "Princes Lyka",
            "middle_name" => "M",
            "email" => "princeslyka.santos@company.com",
            "contact" => "+639123456781",
            "age" => "24",
            "sex" => "Female",
            "civil_status" => "single",
            "birth_date" => "2000-03-15",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of the Philippines",
                "degree" => "Bachelor of Science in Information Technology"
            ],
            "masteral" => [
                "school" => "Ateneo de Manila University",
                "degree" => "Master of Science in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Dela Cruz",
            "first_name" => "Juan",
            "middle_name" => "Miguel",
            "email" => "juan.delacruz@company.com",
            "contact" => "+639123456782",
            "age" => "28",
            "sex" => "Male",
            "civil_status" => "married",
            "birth_date" => "1996-07-22",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "De La Salle University",
                "degree" => "Bachelor of Science in Information Systems"
            ],
            "masteral" => [
                "school" => "University of Santo Tomas",
                "degree" => "Master of Science in Information Technology"
            ],
            "doctoral" => [
                "school" => "Philippine Normal University",
                "degree" => "Doctor of Philosophy in Information Technology"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Reyes",
            "first_name" => "Maria",
            "middle_name" => "Andrea",
            "email" => "maria.reyes@company.com",
            "contact" => "+639123456783",
            "age" => "26",
            "sex" => "Female",
            "civil_status" => "single",
            "birth_date" => "1998-11-30",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "Mapua University",
                "degree" => "Bachelor of Science in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Garcia",
            "first_name" => "Carlos",
            "middle_name" => "Jose",
            "email" => "carlos.garcia@company.com",
            "contact" => "+639123456784",
            "age" => "30",
            "sex" => "Male",
            "civil_status" => "married",
            "birth_date" => "1994-01-10",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of the East",
                "degree" => "Bachelor of Science in Information Technology"
            ],
            "masteral" => [
                "school" => "Polytechnic University of the Philippines",
                "degree" => "Master of Science in Information Systems"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Lim",
            "first_name" => "Sophia",
            "middle_name" => "Patricia",
            "email" => "sophia.lim@company.com",
            "contact" => "+639123456785",
            "age" => "25",
            "sex" => "Female",
            "civil_status" => "single",
            "birth_date" => "1999-05-18",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "Far Eastern University",
                "degree" => "Bachelor of Science in Computer Engineering"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Tan",
            "first_name" => "Michael",
            "middle_name" => "David",
            "email" => "michael.tan@company.com",
            "contact" => "+639123456786",
            "age" => "29",
            "sex" => "Male",
            "civil_status" => "married",
            "birth_date" => "1995-09-12",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "Technological Institute of the Philippines",
                "degree" => "Bachelor of Science in Information Systems"
            ],
            "masteral" => [
                "school" => "University of Makati",
                "degree" => "Master of Science in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Rodriguez",
            "first_name" => "Isabella",
            "middle_name" => "Marie",
            "email" => "isabella.rodriguez@company.com",
            "contact" => "+639123456787",
            "age" => "27",
            "sex" => "Female",
            "civil_status" => "single",
            "birth_date" => "1997-02-28",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "Adamson University",
                "degree" => "Bachelor of Science in Information Technology"
            ],
            "doctoral" => [
                "school" => "University of the Philippines",
                "degree" => "Doctor of Philosophy in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Cruz",
            "first_name" => "Roberto",
            "middle_name" => "Antonio",
            "email" => "roberto.cruz@company.com",
            "contact" => "+639123456788",
            "age" => "32",
            "sex" => "Male",
            "civil_status" => "married",
            "birth_date" => "1992-12-05",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "San Beda University",
                "degree" => "Bachelor of Science in Computer Science"
            ],
            "masteral" => [
                "school" => "Ateneo de Naga University",
                "degree" => "Master of Science in Information Technology"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Chua",
            "first_name" => "Amanda",
            "middle_name" => "Louise",
            "email" => "amanda.chua@company.com",
            "contact" => "+639123456789",
            "age" => "23",
            "sex" => "Female",
            "civil_status" => "single",
            "birth_date" => "2001-08-14",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Asia and the Pacific",
                "degree" => "Bachelor of Science in Information Systems"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Flores",
            "first_name" => "Daniel",
            "middle_name" => "Christopher",
            "email" => "daniel.flores@company.com",
            "contact" => "+639123456790",
            "age" => "26",
            "sex" => "Male",
            "civil_status" => "single",
            "birth_date" => "1998-04-25",
            "citizen" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "FEU Institute of Technology",
                "degree" => "Bachelor of Science in Information Technology"
            ],
            "masteral" => [
                "school" => "University of the Philippines",
                "degree" => "Master of Science in Computer Science"
            ]
        ]
    ]
];

echo "Generating sample employees with various education backgrounds...\n";

try {
    foreach ($sampleEmployees as $index => $employee) {
        // Create the document structure
        $document = [
            "position_applied" => "Software Engineer",
            "desired_salary" => "45000",
            "status" => "Hired",
            "personal_info" => $employee['personal_info'],
            "family_background" => [
                "father" => ["name" => "Father Name", "occupation" => "Engineer"],
                "mother" => ["name" => "Mother Name", "occupation" => "Teacher"],
                "parents_address" => "Sample Address",
                "spouse" => ["name" => "N/A", "occupation" => "N/A"]
            ],
            "education" => $employee['education'],
            "skills" => "Programming, Problem Solving, Team Leadership",
            "employment_history" => [
                "company" => "Previous Company",
                "position" => "Junior Developer",
                "reason_for_leaving" => "Career Growth"
            ],
            "emergency_contact" => [
                "name" => "Emergency Contact",
                "relationship" => "Sibling",
                "emergency_address" => "Sample Emergency Address",
                "emergency_number" => "+639999999999"
            ],
            "character_reference" => [
                "name" => "Reference Name",
                "company" => "Reference Company",
                "position" => "Manager",
                "contact" => "+639888888888"
            ],
            "documents" => [],
            "questionnaire" => [
                "description" => "Experienced software engineer with expertise in multiple programming languages",
                "career_plans" => "To become a senior developer and eventually team lead",
                "reason_for_joining" => "Opportunity to work with cutting-edge technologies",
                "why_hire" => "Proven track record of delivering quality software solutions",
                "expectations" => "Professional growth and challenging projects",
                "date_hired" => "2025-10-17",
                "employment_type" => "Full-time",
                "department" => "IT Department",
                "faculty_type" => "Teaching",
                "status" => "Active"
            ]
        ];
        
        // Insert the document
        $result = $collection->insertOne($document);
        
        echo "Inserted employee: " . $employee['personal_info']['first_name'] . " " . $employee['personal_info']['last_name'] . " with ID: " . $result->getInsertedId() . "\n";
    }
    
    echo "\nSuccessfully inserted " . count($sampleEmployees) . " sample employees with various education backgrounds!\n";
    echo "These employees can now be found by the AI system with queries like:\n";
    echo "- 'Find all Information Technology employees'\n";
    echo "- 'List employees with Masteral degrees'\n";
    echo "- 'Show me all Computer Science employees'\n";
    echo "- 'Find all Information Systems employees'\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>