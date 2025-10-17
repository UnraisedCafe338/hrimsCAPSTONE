import requests
import re
import sys
from pymongo import MongoClient

def analyze_course_comparison_query(prompt):
    """Analyze if the prompt is asking for course comparison/percentage analysis"""
    prompt_lower = prompt.lower()
    
    comparison_patterns = [
        r'(what|which)\s+course\s+has\s+more\s+graduates',
        r'(compare|comparison)\s+between\s+.*\s+and\s+',
        r'(percentage|percent|ratio)\s+of\s+.*\s+(compared|versus|vs)\s+',
        r'(more|less|greater|fewer)\s+graduates\s+in\s+.*\s+than\s+',
        r'which\s+is\s+(higher|lower|greater|more)\s+.*\s+or\s+'
    ]
    
    for pattern in comparison_patterns:
        if re.search(pattern, prompt_lower):
            # Extract course names from the query
            courses = extract_courses_from_prompt(prompt_lower)
            return True, courses
    
    return False, []

def extract_courses_from_prompt(prompt_lower):
    """Extract course names from the prompt"""
    courses = []
    
    # Common course patterns
    course_patterns = [
        r'(nursing|maritime|business|criminology|education|is|information\s+system|it|information\s+technology)',
        r'(computer\s+science|bsis|bsit|bsn|bshm|bsba|bscrim)',
    ]
    
    for pattern in course_patterns:
        matches = re.findall(pattern, prompt_lower)
        courses.extend(matches)
    
    return list(set(courses))  # Remove duplicates

def get_course_counts(courses):
    """Get graduate counts for specified courses from both collections"""
    try:
        # Connect to MongoDB
        client = MongoClient("mongodb://localhost:27017/")
        db = client["hrims_db"]
        
        course_counts = {}
        
        # Check both collections
        for collection_name in ["employee", "applicants"]:
            collection = db[collection_name]
            
            for course in courses:
                # Build query for this course
                query = build_course_query(course)
                count = collection.count_documents(query)
                
                if course in course_counts:
                    course_counts[course] += count
                else:
                    course_counts[course] = count
                    
        return course_counts
    except Exception as e:
        print(f"DEBUG: Error getting course counts: {e}", file=sys.stderr)
        return {}

def build_course_query(course):
    """Build MongoDB query for a specific course"""
    course_lower = course.lower()
    
    # Define course-specific search terms
    course_terms = {
        'nursing': ['nursing', 'bsn'],
        'maritime': ['maritime', 'marine'],
        'business': ['business', 'bsba', 'management'],
        'criminology': ['criminology', 'bscrim'],
        'education': ['education', 'bsed', 'beed'],
        'is': ['information system', 'bsis'],
        'it': ['information technology', 'bsit'],
        'computer science': ['computer science', 'bscs']
    }
    
    terms = course_terms.get(course_lower, [course_lower])
    
    # Build OR query across education fields
    or_conditions = []
    for term in terms:
        or_conditions.extend([
            {'education.college.degree': {'$regex': term, '$options': 'i'}},
            {'education.college.school': {'$regex': term, '$options': 'i'}},
            {'education.masteral.degree': {'$regex': term, '$options': 'i'}},
            {'education.masteral.school': {'$regex': term, '$options': 'i'}},
            {'education.doctoral.degree': {'$regex': term, '$options': 'i'}},
            {'education.doctoral.school': {'$regex': term, '$options': 'i'}}
        ])
    
    return {'$or': or_conditions}

def generate_comparison_response(course_counts):
    """Generate a natural language response for course comparison"""
    if not course_counts:
        return "I couldn't find any data for course comparison."
    
    # Sort courses by count
    sorted_courses = sorted(course_counts.items(), key=lambda x: x[1], reverse=True)
    
    if len(sorted_courses) == 1:
        course, count = sorted_courses[0]
        return f"I found {count} graduates for {course.upper()}."
    
    # Generate comparison response
    response_lines = ["Here's the course comparison:"]
    
    # Show counts
    for course, count in sorted_courses:
        response_lines.append(f"- {course.upper()}: {count} graduates")
    
    # Show percentages if more than one course
    if len(sorted_courses) > 1:
        total = sum(course_counts.values())
        if total > 0:
            response_lines.append("\nPercentages:")
            for course, count in sorted_courses:
                percentage = (count / total) * 100
                response_lines.append(f"- {course.upper()}: {percentage:.1f}%")
        
        # Identify the course with the most graduates
        top_course, top_count = sorted_courses[0]
        response_lines.append(f"\n{top_course.upper()} has the most graduates.")
    
    return "\n".join(response_lines)