<?php
// Example script showing how to programmatically set user roles in MongoDB

require_once __DIR__ . '/../vendor/autoload.php';
include(__DIR__ . '/../handlers/connection.php');

use MongoDB\BSON\ObjectId;

/**
 * Set a user's role by email
 * 
 * @param string $email User's email address
 * @param string $role Role to assign (admin, employee, department_head, faculty, staff, applicant)
 * @return bool True if successful, false otherwise
 */
function setUserRole($email, $role) {
    global $usersCollection;
    
    // Validate role
    $validRoles = ['admin', 'employee', 'department_head', 'faculty', 'staff', 'applicant'];
    if (!in_array($role, $validRoles)) {
        echo "Error: Invalid role '$role'. Valid roles are: " . implode(', ', $validRoles) . "\n";
        return false;
    }
    
    try {
        // Find and update the user
        $result = $usersCollection->updateOne(
            ['email' => $email],
            ['$set' => ['role' => $role]]
        );
        
        if ($result->getMatchedCount() > 0) {
            if ($result->getModifiedCount() > 0) {
                echo "Success: Updated role for user '$email' to '$role'\n";
                return true;
            } else {
                echo "Info: User '$email' already has role '$role'\n";
                return true;
            }
        } else {
            echo "Error: User with email '$email' not found\n";
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Create a new user with a specific role
 * 
 * @param string $email User's email address
 * @param string $name User's full name
 * @param string $role Role to assign
 * @param string $department User's department (optional)
 * @return bool True if successful, false otherwise
 */
function createNewUser($email, $name, $role, $department = null) {
    global $usersCollection;
    
    // Validate role
    $validRoles = ['admin', 'employee', 'department_head', 'faculty', 'staff', 'applicant'];
    if (!in_array($role, $validRoles)) {
        echo "Error: Invalid role '$role'. Valid roles are: " . implode(', ', $validRoles) . "\n";
        return false;
    }
    
    try {
        // Check if user already exists
        $existingUser = $usersCollection->findOne(['email' => $email]);
        if ($existingUser) {
            echo "Error: User with email '$email' already exists\n";
            return false;
        }
        
        // Prepare user data
        $userData = [
            'email' => $email,
            'name' => $name,
            'role' => $role,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        // Add department if provided
        if ($department) {
            $userData['department'] = $department;
        }
        
        // Insert the new user
        $result = $usersCollection->insertOne($userData);
        
        if ($result->getInsertedCount() > 0) {
            echo "Success: Created new user '$email' with role '$role'\n";
            return true;
        } else {
            echo "Error: Failed to create user '$email'\n";
            return false;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Example usage:
echo "=== HRIMS User Role Management Examples ===\n\n";

// Example 1: Set an existing user's role
echo "1. Setting user role:\n";
setUserRole('employee@example.com', 'department_head');

echo "\n2. Creating a new user:\n";
createNewUser('new.employee@institution.edu', 'Jane Smith', 'employee', 'Human Resources');

echo "\n3. Attempting to set invalid role:\n";
setUserRole('admin@example.com', 'invalid_role');

echo "\n=== End of Examples ===\n";

// List all users and their roles
echo "\nCurrent users in the system:\n";
try {
    $users = $usersCollection->find([], ['sort' => ['role' => 1, 'email' => 1]]);
    $usersArray = $users->toArray();
    foreach ($usersArray as $user) {
        echo "- " . $user['email'] . " (" . $user['role'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error listing users: " . $e->getMessage() . "\n";
}
?>