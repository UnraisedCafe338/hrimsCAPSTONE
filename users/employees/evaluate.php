<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include('../../handlers/connection.php');

// Check if user is logged in and is an employee
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

// Get user information
$username = $_SESSION['username'];
$department = $_SESSION['department'] ?? 'Not Assigned';

// Get teacher ID from URL parameter
$teacher_id = $_GET['id'] ?? null;

// Fetch teacher details
$teacher = null;
if ($teacher_id) {
    $teacher = $database->selectCollection("employees")->findOne([
        '_id' => new MongoDB\BSON\ObjectID($teacher_id),
        'department' => $department
    ]);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $teacher) {
    $evaluation_data = [
        'evaluator_id' => $_SESSION['user_id'],
        'evaluator_name' => $username,
        'teacher_id' => $teacher_id,
        'teacher_name' => $teacher['first_name'] . ' ' . $teacher['last_name'],
        'department' => $department,
        'evaluation_date' => date('Y-m-d H:i:s'),
        'criteria' => [
            'teaching_effectiveness' => (int)$_POST['teaching_effectiveness'],
            'classroom_management' => (int)$_POST['classroom_management'],
            'communication_skills' => (int)$_POST['communication_skills'],
            'professional_development' => (int)$_POST['professional_development'],
            'student_engagement' => (int)$_POST['student_engagement']
        ],
        'comments' => $_POST['comments'],
        'overall_score' => (
            (int)$_POST['teaching_effectiveness'] +
            (int)$_POST['classroom_management'] +
            (int)$_POST['communication_skills'] +
            (int)$_POST['professional_development'] +
            (int)$_POST['student_engagement']
        ) / 5
    ];
    
    // Save evaluation to database
    $database->selectCollection("evaluations")->insertOne($evaluation_data);
    
    // Redirect back to dashboard with success message
    header("Location: dashboard.php?evaluation_submitted=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluate Teacher | HRIMS</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <style>
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #2a4b8d;
            --accent-color: #ffdd00;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --light-text: #fff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            color: var(--dark-text);
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--light-text);
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
        }
        
        .header-content h1 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .user-info h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        
        .main-content {
            display: flex;
            min-height: calc(100vh - 80px);
        }
        
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            padding: 20px 0;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            padding: 15px 25px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu li:hover, .sidebar-menu li.active {
            background: rgba(26, 42, 108, 0.05);
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar-menu li a {
            text-decoration: none;
            color: var(--dark-text);
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-menu li a i {
            width: 20px;
            color: var(--primary-color);
        }
        
        .content {
            flex: 1;
            padding: 30px;
        }
        
        .breadcrumb {
            margin-bottom: 20px;
        }
        
        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .breadcrumb span {
            color: #666;
        }
        
        .evaluation-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .form-header h2 {
            color: var(--primary-color);
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .teacher-info {
            background: rgba(26, 42, 108, 0.05);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .teacher-info h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-item label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        
        .info-item p {
            margin: 0;
            padding: 8px 12px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .evaluation-criteria {
            margin-bottom: 30px;
        }
        
        .evaluation-criteria h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .criteria-item {
            margin-bottom: 25px;
        }
        
        .criteria-item label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #444;
        }
        
        .rating-scale {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .rating-option {
            text-align: center;
            flex: 1;
        }
        
        .rating-option input {
            margin-bottom: 5px;
        }
        
        .rating-label {
            font-size: 12px;
            color: #666;
        }
        
        .comments-section {
            margin-bottom: 30px;
        }
        
        .comments-section label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #444;
        }
        
        .comments-section textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline:hover {
            background: rgba(26, 42, 108, 0.05);
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="../../images/exact logo.png" alt="Logo" class="logo">
            <div class="header-content">
                <h1>Human Resources Information Management System</h1>
                <p>Employee Evaluation Portal</p>
            </div>
        </div>
        <div class="user-info">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
            <p>Department: <?php echo htmlspecialchars($department); ?></p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="active">
                    <a href="evaluate.php"><i class="fas fa-clipboard-check"></i> Evaluate Teachers</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-chart-bar"></i> Performance Reports</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-cog"></i> Settings</a>
                </li>
            </ul>
        </div>
        
        <div class="content">
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a> <span>/ Evaluate Teacher</span>
            </div>
            
            <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            
            <div class="evaluation-form">
                <div class="form-header">
                    <h2>Teacher Performance Evaluation</h2>
                    <p>Please evaluate the teacher based on the following criteria</p>
                </div>
                
                <?php if ($teacher): ?>
                    <div class="teacher-info">
                        <h3>Teacher Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Name</label>
                                <p><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                            </div>
                            <div class="info-item">
                                <label>Employee ID</label>
                                <p><?php echo htmlspecialchars($teacher['employee_id'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="info-item">
                                <label>Position</label>
                                <p><?php echo htmlspecialchars($teacher['position'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="info-item">
                                <label>Department</label>
                                <p><?php echo htmlspecialchars($teacher['department'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <div class="evaluation-criteria">
                            <h3>Evaluation Criteria</h3>
                            
                            <div class="criteria-item">
                                <label>1. Teaching Effectiveness (20%)</label>
                                <div class="rating-scale">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" id="teaching_<?php echo $i; ?>" name="teaching_effectiveness" value="<?php echo $i; ?>" required>
                                        <label for="teaching_<?php echo $i; ?>"><?php echo $i; ?></label>
                                        <div class="rating-label"><?php echo ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$i-1]; ?></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="criteria-item">
                                <label>2. Classroom Management (20%)</label>
                                <div class="rating-scale">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" id="classroom_<?php echo $i; ?>" name="classroom_management" value="<?php echo $i; ?>" required>
                                        <label for="classroom_<?php echo $i; ?>"><?php echo $i; ?></label>
                                        <div class="rating-label"><?php echo ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$i-1]; ?></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="criteria-item">
                                <label>3. Communication Skills (20%)</label>
                                <div class="rating-scale">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" id="communication_<?php echo $i; ?>" name="communication_skills" value="<?php echo $i; ?>" required>
                                        <label for="communication_<?php echo $i; ?>"><?php echo $i; ?></label>
                                        <div class="rating-label"><?php echo ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$i-1]; ?></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="criteria-item">
                                <label>4. Professional Development (20%)</label>
                                <div class="rating-scale">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" id="professional_<?php echo $i; ?>" name="professional_development" value="<?php echo $i; ?>" required>
                                        <label for="professional_<?php echo $i; ?>"><?php echo $i; ?></label>
                                        <div class="rating-label"><?php echo ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$i-1]; ?></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="criteria-item">
                                <label>5. Student Engagement (20%)</label>
                                <div class="rating-scale">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="rating-option">
                                        <input type="radio" id="engagement_<?php echo $i; ?>" name="student_engagement" value="<?php echo $i; ?>" required>
                                        <label for="engagement_<?php echo $i; ?>"><?php echo $i; ?></label>
                                        <div class="rating-label"><?php echo ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$i-1]; ?></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="comments-section">
                            <label for="comments">Additional Comments</label>
                            <textarea id="comments" name="comments" placeholder="Please provide any additional comments or feedback..."></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Submit Evaluation</button>
                            <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <h3>Teacher not found or access denied</h3>
                        <p>The requested teacher could not be found or is not in your department.</p>
                        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2025 Human Resources Information Management System. All rights reserved.</p>
    </div>
</body>
</html>