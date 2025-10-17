import re

# Test the individual parts
prompt = "Find all BS Information Technology graduates"
prompt_lower = prompt.lower()

print(f"Prompt: '{prompt}'")
print(f"Prompt lower: '{prompt_lower}'")

# Test pattern 0 - let's make it simpler
pattern0 = r'(find\s+all)\s+(.+?)\s+(graduates)'
match0 = re.search(pattern0, prompt_lower)
print(f"\nPattern 0: {pattern0}")
if match0:
    print(f"Match found: '{match0.group(0)}'")
    print(f"Groups: {match0.groups()}")
    print(f"Group 1 (find all): '{match0.group(1)}'")
    print(f"Group 2 (middle part): '{match0.group(2)}'")
    print(f"Group 3 (graduates): '{match0.group(3)}'")
else:
    print("No match")

# Test the full function
def analyze_list_query(prompt):
    """Analyze if the prompt is asking to list all graduates of a specific course"""
    prompt_lower = prompt.lower()
    
    # Simpler pattern that should work
    pattern = r'(find\s+all|list\s+me\s+all|show\s+me\s+all|give\s+me\s+all)\s+(.+?)\s+(graduates?)'
    match = re.search(pattern, prompt_lower)
    if match:
        print(f"\nFunction - Matched pattern: {pattern}")
        print(f"Full match: '{match.group(0)}'")
        course_name = match.group(2).strip()
        print(f"Extracted course: '{course_name}'")
        return True, course_name
    
    return False, ""

print(f"\n{'='*50}")
print("Testing full function:")
is_list, course = analyze_list_query(prompt)
print(f"Is list query: {is_list}")
print(f"Extracted course: '{course}'")