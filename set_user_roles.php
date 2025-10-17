<?php
require_once __DIR__ . '/vendor/autoload.php';
include('handlers/connection.php');

use MongoDB\BSON\ObjectId;

echo "<h2>HRIMS User Role Management</h2>";

// Check if we're updating a user
if ($_POST['action'] ?? '' === 'update_role') {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    
    try {
        $updateResult = $usersCollection->updateOne(
            ['_id' => new ObjectId($userId)],
            ['$set' => ['role' => $newRole]]
        );
        
        if ($updateResult->getModifiedCount() > 0) {
            echo "<p style='color: green;'>Successfully updated user role to: $newRole</p>";
        } else {
            echo "<p style='color: red;'>Failed to update user role</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error updating user: " . $e->getMessage() . "</p>";
    }
}

// Check if we're adding a new user
if ($_POST['action'] ?? '' === 'add_user') {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $name = $_POST['name'] ?? 'User';
    
    // Check if user already exists
    $existingUser = $usersCollection->findOne(['email' => $email]);
    
    if ($existingUser) {
        echo "<p style='color: red;'>User with email $email already exists!</p>";
    } else {
        try {
            $insertResult = $usersCollection->insertOne([
                'email' => $email,
                'role' => $role,
                'name' => $name,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);
            
            if ($insertResult->getInsertedCount() > 0) {
                echo "<p style='color: green;'>Successfully added new user with role: $role</p>";
            } else {
                echo "<p style='color: red;'>Failed to add new user</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error adding user: " . $e->getMessage() . "</p>";
        }
    }
}

// Display all users
echo "<h3>Current Users</h3>";
try {
    $users = $usersCollection->find([], ['sort' => ['email' => 1]]);
    
    $usersArray = $users->toArray();
    if (count($usersArray) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Email</th><th>Name</th><th>Role</th><th>Actions</th></tr>";
        
        foreach ($usersArray as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['role'] ?? 'N/A') . "</td>";
            echo "<td>";
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='action' value='update_role'>";
            echo "<input type='hidden' name='user_id' value='" . $user['_id'] . "'>";
            echo "<select name='role'>";
            echo "<option value='admin'" . (($user['role'] ?? '') === 'admin' ? ' selected' : '') . ">Admin</option>";
            echo "<option value='employee'" . (($user['role'] ?? '') === 'employee' ? ' selected' : '') . ">Employee</option>";
            echo "<option value='department_head'" . (($user['role'] ?? '') === 'department_head' ? ' selected' : '') . ">Department Head</option>";
            echo "<option value='faculty'" . (($user['role'] ?? '') === 'faculty' ? ' selected' : '') . ">Faculty</option>";
            echo "<option value='staff'" . (($user['role'] ?? '') === 'staff' ? ' selected' : '') . ">Staff</option>";
            echo "<option value='applicant'" . (($user['role'] ?? '') === 'applicant' ? ' selected' : '') . ">Applicant</option>";
            echo "</select>";
            echo "<input type='submit' value='Update'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in the database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching users: " . $e->getMessage() . "</p>";
}

// Form to add a new user
echo "<h3>Add New User</h3>";
echo "<form method='POST'>";
echo "<input type='hidden' name='action' value='add_user'>";
echo "<table>";
echo "<tr><td>Email:</td><td><input type='email' name='email' required></td></tr>";
echo "<tr><td>Name:</td><td><input type='text' name='name'></td></tr>";
echo "<tr><td>Role:</td><td>";
echo "<select name='role'>";
echo "<option value='admin'>Admin</option>";
echo "<option value='employee' selected>Employee</option>";
echo "<option value='department_head'>Department Head</option>";
echo "<option value='faculty'>Faculty</option>";
echo "<option value='staff'>Staff</option>";
echo "<option value='applicant'>Applicant</option>";
echo "</select>";
echo "</td></tr>";
echo "<tr><td colspan='2'><input type='submit' value='Add User'></td></tr>";
echo "</table>";
echo "</form>";

echo "<hr>";
echo "<p><a href='index.php'>Back to Main Page</a></p>";
?>