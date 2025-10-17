# Sample Data Generation Summary

## Overview

This document summarizes the sample data generation for testing the AI system's ability to find employees and applicants with specific courses/degrees.

## Generated Sample Data

### Applicants Collection

- **File**: [generate_sample_employees.php](generate_sample_employees.php)
- **Count**: 20 sample applicants
- **Education Coverage**:
  - College degrees (20/20)
  - Masteral degrees (20/20)
  - Doctoral degrees (20/20)
- **Specific Degrees**:
  - Information Technology: 11 applicants
  - Computer Science: 12 applicants
  - Information Systems: 9 applicants
  - Computer Engineering: 1 applicant

### Employees Collection

- **File**: [generate_sample_employees_collection.php](generate_sample_employees_collection.php)
- **Count**: 10 sample employees
- **Education Coverage**:
  - College degrees (10/10)
  - Masteral degrees (10/10)
  - Doctoral degrees (10/10)
- **Specific Degrees**:
  - Information Technology: 6 employees
  - Computer Science: 6 employees
  - Information Systems: 4 employees
  - Computer Engineering: 1 employee

## Verification Scripts

### Check Applicant Data

- **File**: [check_sample_data.php](check_sample_data.php)
- Shows detailed information about sample applicants and their education backgrounds

### Check Employee Data

- **File**: [check_sample_employees.php](check_sample_employees.php)
- Shows detailed information about sample employees and their education backgrounds

## AI System Testing

### Direct Python Testing

- **Files**:
  - [test_ai_with_sample_data.php](test_ai_with_sample_data.php)
  - [test_ai_employee_query.php](test_ai_employee_query.php)
- **Purpose**: Tests direct communication with the AI Python script
- **Results**: AI system successfully identifies employees/applicants with specific degrees

## Sample Employee Details

### Information Technology Specialists

1. Princes Lyka Santos - BSIT (UP), MS in CS (Ateneo)
2. Carlos Garcia - BSIT (UE), MS in IS (PUP)
3. Isabella Rodriguez - BSIT (Adamson), PhD in CS (UP)
4. Daniel Flores - BSIT (FEU), MS in CS (UP)
5. Juan Dela Cruz - BSIS (DLSU), MS in IT (UST), PhD in IT (PNU)
6. Roberto Cruz - BSCS (San Beda), MS in IT (Ateneo de Naga)

### Computer Science Experts

1. Maria Reyes - BSCS (Mapua)
2. Roberto Cruz - BSCS (San Beda), MS in IT (Ateneo de Naga)
3. Elena Bautista - BSCS (Centro Escolar), MS in IS (DLSU), PhD in IT (UST)
4. Isaac Domingo - BSCS (University of Cebu)
5. Luna Navarro - BSCS (University of Iloilo)
6. Oscar Aquino - BSCS (Xavier), MS in CS (USM), PhD in IT (CPU)

### Information Systems Professionals

1. Juan Dela Cruz - BSIS (DLSU), MS in IT (UST), PhD in IT (PNU)
2. Michael Tan - BSIS (TIP), MS in CS (University of Makati)
3. Amanda Chua - BSIS (University of Asia and the Pacific)
4. Hannah Castillo - BSIS (University of Mindanao), MS in CS (MSU)
5. Kevin Ramos - BSIS (University of Baguio), PhD in CS (University of the East)
6. Nina Santiago - BSIS (University of Southeastern Philippines)
7. Marcus Torres - BSIT (UNOR), MS in IS (UPHSD)

### With Advanced Degrees

- **Masteral Degrees**: 20 applicants, 10 employees
- **Doctoral Degrees**: 4 applicants, 3 employees

## Testing AI Queries

The following queries should now work with the AI system:

1. "Find all Information Technology graduates"
2. "List employees with Masteral degrees"
3. "Show me all Computer Science graduates"
4. "Find all Information Systems employees"
5. "Who has a Doctoral degree?"

Each query should return relevant employees/applicants with detailed information about their education background.
