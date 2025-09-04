<?php
$python = "C:\\xampp\\htdocs\\hrims\\assets\\ai\\ai_server.py";
pclose(popen("start /B python " . escapeshellarg($python), "r"));
echo "✅ AI server started silently.";
?>