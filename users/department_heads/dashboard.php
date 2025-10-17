<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
include('../../handlers/connection.php');

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'department_head') {
    header("Location: ../employees/login.php");
    exit();
}

// Get user information
$username = $_SESSION['username'];
$department = $_SESSION['department'] ?? 'Not Assigned';

// Fetch the user's can_evaluate field
$user = $usersCollection->findOne(['email' => $username]);
$canEvaluate = $user['can_evaluate'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard | HRIMS</title>
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
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status.active {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status.pending {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .program-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }
        
        .program-card h4 {
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .program-card p {
            color: #666;
            margin: 5px 0;
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
                <p>Department Head Portal</p>
            </div>
        </div>
        <div class="user-info">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
            <p>Department: <?php echo htmlspecialchars($department); ?></p>
            <a href="../employees/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        
        <div class="content">
            <div class="dashboard-header">
                <h2>Department Head Dashboard</h2>
                <p>Manage your department operations</p>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Faculty Members</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Evaluations Due</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-info">
                        <h3>0</h3>
                        <p>Pending Reports</p>
                    </div>
                </div>
            </div>
            
            <div class="evaluation-section">
                <div class="section-header">
                    <h3>Department Overview</h3>
                    <p>Key metrics and information</p>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Faculty</td>
                                <td>0</td>
                                <td><span class="status pending">Pending</span></td>
                            </tr>
                            <tr>
                                <td>Evaluations Completed</td>
                                <td>0</td>
                                <td><span class="status pending">Pending</span></td>
                            </tr>
                            <tr>
                                <td>Department Budget</td>
                                <td>₱0.00</td>
                                <td><span class="status pending">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="evaluation-section">
                <div class="section-header">
                    <h3>Programs You Can Evaluate</h3>
                    <p>Departments and programs under your evaluation authority</p>
                </div>
                
                <?php if (!empty($canEvaluate)): ?>
                    <?php 
                    $programDescriptions = [
                        'BSIS' => 'Bachelor of Science in Information Systems',
                        'BSME' => 'Bachelor of Science in Mechanical Engineering',
                        'BSTM' => 'Bachelor of Science in Tourism Management',
                        'BSN' => 'Bachelor of Science in Nursing'
                    ];
                    ?>
                    <div class="programs-grid">
                        <?php foreach ($canEvaluate as $program): ?>
                            <div class="program-card">
                                <h4><?php echo htmlspecialchars($program); ?></h4>
                                <p><?php echo htmlspecialchars($programDescriptions[$program] ?? 'Program description not available'); ?></p>
                                <p><span class="status active">Active</span></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No programs assigned for evaluation.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2025 Human Resources Information Management System. All rights reserved.</p>
    </div>
</body>
</html>