<?php
// AI Configuration with NVIDIA GPU Support
// This file contains configuration settings for the AI system

// AI Server Configuration
define('AI_SERVER_URL', 'http://127.0.0.1:8001');
define('AI_SERVER_STATUS_ENDPOINT', AI_SERVER_URL . '/status');
define('AI_SERVER_COMPLETIONS_ENDPOINT', AI_SERVER_URL . '/v1/completions');

// NVIDIA GPU Configuration
define('NVIDIA_GPU_ENABLED', true);
define('GPU_LAYERS', 35);
define('CONTEXT_SIZE', 4096);
define('THREADS', 6);

// Model Configuration
define('MODEL_PATH', 'C:\\Users\\LENOVO\\Downloads\\mistral-7b-instruct-v0.2.Q4_K_M.gguf');
define('LLAMA_RUN_PATH', 'C:\\Users\\LENOVO\\llama.cpp\\build\\bin\\Release\\llama-run.exe');

// Fallback Configuration
define('AI_SERVER_TIMEOUT', 60);
define('AI_PROCESS_TIMEOUT', 120);

// Database Query Configuration
define('DB_QUERY_TIMEOUT', 5);
define('MAX_RESULTS_DISPLAY', 5);
define('MAX_RESULTS_SEARCH', 20);

// Error Messages
define('ERROR_NO_PROMPT', 'Please provide a prompt');
define('ERROR_SERVER_NOT_RUNNING', 'AI server is not running');
define('ERROR_SERVER_TIMEOUT', 'AI server timeout');
define('ERROR_NO_RESPONSE', 'No response from AI server');

// Success Messages
define('SUCCESS_SERVER_STARTED', 'AI server started successfully with NVIDIA GPU acceleration');
define('SUCCESS_SERVER_STOPPED', 'AI server stopped successfully');
define('SUCCESS_SERVER_ALREADY_RUNNING', 'AI server is already running with NVIDIA GPU acceleration');

// Log File
define('AI_LOG_FILE', __DIR__ . '/../handlers/ai/ai_server.log');

?>