<?php
require_once __DIR__ . '/../vendor/autoload.php';
include(__DIR__ . '/../handlers/connection.php');

echo "<h2>User Role Management Demo</h2>";

// Handle form submissions
if ($_POST['action'] ?? '' === 'set_role') {
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    try {
        $result = $usersCollection->updateOne(
            ['email' => $email],
            ['$set' => ['role' => $role]]
        );
        
        if ($result->getMatchedCount() > 0) {
            if ($result->getModifiedCount() > 0) {
                echo "<p style='color: green;'>Successfully updated role for '$email' to '$role'</p>";
            } else {
                echo "<p>User '$email' already has role '$role'</p>";
            }
        } else {
            echo "<p style='color: red;'>User with email '$email' not found</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

if ($_POST['action'] ?? '' === 'create_user') {
    $email = $_POST['new_email'];
    $name = $_POST['new_name'];
    $role = $_POST['new_role'];
    $department = $_POST['new_department'] ?? '';
    
    try {
        // Check if user already exists
        $existingUser = $usersCollection->findOne(['email' => $email]);
        if ($existingUser) {
            echo "<p style='color: red;'>User with email '$email' already exists</p>";
        } else {
            $userData = [
                'email' => $email,
                'name' => $name,
                'role' => $role,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ];
            
            if (!empty($department)) {
                $userData['department'] = $department;
            }
            
            $result = $usersCollection->insertOne($userData);
            
            if ($result->getInsertedCount() > 0) {
                echo "<p style='color: green;'>Successfully created new user '$email' with role '$role'</p>";
            } else {
                echo "<p style='color: red;'>Failed to create user</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

// Display current users
echo "<h3>Current Users</h3>";
try {
    $users = $usersCollection->find([], ['sort' => ['role' => 1, 'email' => 1]]);
    
    $usersArray = $users->toArray();
    if (count($usersArray) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Email</th><th>Name</th><th>Role</th><th>Department</th></tr>";
        
        foreach ($usersArray as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['role'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['department'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching users: " . $e->getMessage() . "</p>";
}

// Form to set user role
echo "<h3>Set User Role</h3>";
echo "<form method='POST'>";
echo "<input type='hidden' name='action' value='set_role'>";
echo "<table>";
echo "<tr><td>Email:</td><td><input type='email' name='email' required></td></tr>";
echo "<tr><td>Role:</td><td>";
echo "<select name='role'>";
echo "<option value='admin'>Admin</option>";
echo "<option value='employee'>Employee</option>";
echo "<option value='department_head'>Department Head</option>";
echo "<option value='faculty'>Faculty</option>";
echo "<option value='staff'>Staff</option>";
echo "<option value='applicant'>Applicant</option>";
echo "</select>";
echo "</td></tr>";
echo "<tr><td colspan='2'><input type='submit' value='Update Role'></td></tr>";
echo "</table>";
echo "</form>";

// Form to create new user
echo "<h3>Create New User</h3>";
echo "<form method='POST'>";
echo "<input type='hidden' name='action' value='create_user'>";
echo "<table>";
echo "<tr><td>Email:</td><td><input type='email' name='new_email' required></td></tr>";
echo "<tr><td>Name:</td><td><input type='text' name='new_name' required></td></tr>";
echo "<tr><td>Role:</td><td>";
echo "<select name='new_role'>";
echo "<option value='admin'>Admin</option>";
echo "<option value='employee' selected>Employee</option>";
echo "<option value='department_head'>Department Head</option>";
echo "<option value='faculty'>Faculty</option>";
echo "<option value='staff'>Staff</option>";
echo "<option value='applicant'>Applicant</option>";
echo "</select>";
echo "</td></tr>";
echo "<tr><td>Department:</td><td><input type='text' name='new_department'></td></tr>";
echo "<tr><td colspan='2'><input type='submit' value='Create User'></td></tr>";
echo "</table>";
echo "</form>";

echo "<hr>";
echo "<p><a href='../index.php'>Back to Main Page</a></p>";
?>