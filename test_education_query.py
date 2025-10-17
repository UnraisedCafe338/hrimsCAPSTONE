#!/usr/bin/env python3

import sys
import os

# Add the admin directory to the path so we can import the ai_script module
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'users', 'admin'))

def test_education_query():
    """Test the education query functionality"""
    print("Testing education query functionality...")
    
    # We'll test by running the ai_script with specific queries
    print("\n1. Testing query for 'information system'...")
    # This would normally be done by calling the script with arguments
    print("This test would normally call the ai_script.py with arguments")
    print("Example: python ai_script.py \"Find me an employee who has IS masteral degree\"")
    
    print("\n2. Testing query for 'BSIS'...")
    print("Example: python ai_script.py \"Find employees with BSIS degrees\"")
    
    print("\n3. Testing query for 'masteral'...")
    print("Example: python ai_script.py \"Find employees with masteral degrees\"")
    
    print("\n4. Testing query for 'doctoral'...")
    print("Example: python ai_script.py \"Find employees with doctoral degrees\"")

if __name__ == "__main__":
    test_education_query()