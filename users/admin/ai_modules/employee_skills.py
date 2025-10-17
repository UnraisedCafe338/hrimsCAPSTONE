import re
import sys
import requests
from pymongo import MongoClient

def analyze_skills_query(prompt):
    """Analyze if the prompt is asking for employee skills"""
    prompt_lower = prompt.lower()
    
    skills_patterns = [
        r'(what\s+are\s+the\s+skills\s+of|skills\s+of|tell\s+me\s+about\s+.*\s+skills)',
        r'(find\s+skills\s+for|get\s+skills\s+of|show\s+skills\s+of)',
    ]
    
    for pattern in skills_patterns:
        if re.search(pattern, prompt_lower):
            # Extract employee name from the query
            name = extract_employee_name(prompt_lower)
            return True, name
    
    return False, ""

def extract_employee_name(prompt_lower):
    """Extract employee name from the prompt"""
    # Remove the query part to get just the name
    name_patterns = [
        r'skills\s+of\s+(.+)',
        r'skills\s+for\s+(.+)',
        r'what\s+are\s+the\s+skills\s+of\s+(.+)',
        r'tell\s+me\s+about\s+(.+)\s+skills',
    ]
    
    for pattern in name_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            name = match.group(1).strip()
            # Remove common trailing words and punctuation
            name = re.sub(r'\s+(please|thanks|thank you)[.?!]*$', '', name, flags=re.IGNORECASE)
            # Remove trailing punctuation
            name = re.sub(r'[.?!]+$', '', name)
            return name
    
    return ""

def find_employee_by_name(name):
    """Find employee by name in both collections"""
    try:
        # Connect to MongoDB
        client = MongoClient("mongodb://localhost:27017/")
        db = client["hrims_db"]
        
        # Split name into parts
        name_parts = [part.strip() for part in name.split() if part.strip()]
        
        if not name_parts:
            return None
            
        # For names with multiple parts, try different combinations
        search_variants = generate_name_variants(name_parts)
        
        print(f"DEBUG: Searching for name '{name}' with variants: {search_variants}", file=sys.stderr)
        
        # Check both collections
        for collection_name in ["employee", "applicants"]:
            collection = db[collection_name]
            
            for variant in search_variants:
                print(f"DEBUG: Trying variant {variant} in collection {collection_name}", file=sys.stderr)
                
                # Try to find exact match
                employee = search_employee_exact(collection, variant)
                if employee:
                    print(f"DEBUG: Found exact match: {employee['name']}", file=sys.stderr)
                    return employee
                    
                # Try fuzzy match
                employee = search_employee_fuzzy(collection, variant)
                if employee:
                    print(f"DEBUG: Found fuzzy match: {employee['name']}", file=sys.stderr)
                    return employee
                    
        return None
    except Exception as e:
        print(f"DEBUG: Error finding employee: {e}", file=sys.stderr)
        return None

def generate_name_variants(name_parts):
    """Generate different name variants for searching"""
    variants = []
    
    if len(name_parts) == 1:
        # Single name part
        variants.append({"first": name_parts[0], "last": name_parts[0]})
    elif len(name_parts) == 2:
        # First and last name
        variants.append({"first": name_parts[0], "last": name_parts[1]})
    elif len(name_parts) == 3:
        # First, middle, last
        variants.append({"first": name_parts[0], "middle": name_parts[1], "last": name_parts[2]})
        # First + middle as first, last
        variants.append({"first": f"{name_parts[0]} {name_parts[1]}", "last": name_parts[2]})
        # Try with first name containing space (e.g., "Princes Lyka M Santos")
        variants.append({"first": f"{name_parts[0]} {name_parts[1]}", "middle": name_parts[2]})
    elif len(name_parts) >= 4:
        # Complex name - try different combinations
        # For "Jim Clark Vinuya Olano", try:
        # 1. First="Jim", Middle="Clark", Last="Vinuya Olano"
        # 2. First="Jim Clark", Middle="Vinuya", Last="Olano"
        # 3. First="Jim", Last="Clark Vinuya Olano"
        # 4. First="Jim Clark Vinuya", Last="Olano"
        
        # First + middle as first, last two as last
        variants.append({"first": name_parts[0], "middle": name_parts[1], "last": f"{name_parts[2]} {name_parts[3]}"})
        
        # First two as first, middle, last
        if len(name_parts) > 3:
            variants.append({"first": f"{name_parts[0]} {name_parts[1]}", "middle": name_parts[2], "last": name_parts[3]})
        
        # First as first, rest as last
        variants.append({"first": name_parts[0], "last": " ".join(name_parts[1:])})
        
        # All but last as first, last as last
        variants.append({"first": " ".join(name_parts[:-1]), "last": name_parts[-1]})
        
        # Try with first name containing multiple spaces
        if len(name_parts) == 4:
            variants.append({"first": f"{name_parts[0]} {name_parts[1]} {name_parts[2]}", "last": name_parts[3]})
    
    return variants

def search_employee_exact(collection, name_variant):
    """Search for employee with exact name match"""
    # Build a more flexible query that can handle partial matches and extra spaces
    and_conditions = []
    
    # For first name, allow partial matching
    if name_variant.get('first'):
        and_conditions.append({
            'personal_info.first_name': {'$regex': re.escape(name_variant['first']), '$options': 'i'}
        })
    
    # For last name, allow partial matching
    if name_variant.get('last'):
        and_conditions.append({
            'personal_info.last_name': {'$regex': re.escape(name_variant['last']), '$options': 'i'}
        })
    
    # Add middle name if provided
    if name_variant.get('middle'):
        and_conditions.append({
            'personal_info.middle_name': {'$regex': re.escape(name_variant['middle']), '$options': 'i'}
        })
    
    if and_conditions:
        query = {'$and': and_conditions}
    else:
        return None
    
    cursor = collection.find(query)
    for doc in cursor:
        return format_employee_response(doc)
    
    return None

def search_employee_fuzzy(collection, name_variant):
    """Search for employee with fuzzy name match"""
    # Create a more comprehensive fuzzy search
    or_conditions = []
    
    # Add conditions for each name part
    if name_variant.get('first'):
        or_conditions.extend([
            {'personal_info.first_name': {'$regex': re.escape(name_variant['first']), '$options': 'i'}},
            {'personal_info.first_name': {'$regex': f".*{re.escape(name_variant['first'])}.*", '$options': 'i'}}
        ])
    
    if name_variant.get('last'):
        or_conditions.extend([
            {'personal_info.last_name': {'$regex': re.escape(name_variant['last']), '$options': 'i'}},
            {'personal_info.last_name': {'$regex': f".*{re.escape(name_variant['last'])}.*", '$options': 'i'}}
        ])
    
    # Add middle name if provided
    if name_variant.get('middle'):
        or_conditions.extend([
            {'personal_info.middle_name': {'$regex': re.escape(name_variant['middle']), '$options': 'i'}},
            {'personal_info.middle_name': {'$regex': f".*{re.escape(name_variant['middle'])}.*", '$options': 'i'}}
        ])
    
    # Also search for the full name parts in any field
    full_name_parts = []
    if name_variant.get('first'):
        full_name_parts.append(name_variant['first'])
    if name_variant.get('last'):
        full_name_parts.append(name_variant['last'])
    if name_variant.get('middle'):
        full_name_parts.append(name_variant['middle'])
    
    for part in full_name_parts:
        if part:
            or_conditions.append({
                'personal_info.first_name': {'$regex': re.escape(part), '$options': 'i'}
            })
            or_conditions.append({
                'personal_info.middle_name': {'$regex': re.escape(part), '$options': 'i'}
            })
            or_conditions.append({
                'personal_info.last_name': {'$regex': re.escape(part), '$options': 'i'}
            })
    
    query = {'$or': or_conditions} if or_conditions else {}
    
    cursor = collection.find(query)
    for doc in cursor:
        return format_employee_response(doc)
    
    return None

def format_employee_response(doc):
    """Format employee data for response"""
    first_name = doc.get('personal_info', {}).get('first_name', '').strip()
    middle_name = doc.get('personal_info', {}).get('middle_name', '').strip()
    last_name = doc.get('personal_info', {}).get('last_name', '').strip()
    
    # Properly format the full name, handling empty parts
    name_parts = [part for part in [first_name, middle_name, last_name] if part]
    full_name = " ".join(name_parts).strip()
    
    skills = doc.get('skills', 'No skills information available')
    position = doc.get('position_applied', 'No position information')
    department = doc.get('department', 'No department information')
    
    # Get education information
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
        'skills': skills,
        'position': position,
        'department': department,
        'education': education_summary,
        'collection': doc.get('collection', 'Unknown')
    }

def generate_skills_response(employee_data):
    """Generate a natural language response for employee skills"""
    if not employee_data:
        return "I couldn't find an employee with that name."
    
    response_lines = [
        f"Here are the details for {employee_data['name']}:",
        f"Position: {employee_data['position']}",
        f"Department: {employee_data['department']}",
        f"Skills: {employee_data['skills']}",
        f"Education: {employee_data['education']}"
    ]
    
    return "\n".join(response_lines)