<?php
// Example showing how to query users by role

require_once __DIR__ . '/../vendor/autoload.php';
include(__DIR__ . '/../handlers/connection.php');

/**
 * Get all users with a specific role
 * 
 * @param string $role Role to search for
 * @return array Array of users with the specified role
 */
function getUsersByRole($role) {
    global $usersCollection;
    
    try {
        $users = $usersCollection->find(['role' => $role]);
        return iterator_to_array($users);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return [];
    }
}

/**
 * Get count of users by role
 * 
 * @return array Associative array with role counts
 */
function getUserRoleCounts() {
    global $usersCollection;
    
    try {
        $pipeline = [
            ['$group' => ['_id' => '$role', 'count' => ['$sum' => 1]]],
            ['$sort' => ['_id' => 1]]
        ];
        
        $result = $usersCollection->aggregate($pipeline);
        $counts = [];
        
        foreach ($result as $item) {
            $counts[$item['_id']] = $item['count'];
        }
        
        return $counts;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return [];
    }
}

// Example usage:
echo "=== Query Users by Role Examples ===\n\n";

// Get role counts
echo "User Role Distribution:\n";
$roleCounts = getUserRoleCounts();
foreach ($roleCounts as $role => $count) {
    echo "- $role: $count users\n";
}

echo "\n---\n";

// Get all employees
echo "\nAll Employees:\n";
$employees = getUsersByRole('employee');
foreach ($employees as $employee) {
    echo "- " . $employee['name'] . " (" . $employee['email'] . ")\n";
}

echo "\n---\n";

// Get all department heads
echo "\nAll Department Heads:\n";
$deptHeads = getUsersByRole('department_head');
foreach ($deptHeads as $head) {
    echo "- " . $head['name'] . " (" . $head['email'] . ") - Department: " . ($head['department'] ?? 'N/A') . "\n";
}

echo "\n=== End of Examples ===\n";
?>