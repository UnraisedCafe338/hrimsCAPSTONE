import sys
import os

# Add the parent directory to the path so we can import the modules
sys.path.append(os.path.join(os.path.dirname(__file__), '..'))

from ai_modules.course_comparison import analyze_course_comparison_query, get_course_counts, generate_comparison_response
from ai_modules.employee_skills import analyze_skills_query, find_employee_by_name, generate_skills_response
from ai_modules.graduate_finder import analyze_graduate_query, find_graduates, generate_graduate_response
from ai_modules.graduate_lister import analyze_list_query, list_all_graduates, generate_list_response

def test_course_comparison():
    print("Testing course comparison feature...")
    is_comparison, courses = analyze_course_comparison_query("What course has more graduates?")
    print(f"Is comparison query: {is_comparison}")
    print(f"Extracted courses: {courses}")
    
    # Test with specific courses
    is_comparison, courses = analyze_course_comparison_query("How much percentage of IS compared to nursing?")
    print(f"Is comparison query: {is_comparison}")
    print(f"Extracted courses: {courses}")
    
    if courses:
        course_counts = get_course_counts(courses)
        print(f"Course counts: {course_counts}")
        response = generate_comparison_response(course_counts)
        print(f"Response: {response}")

def test_employee_skills():
    print("\nTesting employee skills feature...")
    is_skills, name = analyze_skills_query("What are the skills of Jonathan Carlos Olano?")
    print(f"Is skills query: {is_skills}")
    print(f"Extracted name: {name}")

def test_graduate_finder():
    print("\nTesting graduate finder feature...")
    is_graduate, education_level, course = analyze_graduate_query("Find me an IS graduate")
    print(f"Is graduate query: {is_graduate}")
    print(f"Education level: {education_level}")
    print(f"Course: {course}")

def test_graduate_lister():
    print("\nTesting graduate lister feature...")
    is_list, course = analyze_list_query("List me all IS graduates")
    print(f"Is list query: {is_list}")
    print(f"Course: {course}")

if __name__ == "__main__":
    test_course_comparison()
    test_employee_skills()
    test_graduate_finder()
    test_graduate_lister()
    print("\nAll tests completed!")