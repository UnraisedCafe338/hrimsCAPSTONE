<?php
if (!isset($_GET['action'])) {
    exit("No action given.");
}

$action = $_GET['action'];
$cudaBatFile = "C:\\xampp\\htdocs\\hrims\\assets\\ai\\start_ai_cuda.bat"; // CUDA optimized

if ($action === "start") {
    if (!file_exists($cudaBatFile)) {
        echo "❌ Starter not found: " . $cudaBatFile;
        exit;
    }

    // Launch the CUDA starter (non-blocking) via cmd.exe so 'start' works under Apache on Windows
    $cmd = 'cmd.exe /c start "" "' . $cudaBatFile . '"';
    exec($cmd);

    // Poll status endpoint up to ~15s
    $context = stream_context_create(['http' => ['timeout' => 2]]);
    $ok = false;
    $serverInfo = null;
    for ($i = 0; $i < 6; $i++) { // 6 x 2s ≈ 12s total
        usleep(500000); // 0.5s
        $result = @file_get_contents('http://127.0.0.1:8000/status', false, $context);
        if ($result !== false) {
            $ok = true;
            $serverInfo = json_decode($result, true);
            break;
        }
        sleep(2);
    }

    if ($ok) {
        $gpuLayers = $serverInfo && isset($serverInfo['gpu_layers']) ? $serverInfo['gpu_layers'] : 'unknown';
        echo "✅ AI server started. GPU Layers: " . $gpuLayers;
    } else {
        echo "⚠️ Start command sent, but server not responding yet. Give it a few more seconds and try again.";
    }
    
} elseif ($action === "stop") {
    // Kill Python process (or llama server)
    $output = [];
    $return_var = 0;
    exec("taskkill /F /IM python.exe", $output, $return_var);
    if ($return_var === 0) {
        echo "✅ AI server stopped.";
    } else {
        echo "⚠️ No Python processes found or failed to stop.";
    }
} else {
    echo "❌ Unknown action.";
}
?>
