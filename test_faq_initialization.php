<?php
// test_faq_initialization.php - Test page to initialize FAQs
?>
<!DOCTYPE html>
<html>
<head>
    <title>FAQ Initialization Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>FAQ Initialization Test</h1>
        <p>This page will show the sample FAQs that can be added to your MongoDB database.</p>
        
        <?php
        include 'initialize_faqs.php';
        ?>
    </div>
</body>
</html>