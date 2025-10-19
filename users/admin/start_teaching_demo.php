<?php
include('../../handlers/connection.php');

// Get demo ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("No demo ID provided.");
}

try {
    // Get the teaching demo
    $demoCollection = $database->selectCollection("teaching_demos");
    $demo = $demoCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($id)]);
    
    if (!$demo) {
        die("Teaching demo not found.");
    }
    
    // Get applicant information
    $applicantCollection = $database->selectCollection("applicants");
    $applicant = $applicantCollection->findOne(['_id' => $demo->applicant_id]);
    
    // Extract applicant information
    $applicantName = $demo->applicant_name;
    
    // Handle date properly - it might be stored as a string or MongoDB date object
    $demoDateObj = $demo->demo_date;
    if (is_object($demoDateObj) && method_exists($demoDateObj, 'toDateTime')) {
        // It's a MongoDB date object
        $demoDate = $demoDateObj->toDateTime()->format('F j, Y');
    } else {
        // It's likely a string
        $demoDate = date('F j, Y', strtotime($demoDateObj));
    }
    
    $demoTime = $demo->demo_time;
    $demoRoom = $demo->room;
    $demoTopic = $demo->topic;
    $demoDuration = $demo->duration;
    
    // Get information from the teaching demo record
    $areaOfSpecialization = $demo->area_of_specialization ?? '';
    $license = $demo->license ?? 'N/A';
    $positionApplied = $applicant['position_applied'] ?? '';
    
    // Generate Form ID (using demo ID as the basis)
    $formId = "TIOOI-NE2M3-" . strtoupper(substr($id, 0, 8));
    
} catch (Exception $e) {
    die("Error retrieving demo: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teaching Demo Evaluation - <?php echo htmlspecialchars($applicantName); ?></title>
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
    
    .form-container {
      max-width: 1000px;
      margin: 0 auto;
      background: white;
      padding: 30px;
      border: 2px solid #00124d;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 18, 77, 0.2);
    }
    
    .form-header {
      text-align: center;
      margin-bottom: 30px;
      border-bottom: 2px solid #00124d;
      padding-bottom: 20px;
    }
    
    .form-header h1 {
      color: #00124d;
      margin: 0 0 10px 0;
      font-size: 24px;
    }
    
    .form-header p {
      margin: 5px 0;
      color: #333;
      font-weight: bold;
    }
    
    .form-section {
      margin-bottom: 30px;
    }
    
    .form-section h3 {
      color: #00124d;
      border-bottom: 1px solid #ccc;
      padding-bottom: 10px;
      margin-top: 0;
    }
    
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .info-item {
      margin-bottom: 15px;
    }
    
    .info-item.full-width {
      grid-column: 1 / -1;
    }
    
    .info-label {
      font-weight: bold;
      color: #00124d;
      display: block;
      margin-bottom: 5px;
    }
    
    .info-value {
      padding: 8px 12px;
      border-bottom: 1px solid #ccc;
      min-height: 38px;
    }
    
    .evaluation-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    
    .evaluation-table th, .evaluation-table td {
      border: 1px solid #000;
      padding: 10px;
      text-align: center;
    }
    
    .evaluation-table th {
      background-color: #00124d;
      color: white;
    }
    
    .evaluation-table td:first-child {
      text-align: left;
    }
    
    .evaluation-table .rating-column {
      width: 80px;
    }
    
    .rating-input {
      width: 60px;
      text-align: center;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    
    .recommendation-section {
      margin: 30px 0;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    
    .recommendation-options {
      display: flex;
      gap: 30px;
      margin: 15px 0;
    }
    
    .recommendation-option {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .signature-section {
      margin-top: 50px;
      padding-top: 20px;
      border-top: 1px solid #000;
    }
    
    .signature-line {
      margin: 30px 0;
      padding: 40px 0 10px 0;
      border-bottom: 1px solid #000;
      width: 300px;
    }
    
    .note-section {
      background-color: #ffffcc;
      padding: 15px;
      border: 1px solid #ffcc00;
      border-radius: 5px;
      margin-top: 30px;
      font-weight: bold;
    }
    
    .form-actions {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #eee;
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
      margin: 0 10px;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 51, 102, 0.3);
    }

    .btn-secondary {
      background: linear-gradient(90deg, #6c757d 0%, #495057 100%);
    }
    
    @media (max-width: 768px) {
      .info-grid {
        grid-template-columns: 1fr;
      }
      
      .recommendation-options {
        flex-direction: column;
        gap: 10px;
      }
      
      .btn {
        display: block;
        width: 100%;
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  
  <div class="header">Teaching Demo Evaluation</div>
  
  <div class="content">
    <div class="box-header">
      <h2>Evaluation for <?php echo htmlspecialchars($applicantName); ?></h2>
      <a href="view_teaching_demos.php" class="back-link">← Back to Scheduled Demos</a>
    </div>
    
    <div class="box-body">
      <div class="form-container">
        <div class="form-header">
          <h1>EXACT COLLEGES OF ASIA, INC.</h1>
          <p>Saclayin, Arayat, Pampanga</p>
          <p>FORM NO <?php echo htmlspecialchars($formId); ?></p>
        </div>
        
        <div class="form-section">
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">NAME:</span>
              <div class="info-value"><?php echo htmlspecialchars($applicantName); ?></div>
            </div>
            <div class="info-item">
              <span class="info-label">POSITION:</span>
              <div class="info-value"><?php echo htmlspecialchars($positionApplied); ?></div>
            </div>
            <div class="info-item">
              <span class="info-label">AREA OF SPECIALIZATION:</span>
              <div class="info-value"><?php echo htmlspecialchars($areaOfSpecialization); ?></div>
            </div>
            <div class="info-item">
              <span class="info-label">DEMO TOPIC:</span>
              <div class="info-value"><?php echo htmlspecialchars($demoTopic); ?></div>
            </div>
            <div class="info-item">
              <span class="info-label">DATE:</span>
              <div class="info-value"><?php echo htmlspecialchars($demoDate); ?></div>
            </div>
            <div class="info-item">
              <span class="info-label">LICENSE:</span>
              <div class="info-value"><?php echo htmlspecialchars($license); ?></div>
            </div>
          </div>
        </div>
        
        <form id="evaluationForm">
          <input type="hidden" name="demo_id" value="<?php echo htmlspecialchars($id); ?>">
          
          <div class="form-section">
            <h3>PERFORMANCE EVALUATION TABLE</h3>
            
            <table class="evaluation-table">
              <thead>
                <tr>
                  <th>PERSONAL TRAITS</th>
                  <th>SUPERIOR<br>91 - 95</th>
                  <th>VERY GOOD<br>86 - 90</th>
                  <th>GOOD<br>81 - 85</th>
                  <th>FAIR<br>76 - 80</th>
                  <th>POOR<br>70 - 75</th>
                  <th>RATING/<br>SCORE</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1. PRESENTATION</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="presentation" required></td>
                </tr>
                <tr>
                  <td>2. PERSONALITY</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="personality" required></td>
                </tr>
                <tr>
                  <td>3. QUALITY OF VOICE</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="voice_quality" required></td>
                </tr>
                <tr>
                  <td>4. TECHNICAL KNOWLEDGE</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="technical_knowledge" required></td>
                </tr>
                <tr>
                  <td>5. RESOURCEFULNESS</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="resourcefulness" required></td>
                </tr>
                <tr>
                  <td>6. CLASS MANAGEMENT</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="class_management" required></td>
                </tr>
                <tr>
                  <td>7. TEACHING ABILITY</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="teaching_ability" required></td>
                </tr>
                <tr>
                  <td>8. COMMUNICATION SKILLS</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="communication_skills" required></td>
                </tr>
                <tr>
                  <td>9. TIME MANAGEMENT</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="time_management" required></td>
                </tr>
                <tr>
                  <td>10. HUMAN RELATION</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="human_relation" required></td>
                </tr>
                <tr>
                  <td colspan="6" style="text-align: right; font-weight: bold;">OVER ALL RATING</td>
                  <td><input type="number" min="70" max="95" class="rating-input" name="overall_rating" required readonly style="background-color: #f0f0f0;"></td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="recommendation-section">
            <h3>RECOMMENDABLE</h3>
            <div class="recommendation-options">
              <div class="recommendation-option">
                <input type="radio" id="recommend_yes" name="recommendation" value="yes" required>
                <label for="recommend_yes">YES</label>
              </div>
              <div class="recommendation-option">
                <input type="radio" id="recommend_no" name="recommendation" value="no" required>
                <label for="recommend_no">NO</label>
              </div>
            </div>
          </div>
          
          <div class="signature-section">
            <div class="signature-line"></div>
            <p>Signature over printed name</p>
            <p>(Head/Member Screening Committee)</p>
          </div>
          
          <div class="note-section">
            Note: Applicants with overall rating of 81% and above could be recommended for hiring.
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="saveAsDraft()">Save as Draft</button>
            <button type="submit" class="btn">Submit Evaluation</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Form submission handler
    document.addEventListener('DOMContentLoaded', function() {
      // Add form submission handler
      const evaluationForm = document.getElementById('evaluationForm');
      if (evaluationForm) {
        // Add real-time calculation of overall rating
        const ratingInputs = document.querySelectorAll('.rating-input:not([name="overall_rating"])');
        const overallRatingInput = document.querySelector('.rating-input[name="overall_rating"]');
        
        // Function to calculate overall rating
        function calculateOverallRating() {
          let sum = 0;
          let count = 0;
          
          ratingInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value >= 70 && value <= 95) {
              sum += value;
              count++;
            }
          });
          
          if (count > 0) {
            const average = Math.round(sum / count);
            overallRatingInput.value = average;
          } else {
            overallRatingInput.value = '';
          }
        }
        
        // Add event listeners to all rating inputs except overall rating
        ratingInputs.forEach(input => {
          input.addEventListener('input', calculateOverallRating);
          input.addEventListener('change', calculateOverallRating);
          input.addEventListener('keyup', calculateOverallRating);
          input.addEventListener('blur', calculateOverallRating);
        });
        
        evaluationForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Calculate overall rating before submission
          calculateOverallRating();
          
          // Get form data
          const formData = new FormData(evaluationForm);
          
          // Send data to server
          fetch('../../handlers/admin/save_teaching_evaluation.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Evaluation submitted successfully!');
              // Redirect back to scheduled demos
              window.location.href = 'view_teaching_demos.php';
            } else {
              alert('Error saving evaluation: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error saving evaluation. Please try again.');
          });
        });
      }
    });
    
    function saveAsDraft() {
      alert('Evaluation saved as draft.');
      // In a real implementation, you would save the data to the server
    }
  </script>
</body>
</html>