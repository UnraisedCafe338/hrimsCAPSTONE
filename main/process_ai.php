<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prompt = $_POST["prompt"] ?? '';
    $resumeFile = $_SESSION["latest_resume"] ?? '';

    // If there is a resume file, read its text
    $resume_text = '';
    if (!empty($resumeFile)) {
        $path = __DIR__ . "/../uploads/" . basename($resumeFile);
        if (file_exists($path)) {
            if (str_ends_with(strtolower($path), '.pdf')) {
                // If you still need PDF parsing, you can keep ai_script.py for that
                $resume_text = shell_exec(
                    "C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe " .
                    escapeshellarg("extract_text.py") . " " . escapeshellarg($path)
                );
            } elseif (str_ends_with(strtolower($path), '.docx')) {
                $resume_text = shell_exec(
                    "C:\\Users\\LENOVO\\AppData\\Local\\Programs\\Python\\Python312\\python.exe " .
                    escapeshellarg("extract_text.py") . " " . escapeshellarg($path)
                );
            }
        }
    }

    // Construct the prompt
    $system_prompt = !empty($resume_text)
        ? "You are an HR AI assistant. Summarize this resume, highlight skills, and recommend roles."
        : "You are an AI assistant inside a Human Resources Management System. Respond clearly.";
    $full_prompt = "[INST] <<SYS>>\n{$system_prompt}\n<</SYS>>\n\n{$prompt}\n{$resume_text} [/INST]";

    // Send to llama.cpp server
    $url = "http://127.0.0.1:8000/v1/completions";
    $data = [
        "model" => "your_model",  // The alias from --model_alias if you set it
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

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        echo "AI request failed.";
    } else {
        $json = json_decode($result, true);
        echo $json["choices"][0]["text"] ?? "No response";
    }
}
?>
