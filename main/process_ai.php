<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prompt = $_POST["prompt"] ?? '';
    $resumeFile = $_SESSION["latest_resume"] ?? '';

    // Write full prompt to a temporary file
    $tempFilePath = "temp/prompt" . time() . ".txt";
    file_put_contents($tempFilePath, $prompt);

    $python = "C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";
    $script = escapeshellarg("ai_script.py");
    $escaped_prompt_file = escapeshellarg($tempFilePath);

    if (!empty($resumeFile)) {
        $escaped_resume = escapeshellarg(basename($resumeFile));
        $command = "$python $script $escaped_prompt_file $escaped_resume";
    } else {
        $command = "$python $script $escaped_prompt_file";
    }

    $output = [];
    exec($command . " 2>&1", $output, $return_var);

    if ($return_var !== 0 || empty($output)) {
        echo "AI failed to respond. Error details:\n" . implode("\n", $output);
    } else {
        echo implode("\n", $output);
    }

    // Optionally clean up
    unlink($tempFilePath);
}
?>