#!/usr/bin/env python3
import sys
import os
import re

# Add the admin directory to the path so we can import ai_script modules
sys.path.append(os.path.join(os.path.dirname(__file__), 'users', 'admin'))

# Define the functions we want to test directly here
def is_follow_up_question(prompt_text):
    """Detect if the current question is a follow-up to previous conversation."""
    follow_up_indicators = [
        # English follow-up patterns
        r"^(what about|how about|and|also|can you|what if|tell me more|continue|go on)",
        r"(more|another|other|else|next|expand|further|additional)",
        r"(that|this|it|they|them)\s",
        r"^(show|list|find)\s+(more|other|another)",
        r"(compare|versus|vs|difference)",
        r"(previous|earlier|before|above)",
        
        # Filipino/Taglish follow-up patterns
        r"^(paano|ano|at|pano|saka|tapos)",
        r"(pa|din|rin|naman|lang)",
        r"(yan|yun|iyan|iyon)"
    ]
    
    prompt_lower = prompt_text.lower()
    for pattern in follow_up_indicators:
        if re.match(pattern, prompt_lower, re.IGNORECASE):
            return True
    return False

def extract_main_question(prompt_text):
    """Extract the main question from a contextual prompt."""
    # Check if this is a contextual prompt with conversation history
    if "Current follow-up question:" in prompt_text:
        # Extract the main question part
        question_match = re.search(r"Current follow-up question:\s*(.*)", prompt_text)
        if question_match:
            return question_match.group(1)
    elif "Context: We have discussed" in prompt_text:
        # Extract the actual question part
        question_match = re.search(r"^(.*?)(?:\s*\(Context: We have discussed.*)", prompt_text)
        if question_match:
            return question_match.group(1)
    return prompt_text

# Test cases
test_cases = [
    "Princes Lyka",
    "What about Princes Lyka",
    "Tell me more about Princes Lyka",
    "How about Princes Lyka",
    "Princes Lyka M Santos",
    "What are the skills of Princes Lyka M Santos"
]

print("Testing follow-up question detection:")
print("=" * 50)

for test_case in test_cases:
    is_follow_up = is_follow_up_question(test_case)
    main_question = extract_main_question(test_case)
    
    print(f"Input: '{test_case}'")
    print(f"Is follow-up: {is_follow_up}")
    print(f"Main question: '{main_question}'")
    print("-" * 30)