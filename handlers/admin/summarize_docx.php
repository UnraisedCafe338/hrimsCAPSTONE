<?php
// Load project bootstrap in a robust way (works under web server and CLI/static analysis)
$bootstrapPath = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'])
    ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/hrims/bootstrap.php'
    : realpath(__DIR__ . '/../../../bootstrap.php');
if ($bootstrapPath && file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;

// 📂 Full absolute path to file (hardcoded or from file upload)
$filePath = realpath("../uploads/oralcom.docx");

if (!$filePath || !file_exists($filePath)) {
    die("❌ File not found or invalid path.");
}

$fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$rawText = "";

// 📄 DOCX Handling
if ($fileExt === "docx") {
    $phpWord = IOFactory::load($filePath);
    foreach ($phpWord->getSections() as $section) {
        $elements = $section->getElements();

        foreach ($elements as $element) {
            // ✅ Plain text
            if ($element instanceof Text) {
                $rawText .= $element->getText() . "\n";
            }
            // ✅ TextRun (e.g., inline formatting)
            elseif ($element instanceof TextRun) {
                foreach ($element->getElements() as $subElement) {
                    if ($subElement instanceof Text) {
                        $rawText .= $subElement->getText() . "\n";
                    }
                }
            }
        }
    }
}

// 📃 TXT Handling
elseif ($fileExt === "txt") {
    $rawText = file_get_contents($filePath);
}

// 📑 PDF Handling — Use separate Python extractor
elseif ($fileExt === "pdf") {
    $pdfTextScript = realpath("../main/extract_pdf_text.py");

    if (!$pdfTextScript || !file_exists($pdfTextScript)) {
        die("❌ PDF extractor script not found.");
    }

    $command = "python " . escapeshellarg($pdfTextScript) . " " . escapeshellarg($filePath);
    $rawText = shell_exec($command);

    if (empty($rawText)) {
        die("❌ Failed to extract text from PDF.");
    }
}

// ❌ Unsupported file type
else {
    die("❌ Unsupported file type: ." . htmlspecialchars($fileExt));
}

// ✅ Compose the AI prompt
$prompt = "Summarize this document:\n" . $rawText;

// 🧠 Call AI script
$aiScript = realpath("../main/ai_script.py");

if (!$aiScript || !file_exists($aiScript)) {
    die("❌ AI script not found.");
}

$command = "python " . escapeshellarg($aiScript) . " " . escapeshellarg($prompt);
$output = shell_exec($command);

// ✅ Display AI summary
echo "<h3>🧠 AI Summary:</h3>";
echo "<pre style='white-space: pre-wrap; word-break: break-word;'>" . htmlspecialchars($output) . "</pre>";
