<?php
require '../vendor/autoload.php';
require '../connection.php';

$collection = $database->selectCollection('applicants');
$applicantId = $_GET['id'] ?? null;

$applicant = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($applicantId)]);
$documents = $applicant['documents'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Files</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 20px;
        }
        .file-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .file-section {
            margin-bottom: 25px;
        }
        .file-section h4 {
            margin-bottom: 10px;
            color: #444;
        }
        .file-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .file-links a {
            padding: 8px 14px;
            background-color: #0066cc;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .file-links a:hover {
            background-color: #004d99;
        }
    </style>
</head>
<body>

<div class="file-container">
    <h2>Uploaded Files</h2>

    <?php foreach ($documents as $type => $fileArray): ?>
        <div class="file-section">
            <h4><?= ucfirst(str_replace("_", " ", $type)) ?></h4>
            <div class="file-links">
                <?php foreach ($fileArray as $index => $fileId): ?>
                    <a href="view_file.php?id=<?= $fileId ?>" target="_blank">View File <?= $index + 1 ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
