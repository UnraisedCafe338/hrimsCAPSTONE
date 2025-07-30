<?php
require '../vendor/autoload.php'; // MongoDB
require '../connection.php';      // MongoDB connection

use MongoDB\Client;

$gridFS = $database->selectGridFSBucket();
$employment_history = [];

// Build employment history from POST arrays
$companies = $_POST['company'] ?? [];
$positions = $_POST['position'] ?? [];
$reasons = $_POST['reason_for_leaving'] ?? [];
for ($i = 0; $i < count($companies); $i++) {
    $employment_history[] = [
        "company" => $companies[$i] ?? "",
        "position" => $positions[$i] ?? "",
        "reason_for_leaving" => $reasons[$i] ?? ""
    ];
}

// Upload documents
$documents = [];
if (!empty($_FILES)) {
    foreach (["2x2_pic", "resume_applicant", "training_certificates", "diploma", "contracts", "transcript_of_records"] as $fileInput) {
        if (!empty($_FILES[$fileInput]["name"][0])) {
            $documents[$fileInput] = [];
            foreach ($_FILES[$fileInput]["name"] as $key => $name) {
                if ($_FILES[$fileInput]["error"][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$fileInput]["tmp_name"][$key];
                    if (is_uploaded_file($tmpName) && file_exists($tmpName)) {
                        $contentType = mime_content_type($tmpName) ?? 'application/octet-stream';
                        $fileStream = fopen($tmpName, 'rb');
                        $fileId = $gridFS->uploadFromStream($name, $fileStream, [
                            'metadata' => ['mimeType' => $contentType, 'originalName' => $name]
                        ]);
                        fclose($fileStream);
                        $documents[$fileInput][] = (string) $fileId;
                    }
                }
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    // ✅ Build applicantData in ONE go
    $applicantData = [
        "position_applied" => $_POST["position_applied"] ?? "",
        "desired_salary" => $_POST["desired_salary"] ?? "",
        "employment_type" => $_POST["employment_type"] ?? "",
        "date_applied" => date("Y-m-d H:i:s"),
        "status" => "Pending",
        "personal_info" => [
            "last_name" => $_POST["last_name"] ?? "",
            "first_name" => $_POST["first_name"] ?? "",
            "middle_name" => $_POST["middle_name"] ?? "",
            "personal_street" => $_POST["personal_street"] ?? "",
            "personal_region" => $_POST["personal_region"] ?? "",
            "personal_province" => $_POST["personal_province"] ?? "",
            "personal_municipality" => $_POST["personal_municipality"] ?? "",
            "personal_barangay" => $_POST["personal_barangay"] ?? "",
            "email" => $_POST["email"] ?? "",
            "contact_no" => isset($_POST["contact_no"]) && $_POST["contact_no"] !== "" ? "+63" . $_POST["contact_no"] : "",
            "age" => $_POST["age"] ?? "",
            "gender" => $_POST["gender"] ?? "",
            "civil_status" => $_POST["civil_status"] ?? "",
            "birth_date" => $_POST["birth_date"] ?? "",
            "birth_place" => $_POST["birth_place"] ?? "",
            "citizenship" => $_POST["citizenship"] ?? "",
            "religion" => $_POST["religion"] ?? "",
            "height" => $_POST["height"] ?? "",
            "weight" => $_POST["weight"] ?? "",
            "disability" => $_POST["disability"] ?? ""
        ],
        "family_background" => [
            "father" => [
                "name" => $_POST["father_name"] ?? "",
                "occupation" => $_POST["father_occupation"] ?? ""
            ],
            "mother" => [
                "name" => $_POST["mother_name"] ?? "",
                "occupation" => $_POST["mother_occupation"] ?? ""
            ],
            "parent_street" => $_POST["parents_address"] ?? "",
            "parent_region" => $_POST["parent_region"] ?? "",
            "parent_province" => $_POST["parent_province"] ?? "",
            "parent_municipality" => $_POST["parent_municipality"] ?? "",
            "parent_barangay" => $_POST["parent_barangay"] ?? "",
            "spouse" => [
                "name" => $_POST["spouse_name"] ?? "",
                "occupation" => $_POST["spouse_occupation"] ?? ""
            ]
        ],
        "education" => [
            "college" => [
                "school" => $_POST["college"] ?? "",
                "degree" => $_POST["college_degree"] ?? ""
            ],
            "high_school" => [
                "school" => $_POST["high_school"] ?? "",
                "degree" => $_POST["high_school_degree"] ?? ""
            ],
            "elementary" => [
                "school" => $_POST["elementary"] ?? "",
                "degree" => $_POST["elementary_degree"] ?? ""
            ],
            "vocational" => [
                "school" => $_POST["vocational"] ?? "",
                "degree" => $_POST["vocational_degree"] ?? ""
            ],
            "masteral" => [
                "school" => $_POST["masteral"] ?? "",
                "degree" => $_POST["masteral_degree"] ?? ""
            ]
        ],
        "skills" => $_POST["skills"] ?? "",
        "emergency_contact" => [
            "name" => $_POST["ref_name"] ?? "",
            "relationship" => $_POST["emergency_relationship"] ?? "",
            "emergency_street" => $_POST["emergency_street"] ?? "",
            "emergency_region" => $_POST["emergency_region"] ?? "",
            "emergency_province" => $_POST["emergency_province"] ?? "",
            "emergency_municipality" => $_POST["emergency_municipality"] ?? "",
            "emergency_barangay" => $_POST["emergency_barangay"] ?? "",
            "emergency_number" => $_POST["emergency_number"] ?? ""
        ],
        "character_reference" => [
            [
                "name" => $_POST["ref1_name"] ?? "",
                "company" => $_POST["ref1_company"] ?? "",
                "position" => $_POST["ref1_position"] ?? "",
                "contact" => $_POST["ref1_contact"] ?? ""
            ],
            [
                "name" => $_POST["ref2_name"] ?? "",
                "company" => $_POST["ref2_company"] ?? "",
                "position" => $_POST["ref2_position"] ?? "",
                "contact" => $_POST["ref2_contact"] ?? ""
            ],
            [
                "name" => $_POST["ref3_name"] ?? "",
                "company" => $_POST["ref3_company"] ?? "",
                "position" => $_POST["ref3_position"] ?? "",
                "contact" => $_POST["ref3_contact"] ?? ""
            ]
        ],
        "additional_info" => [
            "learned_from" => $_POST["how_did_you_learn"] ?? "",
            "certifications" => $_POST["certifications"] ?? "",
            "willing_overtime" => $_POST["willing_overtime"] ?? "",
            "willing_travel" => $_POST["willing_travel"] ?? "",
            "has_driver_license" => $_POST["has_driver_license"] ?? "",
            "vehicle_type" => $_POST["vehicle_type"] ?? ""
        ],
        "employment_history" => $employment_history,
        "documents" => $documents,
        "questionnaire" => [
            "description" => $_POST["description"] ?? "",
            "career_plans" => $_POST["career_plans"] ?? "",
            "reason_for_joining" => $_POST["reason_for_joining"] ?? "",
            "why_hire" => $_POST["why_hire"] ?? "",
            "expectations" => $_POST["expectations"] ?? ""
        ]
    ];

    // ✅ Then build helper variables if needed
    $personal = $applicantData['personal_info'] ?? [];
    $family = $applicantData['family_background'] ?? [];
    $education = $applicantData['education'] ?? [];
    $skills = $applicantData['skills'] ?? '';
    $emergency = $applicantData['emergency_contact'] ?? [];
    $references = $applicantData['character_reference'] ?? [];
    $additional = $applicantData['additional_info'] ?? [];
    $employmentHistory = $applicantData['employment_history'] ?? [];
    $documents = $applicantData['documents'] ?? [];
    $questionnaire = $applicantData['questionnaire'] ?? [];

    // Build addresses
    $addressParts = [
        $personal['personal_street'] ?? '',
        $personal['personal_barangay'] ?? '',
        $personal['personal_municipality'] ?? '',
        $personal['personal_province'] ?? '',
        $personal['personal_region'] ?? ''
    ];
    $currentAddress = implode(', ', array_filter($addressParts));

    $parentAddressParts = [
        $family['parents_address'] ?? '',
        $family['parent_barangay'] ?? '',
        $family['parent_municipality'] ?? '',
        $family['parent_province'] ?? '',
        $family['parent_region'] ?? ''
    ];
    $parentsFullAddress = implode(', ', array_filter($parentAddressParts));

    $emergencyAddressParts = [
        $emergency['emergency_street'] ?? '',
        $emergency['emergency_barangay'] ?? '',
        $emergency['emergency_municipality'] ?? '',
        $emergency['emergency_province'] ?? '',
        $emergency['emergency_region'] ?? ''
    ];
    $emergencyFullAddress = implode(', ', array_filter($emergencyAddressParts));

    // ✅ Insert into MongoDB
    $collection = $database->selectCollection("applicants");
    $insertResult = $collection->insertOne($applicantData);

    if ($insertResult->getInsertedCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Application submitted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to submit application."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
