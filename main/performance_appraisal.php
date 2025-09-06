<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Appraisal</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<style>
  .performance-button {
    background-color: #00124d;
    border-left: 4px solid #ffffff;
  }
  /* Fix Action column width */
  table th:nth-child(4),
  table td:nth-child(4) {
    width: 230px;
  }
  .actions { min-width: 180px; }
</style>
<body>
<?php include 'sidebar.php'; ?>
  <div class="header">Performance Appraisal</div><br><br><br><br><br>
    <div class="content">
        <div class="box-header">
          <form method="GET">
            <input type="text" name="search" placeholder="Search..." 
                   style="padding: 5px; width: 250px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" 
                    style="padding: 5px 10px; border-radius: 5px; border: none; background-color: #00124d; color: white;">
              🔍
            </button>
          </form>
        </div>
  
<div class="box-body">
  <table>
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Overall Rating</th>
        <th>Category</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      include('../connection.php');
      $employees = $database->selectCollection("employees");
      $evaluations = $database->selectCollection("performance_evaluations");

      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      $filter = $search ? ['full_name' => ['$regex' => $search, '$options' => 'i']] : [];

      $employeeList = $employees->find($filter);

      $currentYear = date("Y");

      foreach ($employeeList as $emp) {
          $empId = $emp['_id'];

          $firstName = $emp['personal_info']['first_name'] ?? 'N/A';
          $middleName = $emp['personal_info']['middle_name'] ?? 'N/A';
          $lastName = $emp['personal_info']['last_name'] ?? 'N/A';

          // Find the latest evaluation for this employee in the current year
          $evaluation = $evaluations->findOne([
              'employee_id' => $empId,
              'evaluated_at' => [
                  '$gte' => new MongoDB\BSON\UTCDateTime(strtotime("$currentYear-01-01 00:00:00") * 1000),
                  '$lte' => new MongoDB\BSON\UTCDateTime(strtotime("$currentYear-12-31 23:59:59") * 1000)
              ]
          ]);

          $rating = $evaluation['rating'] ?? 'N/A';
          $category = $evaluation['category'] ?? 'N/A';

          $ratingColor = strtolower($category) === 'excellent' ? 'green' : (strtolower($category) === 'poor' ? 'red' : 'black');
          $isEvaluatedThisYear = $evaluation ? true : false;

          echo "<tr>";
          echo "<td>{$firstName} {$middleName} {$lastName}</td>";
          echo "<td style='color: {$ratingColor};'>{$rating}</td>";
          echo "<td style='color: #00124d;'>{$category}</td>";
          echo "<td class='actions'>";
          if ($isEvaluatedThisYear) {
              echo "<button disabled style='background-color: #00124d; color: gold; border: none; border-radius: 10px; padding: 5px 10px;'>Evaluated ($currentYear)</button>";
          } else {
              echo "<a class='btn-evaluate' href='evaluate.php?id={$empId}'>Evaluate</a>";
          }
          echo " <a class='btn-view' href='view.php?id={$empId}'>View</a>";
          echo "</td>";
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>
  </div>
</div>

</body>
</html>
