<?php
echo "<h2>Testing AI Server Startup Methods</h2>";

// Test basic shell execution
echo "<h3>Testing Basic Shell Execution:</h3>";
$test1 = shell_exec('echo "Test 1: Basic shell execution works"');
echo "Result: " . ($test1 ? $test1 : "Failed") . "<br>";

// Test Python availability
echo "<h3>Testing Python Availability:</h3>";
$python_version = shell_exec('python --version 2>&1');
echo "Python version: " . ($python_version ? $python_version : "Python not found") . "<br>";

// Test batch file existence
$batFile = "C:\\xampp\\htdocs\\hrims\\assets\\ai\\ai_server_start.bat";
echo "<h3>Testing Batch File:</h3>";
echo "Batch file exists: " . (file_exists($batFile) ? "Yes" : "No") . "<br>";
echo "Batch file path: " . $batFile . "<br>";

// Test different execution methods
echo "<h3>Testing Execution Methods:</h3>";

// Method 1: shell_exec with start
echo "<h4>Method 1: shell_exec with start</h4>";
$command1 = 'start /B "" "' . $batFile . '"';
$result1 = shell_exec($command1);
echo "Result: " . ($result1 !== null ? "Success" : "Failed") . "<br>";

// Method 2: exec with start
echo "<h4>Method 2: exec with start</h4>";
$command2 = 'start /B "" "' . $batFile . '"';
$output2 = [];
$return_var2 = 0;
exec($command2, $output2, $return_var2);
echo "Result: " . ($return_var2 === 0 ? "Success" : "Failed (Code: $return_var2)") . "<br>";

// Method 3: system with start
echo "<h4>Method 3: system with start</h4>";
$command3 = 'start /B "" "' . $batFile . '"';
$result3 = system($command3, $return_var3);
echo "Result: " . ($return_var3 === 0 ? "Success" : "Failed (Code: $return_var3)") . "<br>";

// Method 4: Direct Python execution
echo "<h4>Method 4: Direct Python execution</h4>";
$pythonFile = "C:\\xampp\\htdocs\\hrims\\assets\\ai\\ai_server.py";
$command4 = 'start /B python "' . $pythonFile . '"';
$result4 = shell_exec($command4);
echo "Result: " . ($result4 !== null ? "Success" : "Failed") . "<br>";

// Method 5: cmd /c
echo "<h4>Method 5: cmd /c</h4>";
$command5 = 'cmd /c start /B "" "' . $batFile . '"';
$result5 = shell_exec($command5);
echo "Result: " . ($result5 !== null ? "Success" : "Failed") . "<br>";

// Method 6: PowerShell
echo "<h4>Method 6: PowerShell</h4>";
$psFile = "C:\\xampp\\htdocs\\hrims\\assets\\ai\\start_ai_server.ps1";
$command6 = 'powershell.exe -ExecutionPolicy Bypass -File "' . $psFile . '" start';
$result6 = shell_exec($command6);
echo "Result: " . ($result6 !== null ? "Success" : "Failed") . "<br>";

// Method 7: VBScript
echo "<h4>Method 7: VBScript</h4>";
$vbsFile = "C:\\xampp\\htdocs\\hrims\\assets\\ai\\start_ai_server.vbs";
$command7 = 'cscript //nologo "' . $vbsFile . '"';
$result7 = shell_exec($command7);
echo "Result: " . ($result7 !== null ? "Success" : "Failed") . "<br>";

// Check if any Python processes are running
echo "<h3>Checking for Running Python Processes:</h3>";
$python_processes = shell_exec('tasklist /FI "IMAGENAME eq python.exe" 2>&1');
echo "Python processes:<br><pre>" . htmlspecialchars($python_processes) . "</pre>";

echo "<h3>Test Complete</h3>";
echo "<p>If all methods failed, the issue might be:</p>";
echo "<ul>";
echo "<li>PHP execution permissions</li>";
echo "<li>Windows security policies</li>";
echo "<li>Antivirus blocking execution</li>";
echo "<li>Python not in system PATH</li>";
echo "</ul>";
?>
