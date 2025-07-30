<?php
require '../vendor/autoload.php';
require '../connection.php';

use MongoDB\BSON\ObjectId;

$gridFS = $database->selectGridFSBucket();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    $applicantId = $_GET['id'] ?? '';

    if (!$applicantId) {
        echo json_encode(["status" => "error", "message" => "Missing applicant ID"]);
        exit;
    }

    $collection = $database->selectCollection("applicants");

    // Build update data
    $updateData = [
        "position_applied" => $_POST["position_applied"] ?? "",
        "desired_salary" => $_POST["desired_salary"] ?? "",
        "personal_info" => [
            "last_name" => $_POST["last_name"] ?? "",
            "first_name" => $_POST["first_name"] ?? "",
            "middle_name" => $_POST["middle_name"] ?? "",
            "address" => $_POST["address"] ?? "",
            "email" => $_POST["email"] ?? "",
            "contact_no" => $_POST["contact_no"] ?? "",
            "age" => $_POST["age"] ?? "",
            "gender" => $_POST["gender"] ?? "",
            "civil_status" => $_POST["civil_status"] ?? "",
            "birth_date" => $_POST["birth_date"] ?? "",
            "birth_place" => $_POST["birth_place"] ?? "",
            "citizen" => $_POST["citizen"] ?? "",
            "religion" => $_POST["religion"] ?? "",
            "height" => $_POST["height"] ?? "",
            "weight" => $_POST["weight"] ?? "",
            "disability" => $_POST["disability"] ?? ""

        ],
        "family_background" => [
            "father" => [
                "name" => $POST["father_name"] ?? "",
                "occupation" => $_POST["father_occupation"] ?? "",
            ],
            "mother" => [
                "name" => $_POST["mother_name"] ?? "",
                "occupation" => $_POST["mother_occupation"] ?? "",
            ],
            "spouse" => [
                "name" => $_POST["spouse_name"] ?? "",
                "occupation" => $_POST["spouse_occupation"] ?? ""
            ],
        ],
        "education" => [ 
          "college"  => [
            "school" => $POST["college_school"] ?? "",
            "degree" => $POST["college_degree"] ?? ""
          ],
 
          "high_school"  => [
            "school" => $POST["highschool_school"] ?? "",
            "degree" => $POST["highschool_degree"] ?? ""
          ],
          "elementary"  => [
            "school" => $POST["elementary_school"] ?? "",
            "degree" => $POST["elementary_degree"] ?? ""
          ],
           "vocational"  => [
            "school" => $POST["vocational_school"] ?? "",
            "degree" => $POST["vocational_degree"] ?? ""
          ],
           "masteral"  => [
            "school" => $POST["masteral_school"] ?? "",
            "degree" => $POST["masteral_degree"] ?? ""
          ],
        ],
        
        "emergency_contact" => [
            "name" => $_POST["emergency_contact_name"] ?? "",
            "relationship" => $_POST["emergency_relationship"] ?? "",
            "contact_no" => $_POST["emergency_number"] ?? "",

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
        "questionnaire" => [
            "description" => $_POST["description"] ?? "",
            "career_plans" => $_POST["career_plans"] ?? "",
            "reason_for_joining" => $_POST["reason_for_joining"] ?? "",
            "why_hire" => $_POST["why_hire"] ?? "",
            "expectations" => $_POST["expectations"] ?? ""
        ],

    ];
    

    // Update 2x2 picture if new one is uploaded
    if (isset($_FILES["2x2pic"]) && $_FILES["2x2pic"]["error"] === UPLOAD_ERR_OK) {
        $fileStream = fopen($_FILES["2x2pic"]["tmp_name"], 'rb');
        $photoId = $gridFS->uploadFromStream($_FILES["2x2pic"]["name"], $fileStream);
        fclose($fileStream);
        $updateData["personal_info"]["photo_id"] = (string) $photoId;
    }

    // Optional: Add new uploaded files to documents
    $documents = [];
    foreach (["resume_applicant", "training_certificates", "diploma", "contracts", "transcript_of_records"] as $fileInput) {
        if (!empty($_FILES[$fileInput]["name"][0])) {
            foreach ($_FILES[$fileInput]["name"] as $key => $name) {
                if ($_FILES[$fileInput]["error"][$key] === UPLOAD_ERR_OK) {
                    $fileStream = fopen($_FILES[$fileInput]["tmp_name"][$key], 'rb');
                    $fileId = $gridFS->uploadFromStream($name, $fileStream);
                    fclose($fileStream);
                    $documents[$fileInput][] = (string) $fileId;
                }
            }
        }
    }

    // Apply update using $set and $push for documents
    $updateQuery = ['$set' => $updateData];

    if (!empty($documents)) {
        foreach ($documents as $docType => $fileIds) {
            foreach ($fileIds as $fileId) {
                $updateQuery['$push']["documents.$docType"] = $fileId;
            }
        }
    }

    $result = $collection->updateOne(
        ['_id' => new ObjectId($applicantId)],
        $updateQuery
    );

    if ($result->getModifiedCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Application updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No changes made or update failed."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
