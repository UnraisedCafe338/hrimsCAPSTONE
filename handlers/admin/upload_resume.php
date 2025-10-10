<?php
if (!isset($_FILES['resume'])) {
    echo json_encode(["error" => "No file uploaded."]);
    exit;
}

$uploadDir = "../uploads/";
$filename = basename($_FILES['resume']['name']);
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath)) {
    // ✅ File uploaded, now send file path to ai_script.py
    $absPath = realpath($targetPath);
    $aiScript = realpath("../main/ai_script.py");

    if (!$absPath || !$aiScript) {
        echo json_encode(["error" => "Path resolution failed."]);
        exit;
    }

    $command = "python " . escapeshellarg($aiScript) . " " . escapeshellarg($absPath);
    $output = shell_exec($command);

    echo json_encode([
        "message" => "✅ Document summarized.",
        "ai_response" => trim($output)
    ]);
} else {
    echo json_encode(["error" => "Failed to upload file."]);
}
