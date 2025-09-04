<?php
session_start();

require '../vendor/autoload.php'; // MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");

// Collections
$applicantsCol = $client->hrims->applicants;
$employeesCol  = $client->hrims->employee;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prompt = $_POST["prompt"] ?? '';
    $resumeFile = $_SESSION["latest_resume"] ?? '';

    // --- Resume Handling ---
    $resume_text = '';
    if (!empty($resumeFile)) {
        $path = __DIR__ . "/../uploads/" . basename($resumeFile);
        if (file_exists($path)) {
            $resume_text = shell_exec(
                "C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe " .
                escapeshellarg("extract_text.py") . " " . escapeshellarg($path)
            );
        }
    }

    // --- Database Context ---
    $context = '';
    $degree = '';

    // Try to detect degree keyword in prompt
    if (preg_match('/\b([A-Za-z\s]+)\b/i', $prompt, $match)) {
        $degree = trim($match[1]);
    }

    if (!empty($degree)) {
        // Search in employees first (priority)
        $empCount = $employeesCol->countDocuments([
            "education.college.degree" => new MongoDB\BSON\Regex($degree, "i")
        ]);
        $appCount = $applicantsCol->countDocuments([
            "education.college.degree" => new MongoDB\BSON\Regex($degree, "i")
        ]);

        if ($empCount > 0 || $appCount > 0) {
            $context .= "Faculty/Employees with degree in {$degree}: {$empCount}. ";
            $context .= "Applicants with degree in {$degree}: {$appCount}. ";
        } else {
            $context .= "No data found for degree: {$degree}. ";
        }
    }

    // Skills context
    if (stripos($prompt, "skills") !== false) {
        $skillsEmp = $employeesCol->distinct("skills");
        $skillsApp = $applicantsCol->distinct("skills");
        $skills = array_unique(array_merge($skillsEmp, $skillsApp));
        $context .= "Unique skills in database: " . implode(", ", $skills) . ". ";
    }

    // If asking "natapos" or "degree", list all available degrees
    if (stripos($prompt, "natapos") !== false || stripos($prompt, "degree") !== false) {
        $degEmp = $employeesCol->distinct("education.college.degree");
        $degApp = $applicantsCol->distinct("education.college.degree");
        $names = array_filter(array_unique(array_merge($degEmp, $degApp)));
        $context .= "Available degrees in database: " . implode(", ", $names) . ". ";
    }

    // --- AI Prompt ---
    $system_prompt = "You are an HR AI assistant. 
- Always prioritize faculty/employee data before applicants.
- Only use the Database Context for numbers. 
- Do not invent or assume data.
- If the Database Context says 'No data found', you must reply the same.
- Use resume text only for extra context, not for counts.";

    $full_prompt = "[INST] <<SYS>>\n{$system_prompt}\n<</SYS>>\n\n"
                 . "Resume Text:\n{$resume_text}\n\n"
                 . "Database Context:\n{$context}\n\n"
                 . "User Query: {$prompt} [/INST]";

    // --- AI Server Check ---
    $statusUrl = "http://127.0.0.1:8000/status";
    $statusOk = @file_get_contents($statusUrl);
    if ($statusOk === false) {
        echo "AI server is not running. Click Start AI and try again.";
        exit;
    }

    // --- Send to AI ---
    $url = "http://127.0.0.1:8000/v1/completions";
    $data = [
        "model" => "../assets/ai/mistral-7b-instruct-v0.2.Q4_K_M.gguf",
        "prompt" => $full_prompt,
        "max_tokens" => 512,
        "stop" => ["</s>"]
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json\r\n",
            "method"  => "POST",
            "content" => json_encode($data),
        ],
    ];

    $ctx = stream_context_create($options);
    $result = file_get_contents($url, false, $ctx);

    if ($result === FALSE) {
        echo "AI request failed.";
    } else {
        $json = json_decode($result, true);
        echo $json["choices"][0]["text"] ?? "No response";
    }
}
?>
