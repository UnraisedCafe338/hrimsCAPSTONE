<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include('../../handlers/connection.php');

// Check if user is logged in and has an employee role
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['employee', 'department_head', 'faculty', 'staff', 'applicant'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$username = $_SESSION['username'];
$department = $_SESSION['department'] ?? 'Not Assigned';

// Fetch teachers in the same department for evaluation
$teachers = [];
if (!empty($department)) {
    $teachers = $database->selectCollection("employees")->find([
        "department" => $department,
        "position" => ['$ne' => "Dean"] // Exclude deans from the list
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard | HRIMS</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <link rel="stylesheet" href="../../css/styles.css">
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
        
        .content {
            flex: 1;
            padding: 30px;
            margin-left: 247px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .dashboard-header h2 {
            color: var(--primary-color);
            font-size: 28px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stat-icon.blue {
            background: rgba(26, 42, 108, 0.1);
            color: var(--primary-color);
        }
        
        .stat-icon.green {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .stat-icon.orange {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: #666;
            margin: 0;
        }
        
        .evaluation-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-header h3 {
            color: var(--primary-color);
            font-size: 22px;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: rgba(26, 42, 108, 0.05);
            color: var(--primary-color);
            font-weight: 600;
        }
        
        tr:hover {
            background: rgba(26, 42, 108, 0.02);
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
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
            margin-left: 248px;
            width: calc(100% - 250px);
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
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
        
        <div class="content">
            <div class="dashboard-header">
                <h2>Dean Evaluation Dashboard</h2>
                <p>Review and evaluate teaching staff in your department</p>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $teachers->count(); ?></h3>
                        <p>Teachers to Evaluate</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Evaluations Completed</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Pending Evaluations</p>
                    </div>
                </div>
            </div>
            
            <div class="evaluation-section">
                <div class="section-header">
                    <h3>Teachers in Your Department</h3>
                    <p>Evaluate teaching staff performance</p>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($teachers->count() > 0): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($teacher['employee_id'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['position'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['department'] ?? 'N/A'); ?></td>
                                    <td><span class="status pending">Pending</span></td>
                                    <td>
                                        <a href="evaluate.php?id=<?php echo $teacher['_id']; ?>" class="btn btn-primary">Evaluate</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No teachers found in your department</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2025 Human Resources Information Management System. All rights reserved.</p>
    </div>
</body>
</html>