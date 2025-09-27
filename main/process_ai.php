<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prompt = $_POST["prompt"] ?? '';
    $data   = $_POST["data"] ?? '';   // <-- NEW: employee/applicant JSON
    $resumeFile = $_SESSION["latest_resume"] ?? '';

    // Prepare log file path
    $logFile = __DIR__ . "/ai_debug.log";
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

    // --- Use the improved AI script directly ---
    $python = getenv('HRIMS_PYTHON') ?: 'C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
    $aiScript = realpath(__DIR__ . "/ai_script.py");

    if ($aiScript && file_exists($aiScript)) {
        // Use the simple prompt directly with our enhanced AI script
        $command = $python . " " . escapeshellarg($aiScript) . " " . escapeshellarg($prompt) . " 2>&1";
        
        if (!empty($logFile)) { @file_put_contents($logFile, "Command: $command\n", FILE_APPEND); }

        $output = shell_exec($command);

        if (!empty($logFile)) { @file_put_contents($logFile, "Output:\n" . $output . "\n", FILE_APPEND); }

        if (!empty($output)) {
            // Clean up debug messages for web display
            $lines = explode("\n", $output);
            $cleanedLines = [];
            
            foreach ($lines as $line) {
                // Skip debug lines that start with "DEBUG:"
                if (!preg_match('/^DEBUG:/', trim($line))) {
                    $cleanedLines[] = $line;
                }
            }
            
            $cleanedOutput = implode("\n", $cleanedLines);
            echo trim($cleanedOutput);
            exit;
        }
    }

    echo "No response available.";
}
?>
