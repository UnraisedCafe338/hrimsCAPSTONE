<?php
// Generate Sample Employees with Various Education Backgrounds
// This script creates sample employee data with different education levels for testing

// Include the MongoDB library
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->selectDatabase("hrims_db");
    $collection = $database->selectCollection("applicants");
    
    echo "Connected to MongoDB successfully!\n";
    
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// First, let's clear any existing sample data (documents with our sample email pattern)
echo "Clearing existing sample data...\n";
$deleteResult = $collection->deleteMany([
    'personal_info.email' => ['$regex' => '.*@example.com$']
]);
echo "Deleted " . $deleteResult->getDeletedCount() . " existing sample employees\n\n";

// Sample employee data with various education backgrounds
$sampleEmployees = [
    [
        "personal_info" => [
            "last_name" => "Santos",
            "first_name" => "Princes Lyka",
            "middle_name" => "M",
            "email" => "princeslyka.santos@example.com",
            "contact_no" => "+639123456781",
            "age" => "24",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "2000-03-15",
            "citizenship" => "filipino"
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
            "email" => "juan.delacruz@example.com",
            "contact_no" => "+639123456782",
            "age" => "28",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1996-07-22",
            "citizenship" => "filipino"
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
            "email" => "maria.reyes@example.com",
            "contact_no" => "+639123456783",
            "age" => "26",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "1998-11-30",
            "citizenship" => "filipino"
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
            "email" => "carlos.garcia@example.com",
            "contact_no" => "+639123456784",
            "age" => "30",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1994-01-10",
            "citizenship" => "filipino"
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
            "email" => "sophia.lim@example.com",
            "contact_no" => "+639123456785",
            "age" => "25",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "1999-05-18",
            "citizenship" => "filipino"
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
            "email" => "michael.tan@example.com",
            "contact_no" => "+639123456786",
            "age" => "29",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1995-09-12",
            "citizenship" => "filipino"
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
            "email" => "isabella.rodriguez@example.com",
            "contact_no" => "+639123456787",
            "age" => "27",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "1997-02-28",
            "citizenship" => "filipino"
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
            "email" => "roberto.cruz@example.com",
            "contact_no" => "+639123456788",
            "age" => "32",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1992-12-05",
            "citizenship" => "filipino"
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
            "email" => "amanda.chua@example.com",
            "contact_no" => "+639123456789",
            "age" => "23",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "2001-08-14",
            "citizenship" => "filipino"
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
            "email" => "daniel.flores@example.com",
            "contact_no" => "+639123456790",
            "age" => "26",
            "gender" => "male",
            "civil_status" => "single",
            "birth_date" => "1998-04-25",
            "citizenship" => "filipino"
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
    ],
    [
        "personal_info" => [
            "last_name" => "Bautista",
            "first_name" => "Elena",
            "middle_name" => "Grace",
            "email" => "elena.bautista@example.com",
            "contact_no" => "+639123456791",
            "age" => "31",
            "gender" => "female",
            "civil_status" => "married",
            "birth_date" => "1993-10-08",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "Centro Escolar University",
                "degree" => "Bachelor of Science in Computer Science"
            ],
            "masteral" => [
                "school" => "De La Salle University",
                "degree" => "Master of Science in Information Systems"
            ],
            "doctoral" => [
                "school" => "University of Santo Tomas",
                "degree" => "Doctor of Philosophy in Information Technology"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Villanueva",
            "first_name" => "Gabriel",
            "middle_name" => "James",
            "email" => "gabriel.villanueva@example.com",
            "contact_no" => "+639123456792",
            "age" => "24",
            "gender" => "male",
            "civil_status" => "single",
            "birth_date" => "2000-01-17",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "AMA Computer University",
                "degree" => "Bachelor of Science in Information Technology"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Castillo",
            "first_name" => "Hannah",
            "middle_name" => "Faith",
            "email" => "hannah.castillo@example.com",
            "contact_no" => "+639123456793",
            "age" => "27",
            "gender" => "female",
            "civil_status" => "married",
            "birth_date" => "1997-06-30",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Mindanao",
                "degree" => "Bachelor of Science in Information Systems"
            ],
            "masteral" => [
                "school" => "Mindanao State University",
                "degree" => "Master of Science in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Domingo",
            "first_name" => "Isaac",
            "middle_name" => "Matthew",
            "email" => "isaac.domingo@example.com",
            "contact_no" => "+639123456794",
            "age" => "29",
            "gender" => "male",
            "civil_status" => "single",
            "birth_date" => "1995-03-22",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Cebu",
                "degree" => "Bachelor of Science in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Mendoza",
            "first_name" => "Julia",
            "middle_name" => "Rose",
            "email" => "julia.mendoza@example.com",
            "contact_no" => "+639123456795",
            "age" => "25",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "1999-11-11",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "STI College",
                "degree" => "Bachelor of Science in Information Technology"
            ],
            "masteral" => [
                "school" => "University of San Carlos",
                "degree" => "Master of Science in Information Technology"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Ramos",
            "first_name" => "Kevin",
            "middle_name" => "Paul",
            "email" => "kevin.ramos@example.com",
            "contact_no" => "+639123456796",
            "age" => "30",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1994-07-19",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Baguio",
                "degree" => "Bachelor of Science in Information Systems"
            ],
            "doctoral" => [
                "school" => "University of the East",
                "degree" => "Doctor of Philosophy in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Navarro",
            "first_name" => "Luna",
            "middle_name" => "Claire",
            "email" => "luna.navarro@example.com",
            "contact_no" => "+639123456797",
            "age" => "26",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "1998-09-03",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Iloilo",
                "degree" => "Bachelor of Science in Computer Science"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Torres",
            "first_name" => "Marcus",
            "middle_name" => "Ryan",
            "email" => "marcus.torres@example.com",
            "contact_no" => "+639123456798",
            "age" => "28",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1996-12-27",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Negros Occidental - Recoletos",
                "degree" => "Bachelor of Science in Information Technology"
            ],
            "masteral" => [
                "school" => "University of Perpetual Help System DALTA",
                "degree" => "Master of Science in Information Systems"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Santiago",
            "first_name" => "Nina",
            "middle_name" => "Joy",
            "email" => "nina.santiago@example.com",
            "contact_no" => "+639123456799",
            "age" => "23",
            "gender" => "female",
            "civil_status" => "single",
            "birth_date" => "2001-04-16",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "University of Southeastern Philippines",
                "degree" => "Bachelor of Science in Information Systems"
            ]
        ]
    ],
    [
        "personal_info" => [
            "last_name" => "Aquino",
            "first_name" => "Oscar",
            "middle_name" => "Mark",
            "email" => "oscar.aquino@example.com",
            "contact_no" => "+639123456800",
            "age" => "31",
            "gender" => "male",
            "civil_status" => "married",
            "birth_date" => "1993-08-09",
            "citizenship" => "filipino"
        ],
        "education" => [
            "college" => [
                "school" => "Xavier University - Ateneo de Cagayan",
                "degree" => "Bachelor of Science in Computer Science"
            ],
            "masteral" => [
                "school" => "University of Southern Mindanao",
                "degree" => "Master of Science in Computer Science"
            ],
            "doctoral" => [
                "school" => "Central Philippine University",
                "degree" => "Doctor of Philosophy in Information Technology"
            ]
        ]
    ]
];

echo "Generating sample employees with various education backgrounds...\n";

try {
    foreach ($sampleEmployees as $index => $employee) {
        // Create the document structure
        $document = [
            "position_applied" => "",
            "desired_salary" => "",
            "status" => "Approved",
            "personal_info" => $employee['personal_info'],
            "family_background" => [
                "father" => ["name" => "", "occupation" => ""],
                "mother" => ["name" => "", "occupation" => ""],
                "spouse" => ["name" => "", "occupation" => ""]
            ],
            "education" => $employee['education'],
            "skills" => "Sample skills for employee " . ($index + 1),
            "employment_history" => [
                "company" => "",
                "position" => "",
                "reason_for_leaving" => ""
            ],
            "emergency_contact" => [
                "name" => "",
                "relationship" => "",
                "emergency_street" => "",
                "emergency_region" => "",
                "emergency_province" => "",
                "emergency_municipality" => "",
                "emergency_barangay" => "",
                "emergency_number" => ""
            ],
            "character_reference" => [
                ["name" => "", "company" => "", "position" => "", "contact" => ""],
                ["name" => "", "company" => "", "position" => "", "contact" => ""],
                ["name" => "", "company" => "", "position" => "", "contact" => ""]
            ],
            "additional_info" => [
                "learned_from" => "",
                "certifications" => "",
                "willing_overtime" => "",
                "willing_travel" => "",
                "has_driver_license" => "",
                "vehicle_type" => ""
            ],
            "documents" => [],
            "questionnaire" => [
                "description" => "",
                "career_plans" => "",
                "reason_for_joining" => "",
                "why_hire" => "",
                "expectations" => ""
            ]
        ];
        
        // Insert the document
        $result = $collection->insertOne($document);
        
        echo "Inserted employee: " . $employee['personal_info']['first_name'] . " " . $employee['personal_info']['last_name'] . " with ID: " . $result->getInsertedId() . "\n";
    }
    
    echo "\nSuccessfully inserted " . count($sampleEmployees) . " sample employees with various education backgrounds!\n";
    echo "You can now test the AI system with queries like:\n";
    echo "- 'Find all Information Technology graduates'\n";
    echo "- 'List employees with Masteral degrees'\n";
    echo "- 'Show me all Computer Science graduates'\n";
    echo "- 'Find all Information Systems graduates'\n";
    echo "- 'Who has a Doctoral degree?'\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>