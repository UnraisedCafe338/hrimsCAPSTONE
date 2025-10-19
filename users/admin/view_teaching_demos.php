<?php
include('../../handlers/connection.php');
$collection = $database->selectCollection("teaching_demos");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $demoId = $_POST['demo_id'];
        
        if ($action === 'edit') {
            // Update demo details
            $updateData = [
                'applicant_name' => $_POST['applicant_name'],
                'demo_date' => $_POST['demo_date'],
                'demo_time' => $_POST['demo_time'],
                'duration' => (int)$_POST['duration'],
                'room' => $_POST['room'],
                'topic' => $_POST['topic'],
                'area_of_specialization' => $_POST['area_of_specialization'],
                'license' => $_POST['license']
            ];
            
            $collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectID($demoId)],
                ['$set' => $updateData]
            );
            
            $message = "Teaching demo updated successfully!";
        } elseif ($action === 'delete') {
            // Delete demo
            $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($demoId)]);
            $message = "Teaching demo deleted successfully!";
        }
    }
}

// Get demos based on status
$pendingDemos = $collection->find(['status' => 'scheduled'], ['sort' => ['demo_date' => 1, 'demo_time' => 1]]);
$evaluatedDemos = $collection->find(['status' => 'evaluated'], ['sort' => ['demo_date' => 1, 'demo_time' => 1]]);

$pendingDemosList = [];
foreach ($pendingDemos as $demo) {
    // Handle date properly - it might be stored as a string or MongoDB date object
    $demoDate = $demo->demo_date;
    if (is_object($demoDate) && method_exists($demoDate, 'toDateTime')) {
        // It's a MongoDB date object
        $formattedDate = $demoDate->toDateTime()->format('Y-m-d');
    } else {
        // It's likely a string
        $formattedDate = date('Y-m-d', strtotime($demoDate));
    }
    
    $pendingDemosList[] = [
        'id' => (string)$demo->_id,
        'applicant_name' => $demo->applicant_name,
        'demo_date' => $formattedDate,
        'demo_time' => $demo->demo_time,
        'duration' => (int)$demo->duration,
        'room' => $demo->room,
        'topic' => $demo->topic,
        'area_of_specialization' => $demo->area_of_specialization ?? '',
        'license' => $demo->license ?? ''
    ];
}

// Process evaluated demos
$evaluatedDemosList = [];
foreach ($evaluatedDemos as $demo) {
    // Handle date properly - it might be stored as a string or MongoDB date object
    $demoDate = $demo->demo_date;
    if (is_object($demoDate) && method_exists($demoDate, 'toDateTime')) {
        // It's a MongoDB date object
        $formattedDate = $demoDate->toDateTime()->format('Y-m-d');
    } else {
        // It's likely a string
        $formattedDate = date('Y-m-d', strtotime($demoDate));
    }
    
    $evaluatedDemosList[] = [
        'id' => (string)$demo->_id,
        'applicant_name' => $demo->applicant_name,
        'demo_date' => $formattedDate,
        'demo_time' => $demo->demo_time,
        'duration' => (int)$demo->duration,
        'room' => $demo->room,
        'topic' => $demo->topic,
        'area_of_specialization' => $demo->area_of_specialization ?? '',
        'license' => $demo->license ?? '',
        'overall_rating' => isset($demo->evaluation) ? $demo->evaluation->overall_rating : 'N/A'
    ];
}

// Debug: Check how many evaluated demos we have
// error_log("Evaluated demos count: " . count($evaluatedDemosList));
// error_log("Evaluated demos: " . print_r($evaluatedDemosList, true));

// Check if we're viewing details for a specific demo
$viewDemoId = $_GET['view'] ?? null;
$viewDemo = null;
if ($viewDemoId) {
    $demo = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($viewDemoId)]);
    if ($demo) {
        // Handle date properly for viewing
        $demoDate = $demo->demo_date;
        if (is_object($demoDate) && method_exists($demoDate, 'toDateTime')) {
            // It's a MongoDB date object
            $formattedDate = $demoDate->toDateTime()->format('F j, Y');
        } else {
            // It's likely a string
            $formattedDate = date('F j, Y', strtotime($demoDate));
        }
        
        $viewDemo = [
            'id' => (string)$demo->_id,
            'applicant_name' => $demo->applicant_name,
            'demo_date' => $formattedDate,
            'demo_time' => $demo->demo_time,
            'duration' => (int)$demo->duration,
            'room' => $demo->room,
            'topic' => $demo->topic,
            'area_of_specialization' => $demo->area_of_specialization ?? '',
            'license' => $demo->license ?? '',
            'status' => $demo->status,
            'evaluation' => $demo->evaluation ?? null
        ];
    }
}

// Determine which tab to show by default
$activeTab = $_GET['tab'] ?? 'pending';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scheduled Teaching Demos</title>
  <link rel="stylesheet" href="/hrims/css/styles.css?v=1.1">
  <style>
    .teaching-demos-button {
      background-color: #00124d;
      border-left: 4px solid #ffffff;
    }
    
    .box-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .box-header h2 {
      margin: 0;
      color: #00124d;
    }
    
    .back-link {
      display: inline-block;
      margin-bottom: 15px;
      color: #003366;
      text-decoration: none;
      font-weight: 500;
      padding: 8px 15px;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .back-link:hover {
      background: #f0f4ff;
    }
    
    .tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 1px solid #ddd;
    }
    
    .tab {
      padding: 10px 20px;
      cursor: pointer;
      background-color: #f1f1f1;
      border: 1px solid #ddd;
      border-bottom: none;
      border-radius: 5px 5px 0 0;
      margin-right: 5px;
    }
    
    .tab.active {
      background-color: #00124d;
      color: white;
    }
    
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
    }
    
    .demos-table {
      width: 100%;
      border-collapse: collapse;
    }

    .demos-table th {
      background-color: #00124d;
      color: #ffffff;
    }

    .demos-table tr:nth-child(even) {
      background-color: #f8f8f8;
    }

    .demos-table tr:hover {
      background-color: #f1f1f1;
    }
    
    .no-demos {
      text-align: center;
      padding: 40px;
      color: #666;
    }
    
    .actions {
      display: flex;
      gap: 10px;
    }
    
    .btn {
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
      cursor: pointer;
      border: none;
    }

    .btn-view {
      background: #003366;
      color: white;
    }

    .btn-edit {
      background: #ffdd00;
      color: #001a66;
    }

    .btn-cancel {
      background: #e74c3c;
      color: white;
    }
    
    .btn-start {
      background: #27ae60;
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px;
      border-radius: 8px;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }
    
    .form-actions {
      text-align: right;
      margin-top: 20px;
    }
    
    .btn-primary {
      background-color: #003366;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .btn-secondary {
      background-color: #6c757d;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 10px;
    }
    
    .demo-details {
      background: #f8f9ff;
      border-radius: 10px;
      padding: 25px;
      margin-bottom: 30px;
      border-left: 4px solid #003366;
    }
    
    .detail-row {
      display: flex;
      margin-bottom: 15px;
    }
    
    .detail-label {
      font-weight: bold;
      width: 200px;
      color: #003366;
    }
    
    .detail-value {
      flex: 1;
    }
    
    .evaluation-section {
      background: #e8f4f8;
      border-radius: 10px;
      padding: 20px;
      margin-top: 20px;
    }
    
    .evaluation-table {
      width: 100%;
      border-collapse: collapse;
      margin: 15px 0;
    }
    
    .evaluation-table th, .evaluation-table td {
      border: 1px solid #000;
      padding: 8px;
      text-align: center;
    }
    
    .evaluation-table th {
      background-color: #00124d;
      color: white;
    }
    
    .evaluation-table td:first-child {
      text-align: left;
    }
    
    .recommendation-section {
      margin: 20px 0;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background: #fff;
    }
    
    @media (max-width: 768px) {
      .demos-table {
        display: block;
        overflow-x: auto;
      }
      
      .box-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
      }
      
      .detail-row {
        flex-direction: column;
      }
      
      .detail-label {
        width: 100%;
        margin-bottom: 5px;
      }
      
      .tabs {
        flex-direction: column;
      }
      
      .tab {
        margin-bottom: 5px;
        border-radius: 5px;
      }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  
  <div class="header">Teaching Demos</div>
  
  <div class="content">
    <?php if (isset($message)): ?>
      <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
    
    <?php if ($viewDemoId && $viewDemo): ?>
      <!-- View Demo Details -->
      <div class="box-header">
        <h2>Demo Details: <?php echo htmlspecialchars($viewDemo['applicant_name']); ?></h2>
        <a href="view_teaching_demos.php?tab=<?php echo $activeTab; ?>" class="back-link">← Back to List</a>
      </div>
      
      <div class="box-body">
        <div class="demo-details">
          <div class="detail-row">
            <div class="detail-label">Applicant Name:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['applicant_name']); ?></div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Demo Date:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['demo_date']); ?></div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Demo Time:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['demo_time']); ?></div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Duration:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['duration']); ?> minutes</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Room:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['room']); ?></div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Demo Topic:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['topic']); ?></div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Area of Specialization:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['area_of_specialization']); ?></div>
          </div>
          <div class="detail-row">
            <div class="detail-label">License:</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewDemo['license']); ?></div>
          </div>
        </div>
        
        <?php if ($viewDemo['status'] === 'evaluated' && $viewDemo['evaluation']): ?>
          <!-- Show evaluation details for evaluated demos -->
          <div class="evaluation-section">
            <h3>Evaluation Results</h3>
            
            <table class="evaluation-table">
              <thead>
                <tr>
                  <th>PERSONAL TRAITS</th>
                  <th>RATING/SCORE</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1. PRESENTATION</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->presentation ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>2. PERSONALITY</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->personality ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>3. QUALITY OF VOICE</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->voice_quality ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>4. TECHNICAL KNOWLEDGE</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->technical_knowledge ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>5. RESOURCEFULNESS</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->resourcefulness ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>6. CLASS MANAGEMENT</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->class_management ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>7. TEACHING ABILITY</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->teaching_ability ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>8. COMMUNICATION SKILLS</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->communication_skills ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>9. TIME MANAGEMENT</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->time_management ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td>10. HUMAN RELATION</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->human_relation ?? 'N/A'); ?></td>
                </tr>
                <tr>
                  <td style="text-align: right; font-weight: bold;">OVER ALL RATING</td>
                  <td><?php echo htmlspecialchars($viewDemo['evaluation']->overall_rating ?? 'N/A'); ?></td>
                </tr>
              </tbody>
            </table>
            
            <div class="recommendation-section">
              <strong>RECOMMENDABLE:</strong> 
              <?php 
              $recommendation = $viewDemo['evaluation']->recommendation ?? '';
              echo htmlspecialchars(strtoupper($recommendation)); 
              ?>
            </div>
            
            <?php if (isset($viewDemo['evaluation']->evaluated_at)): ?>
              <p><strong>Evaluated At:</strong> 
              <?php 
              if (is_object($viewDemo['evaluation']->evaluated_at) && method_exists($viewDemo['evaluation']->evaluated_at, 'toDateTime')) {
                echo htmlspecialchars($viewDemo['evaluation']->evaluated_at->toDateTime()->format('F j, Y H:i'));
              } else {
                echo htmlspecialchars(date('F j, Y H:i', strtotime($viewDemo['evaluation']->evaluated_at)));
              }
              ?>
              </p>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <!-- Show action buttons for pending demos -->
          <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-edit" onclick="openEditModal('<?php echo $viewDemo['id']; ?>', '<?php echo htmlspecialchars($viewDemo['applicant_name']); ?>', '<?php echo htmlspecialchars($viewDemo['demo_date']); ?>', '<?php echo htmlspecialchars($viewDemo['demo_time']); ?>', '<?php echo htmlspecialchars($viewDemo['duration']); ?>', '<?php echo htmlspecialchars($viewDemo['room']); ?>', '<?php echo htmlspecialchars($viewDemo['topic']); ?>', '<?php echo htmlspecialchars($viewDemo['area_of_specialization']); ?>', '<?php echo htmlspecialchars($viewDemo['license']); ?>')">Edit Demo</button>
            <button class="btn btn-start" onclick="startDemo('<?php echo $viewDemo['id']; ?>')">Start Demo</button>
            <button class="btn btn-cancel" onclick="deleteDemo('<?php echo $viewDemo['id']; ?>')">Cancel Demo</button>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <!-- List All Demos with Tabs -->
      <div class="box-header">
        <h2>Teaching Demo Schedule</h2>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
      </div>
      
      <div class="box-body">
        <!-- Tabs -->
        <div class="tabs">
          <div class="tab <?php echo $activeTab === 'pending' ? 'active' : ''; ?>" onclick="switchTab('pending')">Pending Demos</div>
          <div class="tab <?php echo $activeTab === 'evaluated' ? 'active' : ''; ?>" onclick="switchTab('evaluated')">Evaluated Demos</div>
        </div>
        
        <!-- Pending Demos Tab -->
        <div id="pending" class="tab-content <?php echo $activeTab === 'pending' ? 'active' : ''; ?>">
          <?php if (count($pendingDemosList) > 0): ?>
            <table class="demos-table">
              <thead>
                <tr>
                  <th>Applicant</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Duration</th>
                  <th>Room</th>
                  <th>Topic</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingDemosList as $demo): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($demo['applicant_name']); ?></td>
                    <td><?php echo htmlspecialchars($demo['demo_date']); ?></td>
                    <td><?php echo htmlspecialchars($demo['demo_time']); ?></td>
                    <td><?php echo htmlspecialchars($demo['duration']); ?> mins</td>
                    <td><?php echo htmlspecialchars($demo['room']); ?></td>
                    <td><?php echo htmlspecialchars($demo['topic']); ?></td>
                    <td class="actions">
                      <button class="btn btn-view" onclick="viewDemo('<?php echo $demo['id']; ?>', 'pending')">View</button>
                      <button class="btn btn-edit" onclick="openEditModal('<?php echo $demo['id']; ?>', '<?php echo htmlspecialchars($demo['applicant_name']); ?>', '<?php echo htmlspecialchars($demo['demo_date']); ?>', '<?php echo htmlspecialchars($demo['demo_time']); ?>', '<?php echo htmlspecialchars($demo['duration']); ?>', '<?php echo htmlspecialchars($demo['room']); ?>', '<?php echo htmlspecialchars($demo['topic']); ?>', '<?php echo htmlspecialchars($demo['area_of_specialization']); ?>', '<?php echo htmlspecialchars($demo['license']); ?>')">Edit</button>
                      <button class="btn btn-start" onclick="startDemo('<?php echo $demo['id']; ?>')">Start Demo</button>
                      <button class="btn btn-cancel" onclick="deleteDemo('<?php echo $demo['id']; ?>')">Cancel</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="no-demos">
              <h3>No pending teaching demos</h3>
              <p>There are currently no pending teaching demos scheduled.</p>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Evaluated Demos Tab -->
        <div id="evaluated" class="tab-content <?php echo $activeTab === 'evaluated' ? 'active' : ''; ?>">
          <?php if (count($evaluatedDemosList) > 0): ?>
            <table class="demos-table">
              <thead>
                <tr>
                  <th>Applicant</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Duration</th>
                  <th>Room</th>
                  <th>Topic</th>
                  <th>Overall Rating</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($evaluatedDemosList as $demo): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($demo['applicant_name']); ?></td>
                    <td><?php echo htmlspecialchars($demo['demo_date']); ?></td>
                    <td><?php echo htmlspecialchars($demo['demo_time']); ?></td>
                    <td><?php echo htmlspecialchars($demo['duration']); ?> mins</td>
                    <td><?php echo htmlspecialchars($demo['room']); ?></td>
                    <td><?php echo htmlspecialchars($demo['topic']); ?></td>
                    <td><?php echo htmlspecialchars($demo['overall_rating']); ?></td>
                    <td class="actions">
                      <button class="btn btn-view" onclick="viewDemo('<?php echo $demo['id']; ?>', 'evaluated')">View</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="no-demos">
              <h3>No evaluated teaching demos</h3>
              <p>There are currently no evaluated teaching demos.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Edit Teaching Demo</h2>
      <form id="editForm" method="POST">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="demo_id" id="editDemoId">
        
        <div class="form-group">
          <label for="applicant_name">Applicant Name:</label>
          <input type="text" id="applicant_name" name="applicant_name" required>
        </div>
        
        <div class="form-group">
          <label for="demo_date">Demo Date:</label>
          <input type="date" id="demo_date" name="demo_date" required>
        </div>
        
        <div class="form-group">
          <label for="demo_time">Demo Time:</label>
          <input type="time" id="demo_time" name="demo_time" required>
        </div>
        
        <div class="form-group">
          <label for="duration">Duration (minutes):</label>
          <select id="duration" name="duration" required>
            <option value="30">30 minutes</option>
            <option value="45">45 minutes</option>
            <option value="60">60 minutes</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="room">Room:</label>
          <input type="text" id="room" name="room" required>
        </div>
        
        <div class="form-group">
          <label for="topic">Topic:</label>
          <input type="text" id="topic" name="topic" required>
        </div>
        
        <div class="form-group">
          <label for="area_of_specialization">Area of Specialization:</label>
          <input type="text" id="area_of_specialization" name="area_of_specialization" required>
        </div>
        
        <div class="form-group">
          <label for="license">License:</label>
          <input type="text" id="license" name="license">
        </div>
        
        <div class="form-actions">
          <button type="button" class="btn-secondary" id="cancelEdit">Cancel</button>
          <button type="submit" class="btn-primary">Update Demo</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Get modal element
    var modal = document.getElementById("editModal");
    var span = document.getElementsByClassName("close")[0];
    var cancelBtn = document.getElementById("cancelEdit");
    
    // Function to switch tabs
    function switchTab(tabName) {
      // Hide all tab content
      var tabContents = document.getElementsByClassName("tab-content");
      for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
      }
      
      // Remove active class from all tabs
      var tabs = document.getElementsByClassName("tab");
      for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove("active");
      }
      
      // Show the selected tab content
      var selectedTab = document.getElementById(tabName);
      if (selectedTab) {
        selectedTab.classList.add("active");
      }
      
      // Update URL to reflect the active tab
      var url = new URL(window.location);
      url.searchParams.set('tab', tabName);
      window.history.replaceState({}, '', url);
    }
    
    // Set the active tab on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Get the tab from URL parameter or default to 'pending'
      const urlParams = new URLSearchParams(window.location.search);
      const tab = urlParams.get('tab') || 'pending';
      
      // Activate the appropriate tab
      switchTab(tab);
    });
    
    // Function to open edit modal
    function openEditModal(id, applicantName, demoDate, demoTime, duration, room, topic, areaOfSpecialization, license) {
      document.getElementById("editDemoId").value = id;
      document.getElementById("applicant_name").value = applicantName;
      document.getElementById("demo_date").value = formatDateForInput(demoDate);
      document.getElementById("demo_time").value = demoTime;
      
      // Set the duration select value
      var durationSelect = document.getElementById("duration");
      durationSelect.value = duration;
      
      document.getElementById("room").value = room;
      document.getElementById("topic").value = topic;
      document.getElementById("area_of_specialization").value = areaOfSpecialization;
      document.getElementById("license").value = license;
      
      modal.style.display = "block";
    }
    
    // Function to format date for input field
    function formatDateForInput(dateString) {
      // Convert date string like "October 15, 2025" to "2025-10-15"
      const months = {
        "January": "01", "February": "02", "March": "03", "April": "04",
        "May": "05", "June": "06", "July": "07", "August": "08",
        "September": "09", "October": "10", "November": "11", "December": "12"
      };
      
      const parts = dateString.split(" ");
      if (parts.length === 3) {
        const month = months[parts[0]];
        const day = parts[1].replace(",", "").padStart(2, '0');
        const year = parts[2];
        return `${year}-${month}-${day}`;
      }
      return dateString;
    }
    
    // Close modal when clicking on X
    span.onclick = function() {
      modal.style.display = "none";
    }
    
    // Close modal when clicking on cancel button
    cancelBtn.onclick = function() {
      modal.style.display = "none";
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
    
    // View demo function
    function viewDemo(demoId, tab) {
      // Preserve the current tab in the URL
      window.location.href = 'view_teaching_demos.php?view=' + demoId + '&tab=' + tab;
    }
    
    // Start demo function
    function startDemo(demoId) {
      window.location.href = 'start_teaching_demo.php?id=' + demoId;
    }
    
    // Delete demo function
    function deleteDemo(demoId) {
      if (confirm('Are you sure you want to cancel this teaching demo?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        form.appendChild(actionInput);
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'demo_id';
        idInput.value = demoId;
        form.appendChild(idInput);
        
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
</body>
</html>