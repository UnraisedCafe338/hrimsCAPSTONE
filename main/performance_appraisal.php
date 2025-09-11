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
  table th:nth-child(5),
  table td:nth-child(5) {
    width: 230px;
  }
  .actions { min-width: 180px; }
</style>
<body>
<?php include 'sidebar.php'; ?>
  <div class="header">Performance Appraisal</div><br><br><br><br><br>
    <div class="content">
        <?php
        // Initialize DB and filters before rendering controls
        include('../connection.php');
        $employees = $database->selectCollection("employee");
        $evaluations = $database->selectCollection("performance_evaluations");

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $selectedDepartment = isset($_GET['department']) ? trim($_GET['department']) : '';

        $filter = [];
        $andConditions = [];
        if ($search !== '') {
          $regexCond = ['$regex' => $search, '$options' => 'i'];
          $andConditions[] = ['$or' => [
            ['personal_info.first_name' => $regexCond],
            ['personal_info.middle_name' => $regexCond],
            ['personal_info.last_name' => $regexCond],
            ['full_name' => $regexCond],
          ]];
        }

        if ($selectedDepartment !== '' && strtolower($selectedDepartment) !== 'all') {
          if ($selectedDepartment === 'Unspecified') {
            $andConditions[] = ['$or' => [
              ['department' => ['$exists' => false]],
              ['department' => ''],
              ['department' => null],
            ]];
          } else {
            $andConditions[] = ['department' => $selectedDepartment];
          }
        }

        if (count($andConditions) > 0) {
          $filter = ['$and' => $andConditions];
        }

        // Fetch distinct departments for filter dropdown
        $departments = $employees->distinct('department');
        if (!is_array($departments)) { $departments = []; }
        $departments = array_values(array_filter(array_map('strval', $departments), function($d){ return trim($d) !== ''; }));
        sort($departments, SORT_NATURAL | SORT_FLAG_CASE);

        $employeeList = $employees->find($filter);
        $currentYear = date("Y");
        ?>
        <div class="box-header">
          <form method="GET">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>"
                   style="padding: 5px; width: 250px; border-radius: 5px; border: 1px solid #ccc;">
            <select name="department" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc; margin-left: 8px;">
              <option value="" <?php echo $selectedDepartment === '' ? 'selected' : ''; ?>>All Departments</option>
              <?php foreach ($departments as $dept): ?>
                <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo ($selectedDepartment === $dept) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($dept); ?>
                </option>
              <?php endforeach; ?>
              <option value="Unspecified" <?php echo ($selectedDepartment === 'Unspecified') ? 'selected' : ''; ?>>Unspecified</option>
            </select>
            <button type="submit" 
                    style="padding: 5px 10px; border-radius: 5px; border: none; background-color: #00124d; color: white; margin-left: 8px;">
              🔍
            </button>
          </form>
        </div>
  
<div class="box-body">
  <table>
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Department</th>
        <th>Overall Rating</th>
        <th>Category</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($employeeList as $emp) {
          $empId = $emp['_id'];

          $firstName = $emp['personal_info']['first_name'] ?? 'N/A';
          $middleName = $emp['personal_info']['middle_name'] ?? 'N/A';
          $lastName = $emp['personal_info']['last_name'] ?? 'N/A';
          $department = isset($emp['department']) && trim((string)$emp['department']) !== '' ? $emp['department'] : 'Unspecified';

          // Find the latest evaluation for this employee in the current year
          $evaluation = $evaluations->findOne([
              'employee_id' => $empId,
              'evaluated_at' => [
                  '$gte' => new \MongoDB\BSON\UTCDateTime(strtotime("$currentYear-01-01 00:00:00") * 1000),
                  '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime("$currentYear-12-31 23:59:59") * 1000)
              ]
          ]);

          $rating = $evaluation['rating'] ?? 'N/A';
          $category = $evaluation['category'] ?? 'N/A';

          $ratingColor = strtolower($category) === 'excellent' ? 'green' : (strtolower($category) === 'poor' ? 'red' : 'black');
          $isEvaluatedThisYear = $evaluation ? true : false;

          echo "<tr>";
          echo "<td>{$firstName} {$middleName} {$lastName}</td>";
          echo "<td>" . htmlspecialchars((string)$department) . "</td>";
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
