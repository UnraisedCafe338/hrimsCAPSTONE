<?php
include('../../handlers/connection.php');
$collection = $database->selectCollection("applicants");

// Get ID from the URL
$id = $_GET['id'] ?? null;

if ($id) {
  try {
    $applicant = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($id)]);
  } catch (Exception $e) {
    die("Invalid ID format.");
  }

  if (!$applicant) {
    die("Applicant not found.");
  }

  // Extract the needed data
  $prefixName = $applicant['personal_info']['prefix_name'] ?? '';
  $firstName = $applicant['personal_info']['first_name'] ?? '';
  $middleName = $applicant['personal_info']['middle_name'] ?? '';
  $lastName = $applicant['personal_info']['last_name'] ?? '';
  $suffixName = $applicant['personal_info']['suffix_name'] ?? '';
  $fullName = "$prefixName $firstName $middleName $lastName $suffixName";
  $positionApplied = $applicant['position_applied'] ?? '';
  $email = $applicant['personal_info']['email'] ?? '';
  $contactNo = $applicant['personal_info']['contact_no'] ?? '';
  
  // Education information
  $college = $applicant['education']['college']['school'] ?? '';
  $degree = $applicant['education']['college']['degree'] ?? '';
  
  // Skills
  $skills = $applicant['skills'] ?? '';
  
  // Area of Specialization (from degree)
  $areaOfSpecialization = $degree;
  
  // License information
  $license = $applicant['professional_license'] ?? '';
  
  // Generate a temporary Form ID for display (will be finalized when saved)
  $tempFormId = "TIOOI-NE2M3-" . strtoupper(substr($id, 0, 8));
} else {
  die("No applicant ID provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teaching Demo Schedule - <?php echo htmlspecialchars($fullName); ?></title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      margin: 0;
      padding: 20px;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
      overflow: hidden;
    }

    .header {
      background: linear-gradient(90deg, #001a66 0%, #00124d 100%);
      color: white;
      padding: 25px 30px;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
      font-weight: 600;
    }

    .header p {
      margin: 8px 0 0;
      opacity: 0.9;
      font-size: 16px;
    }

    .notification-container {
      position: relative;
      cursor: pointer;
    }

    .notification-bell {
      font-size: 24px;
      color: white;
      position: relative;
    }

    .notification-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: #ffdd00;
      color: #001a66;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 12px;
      font-weight: bold;
    }

    .notification-dropdown {
      position: absolute;
      top: 40px;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      width: 300px;
      z-index: 1000;
      display: none;
    }

    .notification-dropdown.show {
      display: block;
    }

    .notification-header {
      padding: 15px;
      border-bottom: 1px solid #eee;
      font-weight: 600;
      color: #003366;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .notification-list {
      max-height: 300px;
      overflow-y: auto;
    }

    .notification-item {
      padding: 15px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
    }

    .notification-item:hover {
      background-color: #f8f9ff;
    }

    .notification-item.unread {
      background-color: #eef5ff;
    }

    .notification-title {
      font-weight: 600;
      color: #003366;
      margin-bottom: 5px;
    }

    .notification-time {
      font-size: 12px;
      color: #666;
    }

    .notification-message {
      font-size: 14px;
      color: #444;
      margin: 5px 0;
    }

    .mark-read {
      font-size: 12px;
      color: #003366;
      text-decoration: underline;
    }
    
    .content {
      padding: 30px;
    }

    .applicant-info {
      background: #f8f9ff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      border-left: 4px solid #003366;
    }

    .applicant-info h2 {
      margin-top: 0;
      color: #003366;
      border-bottom: 2px solid #eaeef5;
      padding-bottom: 10px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }

    .info-item {
      margin-bottom: 12px;
    }

    .info-label {
      font-weight: 600;
      color: #003366;
      display: block;
      margin-bottom: 4px;
      font-size: 14px;
    }

    .info-value {
      font-size: 16px;
      padding: 8px 12px;
      background: white;
      border-radius: 6px;
      border: 1px solid #e1e5f0;
    }

    .form-section {
      margin-bottom: 30px;
    }

    .form-section h2 {
      color: #003366;
      border-bottom: 2px solid #eaeef5;
      padding-bottom: 10px;
      margin-top: 0;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #444;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #d1d8e0;
      border-radius: 6px;
      font-size: 15px;
      transition: border-color 0.3s;
      box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #003366;
      box-shadow: 0 0 0 2px rgba(0, 51, 102, 0.1);
    }

    .full-width {
      grid-column: 1 / -1;
    }

    .btn-container {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #eaeef5;
    }

    .btn {
      background: linear-gradient(90deg, #003366 0%, #001a66 100%);
      color: white;
      border: none;
      padding: 14px 30px;
      font-size: 16px;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 51, 102, 0.2);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 51, 102, 0.3);
    }

    .btn:active {
      transform: translateY(0);
    }

    .back-link {
      display: inline-block;
      margin-bottom: 20px;
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

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .info-grid {
        grid-template-columns: 1fr;
      }
      
      .header {
        padding: 20px 15px;
      }
      
      .content {
        padding: 20px 15px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Teaching Demonstration Schedule</h1>
      <p>For Applicant: <?php echo htmlspecialchars($fullName); ?></p>
      <p style="font-size: 14px; margin-top: 10px;">Form ID: <?php echo htmlspecialchars($tempFormId); ?></p>
    </div>
    
    <div class="content">
      <a href="applicant_view.php?id=<?php echo htmlspecialchars($id); ?>" class="back-link">← Back to Applicant View</a>
      
      <div class="applicant-info">
        <h2>Applicant Information</h2>
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Full Name</span>
            <div class="info-value"><?php echo htmlspecialchars($fullName); ?></div>
          </div>
          <div class="info-item">
            <span class="info-label">Position Applied</span>
            <div class="info-value"><?php echo htmlspecialchars($positionApplied); ?></div>
          </div>
          <div class="info-item">
            <span class="info-label">Email</span>
            <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
          </div>
          <div class="info-item">
            <span class="info-label">Contact Number</span>
            <div class="info-value"><?php echo htmlspecialchars($contactNo); ?></div>
          </div>
          <div class="info-item">
            <span class="info-label">College</span>
            <div class="info-value"><?php echo htmlspecialchars($college); ?></div>
          </div>
          <div class="info-item">
            <span class="info-label">Degree</span>
            <div class="info-value"><?php echo htmlspecialchars($degree); ?></div>
          </div>
        </div>
      </div>
      
      <div class="form-section">
        <h2>Teaching Demo Details</h2>
        <form id="teachingDemoForm">
          <input type="hidden" name="applicant_id" value="<?php echo htmlspecialchars($id); ?>">
          <div class="form-grid">
            <div class="form-group">
              <label for="demo_date">Demo Date</label>
              <input type="date" id="demo_date" name="demo_date" required>
            </div>
            
            <div class="form-group">
              <label for="demo_time">Demo Time</label>
              <input type="time" id="demo_time" name="demo_time" required>
            </div>
            
            <div class="form-group">
              <label for="demo_duration">Duration (minutes)</label>
              <select id="demo_duration" name="demo_duration" required>
                <option value="">Select Duration</option>
                <option value="30">30 minutes</option>
                <option value="45">45 minutes</option>
                <option value="60">60 minutes</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="demo_room">Room/Venue</label>
              <input type="text" id="demo_room" name="demo_room" placeholder="e.g., Room 201, Building A" required>
            </div>
            
            <div class="form-group">
              <label for="area_of_specialization">Area of Specialization</label>
              <input type="text" id="area_of_specialization" name="area_of_specialization" value="<?php echo htmlspecialchars($areaOfSpecialization); ?>" placeholder="Enter area of specialization" required>
            </div>
            
            <div class="form-group">
              <label for="license">License</label>
              <input type="text" id="license" name="license" value="<?php echo htmlspecialchars($license); ?>" placeholder="Enter professional license (if any)">
            </div>
            
            <div class="form-group full-width">
              <label for="topic">Teaching Topic</label>
              <input type="text" id="topic" name="topic" placeholder="Enter the topic for the teaching demonstration" required>
            </div>
            
            <div class="form-group full-width">
              <label for="materials">Required Materials</label>
              <textarea id="materials" name="materials" rows="3" placeholder="List any materials needed for the demo (e.g., projector, whiteboard, markers)"></textarea>
            </div>
            
            <div class="form-group full-width">
              <label for="notes">Additional Notes</label>
              <textarea id="notes" name="notes" rows="4" placeholder="Any additional information or special instructions"></textarea>
            </div>
          </div>
          
          <div class="btn-container">
            <button type="submit" class="btn">Schedule Teaching Demo</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Pre-fill some fields based on applicant data
    document.addEventListener('DOMContentLoaded', function() {
      // Set a default topic based on the applicant's degree
      const degree = "<?php echo htmlspecialchars($degree); ?>";
      const position = "<?php echo htmlspecialchars($positionApplied); ?>";
      
      if (degree || position) {
        let topic = "Introduction to ";
        if (degree) {
          topic += degree.replace("Bachelor of", "").replace("Master of", "").trim();
        } else {
          topic += position;
        }
        document.getElementById('topic').value = topic;
      }
      
      // Set min date to today
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('demo_date').min = today;
      
      // Set default time
      document.getElementById('demo_time').value = "09:00";
      
      // Form submission handler
      document.getElementById('teachingDemoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const demoData = {};
        
        // Convert FormData to JSON object with proper field names
        for (let [key, value] of formData.entries()) {
          // Map field names to match what the server expects
          let newKey = key;
          if (key === 'demo_duration') {
            newKey = 'duration';  // Map demo_duration to duration
          } else if (key === 'demo_date') {
            newKey = 'demo_date';
          } else if (key === 'demo_time') {
            newKey = 'demo_time';
          } else if (key === 'demo_room') {
            newKey = 'room';
          }
          
          // Convert duration to integer
          if (newKey === 'duration') {
            demoData[newKey] = parseInt(value);
          } else {
            demoData[newKey] = value;
          }
        }
        
        // Validate required fields
        const requiredFields = ['applicant_id', 'demo_date', 'demo_time', 'duration', 'room', 'topic', 'area_of_specialization'];
        let missingFields = [];
        for (let field of requiredFields) {
          if (!demoData[field] || demoData[field] === '') {
            missingFields.push(field);
          }
        }
        
        if (missingFields.length > 0) {
          alert('Please fill in all required fields. Missing: ' + missingFields.join(', '));
          return;
        }
        
        // Send data to server
        fetch('../../handlers/admin/save_teaching_demo.php', {
          method: 'POST',
          body: JSON.stringify(demoData),
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Teaching demo scheduled successfully!');
            // Redirect to applicant view page after successful scheduling
            window.location.href = 'applicant_view.php?id=<?php echo $id; ?>';
          } else {
            alert('Error scheduling demo: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error scheduling demo. Please try again.');
        });
      });
    });
  </script>
</body>
</html>