# Employee Login System for HRIMS

This document provides information about the employee login system implemented for the Human Resources Information Management System (HRIMS).

## Overview

The employee login system allows deans and other employees to log in to a separate portal where they can evaluate teachers in their department. This system is distinct from the admin login and provides role-based access control.

## Features

1. **Separate Login Portal**: Employees (deans) have their own login page at `/users/employees/login.php`
2. **Two-Factor Authentication**: Uses Google Authenticator for secure login
3. **Role-Based Access**: Only users with `role: "employee"` can access the employee portal
4. **Department-Based Evaluation**: Deans can only evaluate teachers in their own department
5. **Performance Evaluation**: Deans can evaluate teachers based on multiple criteria

## File Structure

```
/users/employees/
├── login.php           # Employee login page
├── dashboard.php       # Employee dashboard
├── evaluate.php        # Teacher evaluation form
├── logout.php          # Logout functionality
└── register_employee.php # Admin script to register new employees
```

## Database Structure

### Users Collection

Employee users are stored in the `users` collection with the following fields:

- `email`: Employee work email
- `first_name`: First name
- `last_name`: Last name
- `employee_id`: Unique employee identifier
- `position`: Job position (e.g., "Dean")
- `department`: Department name
- `role`: Set to "employee" for employee users
- `otp_secret`: Secret for Google Authenticator
- `created_at`: Timestamp of creation

### Employees Collection

Additional employee data is stored in the `employees` collection with similar fields.

### Evaluations Collection

Performance evaluations are stored in the `evaluations` collection.

## Setup Instructions

1. **Access the Employee Login**:

   - Visit `http://localhost/hrims/users/employees/login.php`
   - Or click "Employee/Dean Login" on the main login page

2. **Register New Employees** (Admin Only):

   - Admins can register new employees using the registration script
   - Run `register_employee.php` from the admin panel

3. **Two-Factor Authentication Setup**:
   - After registration, employees need to set up Google Authenticator
   - Scan the QR code provided during registration
   - Use the generated 6-digit codes to log in

## Testing

A test employee has been created with the following credentials:

- Email: `dean.cs@institution.edu`
- OTP Secret: `OALXCMCEVC4AHEYX`
- Position: Dean
- Department: Computer Science

To test the login:

1. Visit the employee login page
2. Enter the email above
3. Generate a 6-digit OTP code using an authenticator app with the provided secret
4. Login to access the employee dashboard

## Security

- All passwords are hashed using PHP's `password_hash()` function
- Two-factor authentication is mandatory for all employee accounts
- Session management follows security best practices
- Role-based access control prevents unauthorized access

## Customization

The employee portal can be customized by modifying the following files:

- `login.php`: Login page design and functionality
- `dashboard.php`: Main dashboard layout and statistics
- `evaluate.php`: Teacher evaluation form and criteria
- `register_employee.php`: Employee registration form

## Troubleshooting

1. **Login Issues**:

   - Verify the email address is correct
   - Ensure the user has `role: "employee"` in the database
   - Confirm the OTP code is generated using the correct secret

2. **Database Connection**:

   - Check `handlers/connection.php` for correct MongoDB configuration
   - Ensure MongoDB is running and accessible

3. **Missing Collections**:
   - Run the initialization scripts in `MONGODB_INIT_INSTRUCTIONS.md` if needed
