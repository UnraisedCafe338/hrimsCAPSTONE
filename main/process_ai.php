<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prompt = $_POST["prompt"] ?? '';
    $data   = $_POST["data"] ?? '';   // <-- NEW: employee/applicant JSON
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

    // --- Database Context (we’ll replace this with $data from POST) ---
    $context = $data ?: '';

    // --- AI Prompt ---
    $system_prompt = "You are an HR AI assistant. 
- Always prioritize faculty/employee data before applicants.
- Only use the Database Context for numbers. 
- Do not invent or assume data.
- If the Database Context says 'No data found', you must reply the same.
- Use resume text only for extra context, not for counts.
- Keep answers short and direct.";

    // Truncate large contexts to avoid overflows
    $trunc = function($s, $max) { return mb_substr($s, 0, $max, 'UTF-8'); };
    $resume_text_short = $trunc($resume_text, 2000);
    $context_short = $trunc($context, 2000);

    $full_prompt = "[INST] <<SYS>>\n{$system_prompt}\n<</SYS>>\n\n"
                 . "Resume Text:\n{$resume_text_short}\n\n"
                 . "Database Context:\n{$context_short}\n\n"
                 . "User Query: {$prompt} [/INST]";

    // Prepare log file path
    $logFile = __DIR__ . "/ai_debug.log";

    // --- Local script (raw fallback) ---
    $python = getenv('HRIMS_PYTHON') ?: 'C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
    $aiScript = realpath(__DIR__ . "/ai_script.py");

    if ($aiScript && file_exists($aiScript)) {
        // Pass BOTH the prompt and context JSON to Python
        $command = $python . " " . escapeshellarg($aiScript) . " " 
                 . escapeshellarg($full_prompt) . " " . escapeshellarg($context_short) . " 2>&1";
        
        if (!empty($logFile)) { @file_put_contents($logFile, "Command: $command\n", FILE_APPEND); }

        $output = shell_exec($command);

        if (!empty($logFile)) { @file_put_contents($logFile, "Output:\n" . $output . "\n", FILE_APPEND); }

        if (!empty($output)) {
            echo trim($output);
            exit;
        }
    }

    echo "No response available.";
}
?>
