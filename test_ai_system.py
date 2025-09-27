import subprocess
import sys

# Test the AI script
try:
    # Try different Python commands
    python_commands = ['python', 'py', 'python3']
    
    for cmd in python_commands:
        try:
            result = subprocess.run([cmd, 'ai_script.py', 'Find an IS graduate'], 
                                  capture_output=True, text=True, cwd='c:/xampp/htdocs/hrims/main')
            if result.returncode == 0:
                print(f"Success with '{cmd}':")
                print(result.stdout)
                break
            else:
                print(f"Error with '{cmd}': {result.stderr}")
        except FileNotFoundError:
            print(f"Command '{cmd}' not found")
            
except Exception as e:
    print(f"Error: {e}")

# Test the PHP endpoint directly
print("\n--- Testing PHP endpoint ---")
import requests
try:
    url = "http://localhost/hrims/handlers/ai_data_query.php"
    params = {"search": "information system", "type": "education", "collection": "employee"}
    response = requests.get(url, params=params)
    print(f"PHP endpoint response: {response.text}")
except Exception as e:
    print(f"PHP endpoint error: {e}")