import re
import sys
import requests
from pymongo import MongoClient

def analyze_graduate_query(prompt):
    """Analyze if the prompt is asking for graduates with specific education levels"""
    prompt_lower = prompt.lower()
    
    # Improved patterns that actually work
    patterns = [
        r'(find\s+me\s+an|find\s+an|show\s+me\s+a|list\s+me\s+all|find\s+all)\s+(.+?)\s+(graduate|graduates)',
        r'(who\s+has|who\s+have|which\s+employee|which\s+applicant)\s+(.+?)\s+(degree)',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            # For the first pattern, group 2 contains the course/degree info
            # For the second pattern, group 2 contains the course/degree info
            course_info = match.group(2).strip()
            # Extract education level and course from the course info
            education_level, course = extract_education_info_from_text(course_info, prompt_lower)
            return True, education_level, course
    
    # Original patterns as fallback
    graduate_patterns = [
        r'(find\s+me\s+an|find\s+an|show\s+me\s+a|list\s+me\s+all)\s+(employee|applicant|graduate)s?.*\s+(college\s+degree|masteral\s+degree|doctoral\s+degree|degree)',
        r'(find\s+me\s+an|find\s+an|show\s+me\s+a|list\s+me\s+all)\s+(college\s+graduate|masteral\s+graduate|doctoral\s+graduate)',
        r'(who\s+has|who\s+have|which\s+employee|which\s+applicant)\s+(college\s+degree|masteral\s+degree|doctoral\s+degree)',
    ]
    
    for pattern in graduate_patterns:
        if re.search(pattern, prompt_lower):
            # Extract education level and course from the query
            education_level, course = extract_education_info(prompt_lower)
            return True, education_level, course
    
    return False, "", ""

def extract_education_info_from_text(course_info, prompt_lower):
    """Extract education level and course from text description"""
    # Extract education level
    education_level = "college"  # default
    
    if "masteral" in prompt_lower or "master's" in prompt_lower or "master" in course_info:
        education_level = "masteral"
    elif "doctoral" in prompt_lower or "phd" in prompt_lower or "doctorate" in course_info:
        education_level = "doctoral"
    
    # Extract course - remove education level words from course_info
    course = re.sub(r'\b(college|masteral|master\'?s?|doctoral|phd|doctorate)\b', '', course_info, flags=re.IGNORECASE).strip()
    course = re.sub(r'\s+', ' ', course)  # Clean up multiple spaces
    
    return education_level, course

def extract_education_info(prompt_lower):
    """Extract education level and course from the prompt"""
    # Extract education level
    education_level = "college"  # default
    
    if "masteral" in prompt_lower or "master's" in prompt_lower:
        education_level = "masteral"
    elif "doctoral" in prompt_lower or "phd" in prompt_lower:
        education_level = "doctoral"
    
    # Extract course
    course = ""
    
    # Look for course names after "in" or "with"
    course_patterns = [
        r'(in|with|on|about)\s+([a-zA-Z\s]+)(?:\s+degree)?',
        r'(information\s+system|is|information\s+technology|it|computer\s+science|nursing|business|criminology|education|maritime)',
    ]
    
    for pattern in course_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            course = match.group(2) if len(match.groups()) >= 2 else match.group(1)
            course = course.strip()
            break
    
    # Clean up course name
    course = re.sub(r'\s+(degree|graduate|graduates)$', '', course, flags=re.IGNORECASE)
    
    return education_level, course

def find_graduates(education_level, course):
    """Find graduates with specific education level and course"""
    try:
        # Connect to MongoDB
        client = MongoClient("mongodb://localhost:27017/")
        db = client["hrims_db"]
        
        graduates = []
        
        # Check both collections
        for collection_name in ["employee", "applicants"]:
            collection = db[collection_name]
            
            # Build query for graduates
            query = build_graduate_query(education_level, course)
            cursor = collection.find(query)
            
            for doc in cursor:
                graduate_info = format_graduate_response(doc, education_level)
                graduate_info['collection'] = collection_name
                graduates.append(graduate_info)
                
        return graduates
    except Exception as e:
        print(f"DEBUG: Error finding graduates: {e}", file=sys.stderr)
        return []

def build_graduate_query(education_level, course):
    """Build MongoDB query for graduates with specific education level and course"""
    # Map education level to field paths
    education_fields = {
        "college": ["education.college.degree", "education.college.school"],
        "masteral": ["education.masteral.degree", "education.masteral.school"],
        "doctoral": ["education.doctoral.degree", "education.doctoral.school"]
    }
    
    fields = education_fields.get(education_level, education_fields["college"])
    
    # Build query with course search
    if course:
        course_regex = re.escape(course.lower())
        or_conditions = []
        for field in fields:
            or_conditions.append({field: {'$regex': course_regex, '$options': 'i'}})
        # Include personal info fields in the query to ensure they're returned
        query = {'$and': [ {'$or': or_conditions} ]}
        return query
    else:
        # Find anyone with education at this level
        return {fields[0]: {'$exists': True, '$ne': ''}}

def format_graduate_response(doc, education_level):
    """Format graduate data for response"""
    first_name = doc.get('personal_info', {}).get('first_name', '')
    middle_name = doc.get('personal_info', {}).get('middle_name', '')
    last_name = doc.get('personal_info', {}).get('last_name', '')
    
    full_name = f"{first_name} {middle_name} {last_name}".strip()
    
    # Get education information based on level
    education_info = {}
    if education_level == "college":
        education_info = {
            'degree': doc.get('education', {}).get('college', {}).get('degree', ''),
            'school': doc.get('education', {}).get('college', {}).get('school', '')
        }
    elif education_level == "masteral":
        education_info = {
            'degree': doc.get('education', {}).get('masteral', {}).get('degree', ''),
            'school': doc.get('education', {}).get('masteral', {}).get('school', '')
        }
    elif education_level == "doctoral":
        education_info = {
            'degree': doc.get('education', {}).get('doctoral', {}).get('degree', ''),
            'school': doc.get('education', {}).get('doctoral', {}).get('school', '')
        }
    
    return {
        'name': full_name,
        'degree': education_info['degree'],
        'school': education_info['school'],
        'position': doc.get('position_applied', ''),
        'department': doc.get('department', '')
    }

def generate_graduate_response(graduates, education_level, course):
    """Generate a natural language response for graduates"""
    if not graduates:
        return f"I couldn't find any {education_level} graduates in {course}." if course else f"I couldn't find any {education_level} graduates."
    
    # Determine if we're listing all or just finding one
    if len(graduates) == 1:
        graduate = graduates[0]
        response_lines = [
            f"I found a {education_level} graduate:",
            f"Name: {graduate['name']}",
            f"Degree: {graduate['degree']}",
            f"School: {graduate['school']}"
        ]
        if graduate['position']:
            response_lines.append(f"Position: {graduate['position']}")
        if graduate['department']:
            response_lines.append(f"Department: {graduate['department']}")
            
        return "\n".join(response_lines)
    else:
        response_lines = [f"I found {len(graduates)} {education_level} graduates in {course if course else 'various fields'}:"]
        
        for i, graduate in enumerate(graduates[:10], 1):  # Limit to first 10
            response_lines.append(f"{i}. {graduate['name']} - {graduate['degree']} from {graduate['school']}")
            
        if len(graduates) > 10:
            response_lines.append(f"... and {len(graduates) - 10} more")
            
        return "\n".join(response_lines)