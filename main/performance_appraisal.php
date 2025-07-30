<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Appraisal</title>
</head>
<style>
    .performance-button {
    background-color: #00124d;
    border-left: 4px solid #ffffff;
}
</style>
<body>
<?php include 'sidebar.php'; ?>
  <div class="header">Performance Appraisal</div><br><br><br><br><br>
    <div class="content">
        <div class="box-header">
          <form method="GET">
    <input type="text" name="search" placeholder="Search..." style="padding: 5px; width: 250px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 5px 10px; border-radius: 5px; border: none; background-color: #00124d; color: white;">🔍</button>
  </form>
  </div>
  
<div class="box-body">
  <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: left;">
    <thead style="background-color: #00124d; color: white;">
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
$collection = $database->selectCollection("employees");

      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      $filter = $search ? ['full_name' => ['$regex' => $search, '$options' => 'i']] : [];

      $employees = $collection->find($filter);

      foreach ($employees as $emp) {
          $firstName = $emp['personal_info']['first_name'] ?? 'N/A';
          $middleName = $emp['personal_info']['middle_name'] ?? 'N/A';
          $lastName = $emp['personal_info']['last_name'] ?? 'N/A';
          $rating = $emp['rating'] ?? 'N/A';
          $category = $emp['category'] ?? 'N/A';

          $ratingColor = strtolower($rating) === 'excellent' ? 'green' : (strtolower($rating) === 'poor' ? 'red' : 'black');
          $isEvaluated = isset($emp['evaluated']) && $emp['evaluated'] === true;

          echo "<tr>";
          echo "<td><strong>{$firstName} {$middleName} {$lastName}</strong></td>";
          echo "<td style='color: {$ratingColor};'>{$rating}</td>";
          echo "<td style='color: #00124d;'>{$category}</td>";
          echo "<td>";
          if ($isEvaluated) {
              echo "<button disabled style='background-color: #00124d; color: gold; border: none; border-radius: 10px; padding: 5px 10px;'>Evaluated</button>";
          } else {
              echo "<a href='evaluate.php?id={$emp['_id']}' style='background-color: #00124d; color: white; border-radius: 10px; padding: 5px 10px; text-decoration: none;'>Evaluate</a>";
          }
          echo " <a href='view.php?id={$emp['_id']}' style='margin-left: 10px; color: #00124d;'>View</a>";
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