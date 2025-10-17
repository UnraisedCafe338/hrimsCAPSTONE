import re

def is_follow_up_question(current_question):
    """Simulate the frontend follow-up question detection logic"""
    # Simulate chat session with existing messages
    chat_sessions = {
        'current': [
            {'sender': 'user', 'content': 'Hello', 'timestamp': '2025-01-01T10:00:00Z'},
            {'sender': 'ai', 'content': 'Hi there! How can I help you?', 'timestamp': '2025-01-01T10:00:05Z'}
        ]
    }
    current_session_id = 'current'
    
    # Only return True if there are messages in the session
    if not chat_sessions[current_session_id] or len(chat_sessions[current_session_id]) == 0:
        return False
    
    follow_up_indicators = [
        # English follow-up patterns - using word boundaries
        r"^(what about|how about|and|also|can you|what if|tell me more|continue|go on)\b",
        r"\b(more|another|other|else|next|expand|further|additional)\b",
        r"\b(that|this|it|they|them)\s",
        r"^(show|list|find)\s+(more|other|another)",
        r"\b(compare|versus|vs|difference)\b",
        
        # Filipino/Taglish follow-up patterns - using word boundaries
        r"^(paano|ano|at|pano|saka|tapos)\b",
        r"\b(pa|din|rin|naman|lang)\b",
        r"\b(yan|yun|iyan|iyon)\b"
    ]
    
    for i, pattern in enumerate(follow_up_indicators):
        if re.search(pattern, current_question.strip(), re.IGNORECASE):
            print(f"Matched pattern {i}: {pattern}")
            return True
    return False

# Test cases
test_cases = [
    "Princes Lyka",
    "Princes Lyka M Santos",
    "What about Princes Lyka",
    "Tell me more about Princes Lyka",
    "How about Princes Lyka"
]

print("Testing frontend follow-up question detection:")
print("=" * 50)

for test_case in test_cases:
    print(f"\nInput: '{test_case}'")
    is_follow_up = is_follow_up_question(test_case)
    print(f"Is follow-up: {is_follow_up}")
    print("-" * 30)