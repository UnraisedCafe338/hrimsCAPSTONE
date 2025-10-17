import re
import sys
import requests
from pymongo import MongoClient

def analyze_list_query(prompt):
    """Analyze if the prompt is asking to list all graduates of a specific course"""
    prompt_lower = prompt.lower()
    
    # Simpler and more effective pattern
    pattern = r'(find\s+all|list\s+me\s+all|show\s+me\s+all|give\s+me\s+all)\s+(.+?)\s+(graduates?)'
    match = re.search(pattern, prompt_lower)
    if match:
        course_name = match.group(2).strip()
        return True, course_name
    
    # Fallback patterns
    list_patterns = [
        r'(all\s+graduates?|all\s+employees|all\s+applicants)\s+(in|on|with|from)\s+',
        r'(list\s+of|names\s+of)\s+(employees|applicants|graduates?)\s+(in|on|with|from)\s+',
    ]
    
    for pattern in list_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            # Extract course from the query
            course = extract_course_from_prompt(prompt_lower)
            return True, course
    
    return False, ""

def extract_course_from_prompt(prompt_lower):
    """Extract course name from the prompt"""
    # Look for course names after "in", "with", "on", or "from"
    course_patterns = [
        r'(in|with|on|from)\s+([a-zA-Z\s]+?)(?:\s+(?:graduate|graduates|degree|employees|applicants|people|names|list))?(\.|\?|$)',
        r'(information\s+system|is|information\s+technology|it|computer\s+science|nursing|business|criminology|education|maritime)',
    ]
    
    for pattern in course_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            # Get the course name (group 2 if it exists, otherwise group 1)
            course = match.group(2) if len(match.groups()) >= 2 and match.group(2) else match.group(1)
            course = course.strip()
            
            # Clean up course name
            course = re.sub(r'\s+(graduate|graduates|degree|employees|applicants|people|names|list)$', '', course, flags=re.IGNORECASE)
            course = re.sub(r'(\.|\?)$', '', course)
            
            return course
    
    return ""

def list_all_graduates(course):
    """List all graduates of a specific course from both collections"""
    try:
        # Connect to MongoDB
        client = MongoClient("mongodb://localhost:27017/")
        db = client["hrims_db"]
        
        graduates = []
        
        # Check both collections
        for collection_name in ["employee", "applicants"]:
            collection = db[collection_name]
            
            # Build query for graduates in this course
            query = build_course_query(course)
            cursor = collection.find(query)
            
            for doc in cursor:
                graduate_info = format_graduate_response(doc)
                graduate_info['collection'] = collection_name
                graduates.append(graduate_info)
                
        return graduates
    except Exception as e:
        print(f"DEBUG: Error listing graduates: {e}", file=sys.stderr)
        return []

def build_course_query(course):
    """Build MongoDB query for graduates in a specific course"""
    course_regex = re.escape(course.lower())
    
    # Search across all education levels
    or_conditions = [
        {'education.college.degree': {'$regex': course_regex, '$options': 'i'}},
        {'education.college.school': {'$regex': course_regex, '$options': 'i'}},
        {'education.masteral.degree': {'$regex': course_regex, '$options': 'i'}},
        {'education.masteral.school': {'$regex': course_regex, '$options': 'i'}},
        {'education.doctoral.degree': {'$regex': course_regex, '$options': 'i'}},
        {'education.doctoral.school': {'$regex': course_regex, '$options': 'i'}}
    ]
    
    return {'$or': or_conditions}

def format_graduate_response(doc):
    """Format graduate data for response"""
    first_name = doc.get('personal_info', {}).get('first_name', '')
    middle_name = doc.get('personal_info', {}).get('middle_name', '')
    last_name = doc.get('personal_info', {}).get('last_name', '')
    
    full_name = f"{first_name} {middle_name} {last_name}".strip()
    
    # Get education information from all levels
    education_info = []
    if doc.get('education', {}).get('college', {}).get('degree'):
        college_degree = doc['education']['college']['degree']
        college_school = doc['education']['college'].get('school', '')
        education_info.append(f"College: {college_degree} from {college_school}")
        
    if doc.get('education', {}).get('masteral', {}).get('degree'):
        masteral_degree = doc['education']['masteral']['degree']
        masteral_school = doc['education']['masteral'].get('school', '')
        education_info.append(f"Masteral: {masteral_degree} from {masteral_school}")
        
    if doc.get('education', {}).get('doctoral', {}).get('degree'):
        doctoral_degree = doc['education']['doctoral']['degree']
        doctoral_school = doc['education']['doctoral'].get('school', '')
        education_info.append(f"Doctoral: {doctoral_degree} from {doctoral_school}")
    
    education_summary = "; ".join(education_info) if education_info else "No education information"
    
    return {
        'name': full_name,
        'education': education_summary,
        'position': doc.get('position_applied', ''),
        'department': doc.get('department', '')
    }

def generate_list_response(graduates, course):
    """Generate a natural language response for graduate list"""
    if not graduates:
        return f"I couldn't find any graduates in {course}."
    
    response_lines = [f"Here are all the graduates in {course}:"]
    
    # Group by collection for clarity
    employee_graduates = [g for g in graduates if g['collection'] == 'employee']
    applicant_graduates = [g for g in graduates if g['collection'] == 'applicants']
    
    if employee_graduates:
        response_lines.append(f"\nEmployees ({len(employee_graduates)}):")
        for i, graduate in enumerate(employee_graduates, 1):
            response_lines.append(f"{i}. {graduate['name']}")
            if graduate['position']:
                response_lines.append(f"   Position: {graduate['position']}")
            if graduate['education']:
                response_lines.append(f"   Education: {graduate['education']}")
    
    if applicant_graduates:
        response_lines.append(f"\nApplicants ({len(applicant_graduates)}):")
        for i, graduate in enumerate(applicant_graduates, 1):
            response_lines.append(f"{i}. {graduate['name']}")
            if graduate['position']:
                response_lines.append(f"   Position: {graduate['position']}")
            if graduate['education']:
                response_lines.append(f"   Education: {graduate['education']}")
    
    return "\n".join(response_lines)