# HRIMS User Management System

This document explains how to manage users and roles in the HRIMS system.

## User Roles

The system supports the following user roles:

- `admin` - System administrators
- `employee` - Regular employees
- `department_head` - Department heads/deans
- `faculty` - Faculty members
- `staff` - Administrative staff
- `applicant` - Job applicants

## Available Management Tools

### 1. View Users (`view_users.php`)

Displays a list of all users in the system with their roles and departments.

### 2. Manage User Roles (`set_user_roles.php`)

Allows you to:

- View all users and their current roles
- Change user roles through a dropdown interface
- Add new users with specific roles

### 3. Add Sample Users (`add_sample_users.php`)

Adds sample users with different roles for testing purposes:

- admin@example.com
- employee@example.com
- dept.head@example.com
- faculty@example.com
- staff@example.com
- applicant@example.com

### 4. Reset User Roles (`reset_user_roles.php`)

Ensures all users have a role assigned. Users without a role will be assigned the default 'employee' role.

### 5. Test MongoDB (`test_mongodb.php`)

Verifies the MongoDB connection and displays database information.

### 6. Initialize MongoDB (`init_mongodb.php`)

Creates necessary collections and indexes in the database.

## How Role-Based Access Works

1. Users log in through the main employee login page (`users/employees/login.php`)
2. The system detects the user's role from the database
3. Users are redirected to their role-specific dashboard:
   - Admins: `users/admin/dashboard.php`
   - Employees: `users/employees/dashboard.php`
   - Department Heads: `users/department_heads/dashboard.php`
   - Faculty: `users/faculty/dashboard.php`
   - Staff: `users/staff/dashboard.php`
   - Applicants: `users/applicants/dashboard.php`

## Test Mode

For easier testing of dashboards without OTP verification:

1. Use the 'Test Mode' checkbox on the login page
2. Or add test users using `add_test_users_no_otp.php`
3. Test users can log in with just their email address

This allows you to quickly test all role-specific dashboards without setting up OTP authentication.

## Database Structure

User information is stored in the `users` collection with the following fields:

- `email` - User's email address (unique)
- `role` - User's role in the system
- `name` - User's full name
- `department` - User's department (optional, depending on role)
- `created_at` - Timestamp when the user was created

## Code Examples

Code examples for programmatically managing user roles can be found in:

- `examples/set_user_role_example.php` - Command-line example
- `examples/user_role_demo.php` - Web interface example
- `examples/query_users_by_role.php` - Query examples

These examples demonstrate how to:

- Set a user's role by email
- Create new users with specific roles
- Validate role assignments
- Handle errors gracefully
- Query users by role
- Get role distribution statistics

## Security Notes

- All user emails must be unique
- Passwords are not stored directly; the system uses OTP-based authentication
- Role changes take effect immediately
- Only administrators should have access to these management tools
