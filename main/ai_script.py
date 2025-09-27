import requests
from pymongo import MongoClient
import sys
import json
import re

if len(sys.argv) < 2:
    print("Please provide a prompt")
    sys.exit(1)

prompt = sys.argv[1]

def looks_like_db_question(text):
    keywords = ["employee", "applicant", "graduate", "skills", "how many", "list", "find", "search", "show", "degree", "course"]
    return any(word in text.lower() for word in keywords)

# --- AI Server Configuration ---
AI_SERVER_URL = "http://127.0.0.1:8001/v1/completions"  # Your FastAPI AI server
AI_SERVER_HEADERS = {
    "Content-Type": "application/json"
}

def query_employee_data(search_term, query_type="general", collection="employee", action="search"):
    """Query employee/applicant data via PHP endpoint or direct MongoDB"""
    # First try PHP endpoint with enhanced capabilities
    try:
        url = "http://localhost/hrims/handlers/ai_data_query.php"
        params = {
            "search": search_term,
            "type": query_type,
            "collection": collection,
            "action": action  # New parameter for advanced queries
        }
        
        response = requests.get(url, params=params, timeout=5)
        if response.status_code == 200:
            return response.json()
        else:
            raise Exception(f"PHP endpoint failed: HTTP {response.status_code}")
    except Exception as e:
        print(f"DEBUG: PHP endpoint failed ({e}), trying direct MongoDB", file=sys.stderr)
        
        # Fallback to direct MongoDB query
        try:
            from pymongo import MongoClient
            client = MongoClient("mongodb://localhost:27017/")
            db = client["hrims_db"]
            mongo_collection = db[collection]
            
            # Build query based on type
            if query_type == "person":
                # Search for specific person by name
                name_parts = search_term.split()
                if len(name_parts) >= 2:
                    first_name = name_parts[0]
                    last_name = name_parts[1]
                    query = {
                        '$and': [
                            {'personal_info.first_name': {'$regex': first_name, '$options': 'i'}},
                            {'personal_info.last_name': {'$regex': last_name, '$options': 'i'}}
                        ]
                    }
                else:
                    # Single name search
                    query = {
                        '$or': [
                            {'personal_info.first_name': {'$regex': search_term, '$options': 'i'}},
                            {'personal_info.last_name': {'$regex': search_term, '$options': 'i'}}
                        ]
                    }
            elif query_type == "education":
                # Search multiple education-related fields
                query = {
                    '$or': [
                        {'education.college.degree': {'$regex': search_term, '$options': 'i'}},
                        {'education.college.school': {'$regex': search_term, '$options': 'i'}},
                        # Also search for individual terms
                        {'education.college.degree': {'$regex': 'information system', '$options': 'i'}},
                        {'education.college.degree': {'$regex': 'BSIS', '$options': 'i'}},
                        {'education.college.degree': {'$regex': 'information', '$options': 'i'}},
                        {'education.college.school': {'$regex': 'information', '$options': 'i'}}
                    ]
                }
            elif query_type == "skills":
                query = {'skills': {'$regex': search_term, '$options': 'i'}}
            else:
                query = {
                    '$or': [
                        {'personal_info.first_name': {'$regex': search_term, '$options': 'i'}},
                        {'personal_info.last_name': {'$regex': search_term, '$options': 'i'}},
                        {'position_applied': {'$regex': search_term, '$options': 'i'}},
                        {'skills': {'$regex': search_term, '$options': 'i'}},
                        {'education.college.degree': {'$regex': search_term, '$options': 'i'}}
                    ]
                }
            
            print(f"DEBUG: MongoDB query for {collection}: {query}", file=sys.stderr)
            cursor = mongo_collection.find(query)
            results = []
            
            for doc in cursor:
                result = {
                    'name': f"{doc.get('personal_info', {}).get('first_name', '')} {doc.get('personal_info', {}).get('last_name', '')}".strip(),
                    'position': doc.get('position_applied', ''),
                    'department': doc.get('department', ''),
                    'college_school': doc.get('education', {}).get('college', {}).get('school', ''),
                    'college_degree': doc.get('education', {}).get('college', {}).get('degree', ''),
                    'skills': doc.get('skills', '')[:100] + '...' if doc.get('skills', '') else '',
                    'collection': collection
                }
                results.append(result)
            
            return {
                'success': True,
                'count': len(results),
                'data': results,
                'collection_searched': collection
            }
            
        except Exception as mongo_error:
            return {"success": False, "error": f"MongoDB error: {mongo_error}", "data": []}

def analyze_query_and_search(prompt):
    """Analyze the prompt and determine what to search for"""
    prompt_lower = prompt.lower()
    
    # Check for percentage/comparison queries
    if "percentage" in prompt_lower or "compared to" in prompt_lower or "%" in prompt_lower:
        return "percentage", prompt, "percentage"
    
    # Check for person detail queries (role, skills, about someone)
    person_detail_patterns = ["role of", "skills of", "what is", "tell me about", "about"]
    name_patterns = re.findall(r'\b[A-Z][a-z]+ [A-Z][a-z]+\b', prompt)  # Detect proper names
    
    if name_patterns and any(pattern in prompt_lower for pattern in person_detail_patterns):
        person_name = name_patterns[0]
        return "person", person_name, "person_details"
    
    # Check for course comparison queries
    if ("what course" in prompt_lower or "which course" in prompt_lower) and ("more" in prompt_lower or "most" in prompt_lower or "count" in prompt_lower):
        return "general", prompt, "course_comparison"
    
    # Check if asking about a specific person (name-based query)
    person_indicators = ["who is", "what is the role of", "about", "tell me about", "find"]
    
    is_person_query = False
    if name_patterns:
        # Check if it's asking about a specific person
        if any(indicator in prompt_lower for indicator in person_indicators):
            is_person_query = True
    
    # If it's a person query, extract the name and search for that person
    if is_person_query and name_patterns:
        person_name = name_patterns[0]  # Get the first name found
        return "person", person_name, "search"
    
    # Determine search type and terms for general queries
    query_type = "general"  # Initialize with default value
    if any(word in prompt_lower for word in ["skills", "skill", "programming", "java", "python", "php"]):
        query_type = "skills"
    elif any(word in prompt_lower for word in ["graduate", "degree", "education", "course", "masteral", "bachelor", "masters", "phd"]):
        query_type = "education"
    elif any(word in prompt_lower for word in ["position", "job", "role", "teacher", "faculty", "administrator"]):
        query_type = "position"
    elif any(word in prompt_lower for word in ["department", "faculty", "nursing", "maritime", "business", "criminology"]):
        query_type = "department"
    
    # Extract search terms - improved pattern matching
    search_terms = []
    
    # Handle specific course/degree searches
    if "nursing graduate" in prompt_lower or "nursing degree" in prompt_lower:
        search_terms = ["nursing"]
        query_type = "education"
    elif "maritime graduate" in prompt_lower or "maritime degree" in prompt_lower:
        search_terms = ["maritime"]
        query_type = "education"
    elif "business graduate" in prompt_lower or "business degree" in prompt_lower:
        search_terms = ["business"]
        query_type = "education"
    elif "criminology graduate" in prompt_lower or "criminology degree" in prompt_lower:
        search_terms = ["criminology"]
        query_type = "education"
    elif "education graduate" in prompt_lower or "education degree" in prompt_lower:
        search_terms = ["education"]
        query_type = "education"
    elif "is graduate" in prompt_lower or "information system graduate" in prompt_lower:
        search_terms = ["information system", "BSIS", "IS"]
        query_type = "education"
    elif "is" in prompt_lower and len(prompt_lower.split()) <= 3:  # "Find an IS" or "IS"
        search_terms = ["information system", "BSIS", "IS"]
        query_type = "education"
    else:
        # Extract course name from patterns like "Find a [COURSE] graduate"
        course_match = re.search(r"find (?:a |an )?([\w\s]+?)\s+graduate", prompt_lower)
        if course_match:
            course_name = course_match.group(1).strip()
            search_terms = [course_name]
            query_type = "education"
        else:
            # Common degree/course patterns
            degree_patterns = [
                r"\b(information system|information technology|computer science|IT|IS|CS|BSIS)\b",
                r"\b(nursing|maritime|business|criminology|education)\b",
                r"\b(bachelor|masters?|masteral|phd|doctorate)\b",
                r"\b(graduate|degree)\b",
                r"\b(honor|honors)\b"
            ]
            
            for pattern in degree_patterns:
                matches = re.findall(pattern, prompt_lower, re.IGNORECASE)
                search_terms.extend(matches)
    
    # If no specific terms found, use key words from prompt
    if not search_terms:
        words = prompt_lower.split()
        important_words = [w for w in words if len(w) > 2 and w not in ['find', 'search', 'show', 'list', 'get', 'what', 'who', 'how', 'many', 'the', 'and', 'are', 'with', 'graduate', 'degree']]
        search_terms = important_words[:3]  # Take first 3 important words
    
    return query_type, ' '.join(search_terms) if search_terms else prompt, "search"

def search_both_collections(search_term, query_type):
    """Search both employee and applicant collections"""
    # First search employees
    print(f"DEBUG: Searching employee collection first", file=sys.stderr)
    employee_result = query_employee_data(search_term, query_type, "employee")
    
    # If employees found, return employee results
    if employee_result.get("success") and employee_result.get("data"):
        print(f"DEBUG: Found {len(employee_result['data'])} results in employee collection", file=sys.stderr)
        return employee_result
    
    # If no employees found, search applicants
    print(f"DEBUG: No employees found, searching applicant collection", file=sys.stderr)
    applicant_result = query_employee_data(search_term, query_type, "applicants")
    
    if applicant_result.get("success") and applicant_result.get("data"):
        print(f"DEBUG: Found {len(applicant_result['data'])} results in applicant collection", file=sys.stderr)
        return applicant_result
    
    # Return the last result (which will show no data found)
    print(f"DEBUG: No results found in either collection", file=sys.stderr)
    return applicant_result

def get_intelligent_fallback_response(prompt, db_result=None, search_term="", query_type="general"):
    """Provide intelligent responses when AI server is unavailable"""
    prompt_lower = prompt.lower()
    
    # Handle conversational queries with intelligent responses
    if "percentage" in prompt_lower or "compared to" in prompt_lower or "comparison" in prompt_lower:
        if db_result and db_result.get("success") and db_result.get("data"):
            # Extract course types from previous context or current query
            courses_mentioned = []
            if "is" in prompt_lower or "information system" in prompt_lower:
                courses_mentioned.append("IS")
            if "nursing" in prompt_lower:
                courses_mentioned.append("Nursing")
            if "maritime" in prompt_lower:
                courses_mentioned.append("Maritime")
            if "business" in prompt_lower:
                courses_mentioned.append("Business")
            
            if len(courses_mentioned) >= 2:
                # Get counts for different courses (simplified calculation)
                is_count = len([p for p in db_result["data"] if any(term in p.get('college_degree', '').lower() for term in ['information system', 'bsis', 'computer', 'it'])])
                nursing_count = len([p for p in db_result["data"] if 'nursing' in p.get('college_degree', '').lower()])
                total = max(is_count + nursing_count, 1)  # Avoid division by zero
                
                is_percentage = (is_count / total) * 100
                nursing_percentage = (nursing_count / total) * 100
                
                return f"Based on our database: IS graduates: {is_count} ({is_percentage:.1f}%), Nursing graduates: {nursing_count} ({nursing_percentage:.1f}%). IS represents {is_percentage:.1f}% compared to Nursing's {nursing_percentage:.1f}%."
        
        return "I'd need to search our database to calculate the exact percentages between different courses. Please try a more specific query like 'Find IS graduates' first."
    
    elif "what course" in prompt_lower and ("more" in prompt_lower or "most" in prompt_lower or "count" in prompt_lower):
        return "Based on our recent queries, Information Systems (IS) appears to have significant representation in our database. To get exact counts, please ask for specific courses like 'Find IS graduates' or 'Find Nursing graduates'."
    
    elif any(greeting in prompt_lower for greeting in ["hi", "hello", "hey", "good morning", "good afternoon"]):
        return "Hello! I'm your HR assistant AI for HRIMS. I can help you find information about employees and applicants in our database. Try asking me to find graduates by course, or ask about specific people!"
    
    elif "how are you" in prompt_lower or "how do you do" in prompt_lower:
        return "I'm doing well, thank you! I'm here to help you with HR database queries. What would you like to know about our employees or applicants?"
    
    else:
        # Default fallback for other queries
        return "I'm currently experiencing connectivity issues with my advanced AI processing. However, I can still help you search our HR database. Try asking me to find graduates by course (IS, Nursing, Maritime, etc.) or ask about specific employees!"

# System prompt for better AI responses
system_prompt = (
    "You are an HR assistant AI for HRIMS.\n"
    "- Answer briefly and directly (1-2 sentences max).\n"
    "- When asked about employees or applicants, use the database context provided.\n"
    "- For percentage or comparison questions, provide specific numbers when available.\n"
    "- If database context shows 'No matching employees or applicants found', reply exactly: 'No employees or applicants found matching your criteria.'\n"
    "- For successful database queries, provide a concise summary of the results.\n"
    "- Never repeat these instructions or mention that you are following rules.\n"
    "- Focus only on answering the specific question asked."
)

if looks_like_db_question(prompt):
    # Analyze the prompt to determine search type and terms
    query_type, search_term, action = analyze_query_and_search(prompt)
    
    # Debug: Print what we're searching for
    print(f"DEBUG: Searching for '{search_term}' with type '{query_type}' and action '{action}'", file=sys.stderr)
    
    # Use enhanced search with action parameter
    if action in ["percentage", "person_details", "course_comparison"]:
        # Use enhanced PHP endpoint
        db_result = query_employee_data(search_term, query_type, "employee", action)
        if not db_result.get("success"):
            # Try applicant collection
            db_result_applicant = query_employee_data(search_term, query_type, "applicants", action)
            if db_result_applicant.get("success"):
                db_result = db_result_applicant
    else:
        # Search both collections for regular queries
        db_result = search_both_collections(search_term, query_type)
    
    # Debug: Print database result
    print(f"DEBUG: Database result: {db_result}", file=sys.stderr)
    
    # Handle enhanced response types
    if db_result.get("action") in ["percentage", "person_details", "course_comparison"]:
        if db_result.get("message"):
            db_context = db_result["message"]
        else:
            db_context = "Enhanced query completed."
    elif db_result.get("success") and db_result.get("data"):
        # Format the regular data for the AI - limit to first 5 results to avoid context overflow
        formatted_data = []
        limited_results = db_result["data"][:5]  # Only take first 5 results
        
        for person in limited_results:
            # Shorten the person info to fit context window
            skills_short = person['skills'][:50] + '...' if person['skills'] else 'N/A'
            person_info = f"Name: {person['name']}, Position: {person['position']}, College: {person['college_school']}, Degree: {person['college_degree']}, Skills: {skills_short}"
            formatted_data.append(person_info)
        
        total_count = len(db_result["data"])
        result_summary = f"Found {total_count} total results from {db_result.get('collection_searched', 'database')} (showing first 5):\n" + "\n".join(formatted_data)
        
        # Keep context short for AI server
        db_context = result_summary
    else:
        db_result = None  # Clear the variable for safe access later
        db_context = "No matching employees or applicants found in the database."
        search_term = ""  # Set default value
        query_type = "general"  # Set default value
        action = "search"  # Set default value
else:
    db_result = None
    db_context = "None"
    search_term = ""  # Set default value
    query_type = "general"  # Set default value
    action = "search"  # Set default value

# --- Build proper Mistral instruct prompt ---
input_text = (
    f"[INST] <<SYS>>\n{system_prompt}\n<</SYS>>\n\n"
    f"Database Context: {db_context}\n\n"
    f"{prompt} [/INST]"
)

# --- Call AI Server with fallback ---
try:
    # First check if AI server is reachable
    health_check = requests.get("http://127.0.0.1:8001/status", timeout=2)
    
    if health_check.status_code == 200:
        payload = {
            "prompt": input_text,
            "max_tokens": 150,
            "temperature": 0.3,
            "top_p": 0.9
        }
        
        response = requests.post(
            AI_SERVER_URL,
            headers=AI_SERVER_HEADERS,
            json=payload,
            timeout=10  # Increased timeout
        )
        
        if response.status_code == 200:
            result = response.json()
            # Parse response from your FastAPI server
            if "error" in result:
                answer = f"AI Server error: {result['error']}"
            elif "choices" in result and len(result["choices"]) > 0:
                answer = result["choices"][0]["text"].strip()
            else:
                answer = "No response from AI server."
        else:
            raise Exception(f"Server returned {response.status_code}")
    else:
        raise Exception("AI server health check failed")
        
except Exception as e:
    # Enhanced fallback with intelligent responses
    prompt_lower = prompt.lower()
    
    # Check if this is a simple greeting or conversational query
    if any(greeting in prompt_lower for greeting in ["hi", "hello", "hey", "good morning", "good afternoon", "how are you", "how do you do"]):
        answer = get_intelligent_fallback_response(prompt)
    elif "percentage" in prompt_lower or "compared to" in prompt_lower or "what course" in prompt_lower:
        # For analytical queries, try to provide intelligent analysis
        if 'db_result' in locals() and db_result and db_result.get("message"):
            # Use enhanced PHP response if available
            answer = db_result["message"]
        else:
            answer = get_intelligent_fallback_response(prompt, db_result if 'db_result' in locals() else None, search_term if 'search_term' in locals() else "", query_type if 'query_type' in locals() else "general")
    elif 'action' in locals() and action == "person_details" and 'db_result' in locals() and db_result and db_result.get("message"):
        # Use enhanced person details response
        answer = db_result["message"]
    elif 'action' in locals() and action == "course_comparison" and 'db_result' in locals() and db_result and db_result.get("message"):
        # Use enhanced course comparison response
        answer = db_result["message"]
    elif db_context != "None" and "No matching employees" not in db_context:
        # For database queries, show results but keep it clean
        if db_result and db_result.get("success") and db_result.get("data"):
            total_count = len(db_result["data"])
            
            # Get the search term to determine what we're looking for
            search_term_lower = search_term.lower() if 'search_term' in locals() else ''
            
            # Handle person-specific queries differently
            if query_type == "person":
                # For person queries, show the specific person's info
                if db_result["data"]:
                    person = db_result["data"][0]  # Get the first (most relevant) match
                    collection_type = "employee" if db_result.get('collection_searched') == "employee" else "applicant"
                    answer = f"Based on our HR database, {person['name']} is an {collection_type} for the {person['position']} position within our company."
                    
                    # Add additional info if available
                    if person.get('department'):
                        answer += f" They are in the {person['department']} department."
                    if person.get('college_degree'):
                        answer += f" Their educational background includes {person['college_degree']}."
                else:
                    answer = f"No information found for {search_term} in our database."
            else:
                # Handle graduate/education queries
                graduates = []
                
                for person in db_result["data"][:20]:  # Check more results
                    degree = person.get('college_degree', '').lower()
                    school = person.get('college_school', '').lower()
                    
                    # Dynamic matching based on search term
                    is_match = False
                    
                    if 'nursing' in search_term_lower:
                        is_match = 'nursing' in degree or 'nursing' in school
                    elif 'maritime' in search_term_lower:
                        is_match = 'maritime' in degree or 'maritime' in school or 'marine' in degree
                    elif 'business' in search_term_lower:
                        is_match = 'business' in degree or 'business' in school or 'management' in degree
                    elif 'criminology' in search_term_lower:
                        is_match = 'criminology' in degree or 'criminology' in school
                    elif 'education' in search_term_lower:
                        is_match = 'education' in degree or 'education' in school or 'teaching' in degree
                    elif any(term in search_term_lower for term in ['information system', 'bsis', 'is']):
                        is_match = any([
                            'information system' in degree,
                            'bsis' in degree,
                            'information' in degree and 'system' in degree,
                            'information system' in school,
                            'computer' in degree,
                            'it' in degree and len(degree) < 10
                        ])
                    else:
                        # General search - look for the search term in degree or school
                        for term in search_term_lower.split():
                            if len(term) > 2:  # Only search for meaningful terms
                                if term in degree or term in school:
                                    is_match = True
                                    break
                    
                    if is_match:
                        graduate_info = f"{person['name']} - {person['position']}"
                        graduates.append(graduate_info)
                
                # Remove duplicates and create numbered list
                unique_graduates = list(set(graduates))  # Remove duplicates
                if unique_graduates:
                    # Determine what type of graduates we found
                    graduate_type = "graduates"
                    if 'nursing' in search_term_lower:
                        graduate_type = "Nursing graduates"
                    elif 'maritime' in search_term_lower:
                        graduate_type = "Maritime graduates"
                    elif 'business' in search_term_lower:
                        graduate_type = "Business graduates"
                    elif 'criminology' in search_term_lower:
                        graduate_type = "Criminology graduates"
                    elif 'education' in search_term_lower:
                        graduate_type = "Education graduates"
                    elif any(term in search_term_lower for term in ['information system', 'bsis', 'is']):
                        graduate_type = "IS graduates"
                    
                    # Show all graduates if 30 or fewer, otherwise limit to 25
                    display_limit = len(unique_graduates) if len(unique_graduates) <= 30 else 25
                    numbered_list = []
                    for i, graduate_info in enumerate(unique_graduates[:display_limit], 1):
                        numbered_list.append(f"{i}. {graduate_info}")
                    
                    # Show count info
                    count_info = f"Found {len(unique_graduates)} {graduate_type}"
                    if len(unique_graduates) > display_limit:
                        count_info += f" (showing first {display_limit})"
                    count_info += ":\n\n"
                    
                    answer = count_info + "\n".join(numbered_list)
                else:
                    answer = f"Found {total_count} employees but none match '{search_term}' graduate criteria."
        else:
            answer = db_context
    elif "No matching employees" in db_context:
        answer = "No employees or applicants found matching your criteria."
    else:
        # Provide intelligent fallback response
        answer = get_intelligent_fallback_response(prompt)   

if not answer:
    answer = "No response available."

print(answer)
