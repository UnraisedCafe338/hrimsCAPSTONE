import requests
import sys

def test_ai_server():
    """Test if AI server is reachable"""
    try:
        response = requests.get("http://127.0.0.1:8000/status", timeout=5)
        print(f"AI Server Status: {response.status_code}")
        if response.status_code == 200:
            print(f"Response: {response.json()}")
            return True
    except Exception as e:
        print(f"AI Server Error: {e}")
    return False

def test_php_endpoint():
    """Test PHP database endpoint"""
    try:
        url = "http://localhost/hrims/handlers/ai_data_query.php"
        params = {"search": "information system", "type": "education", "collection": "employee"}
        response = requests.get(url, params=params, timeout=10)
        print(f"PHP Endpoint Status: {response.status_code}")
        print(f"Response: {response.text}")
        return response.status_code == 200
    except Exception as e:
        print(f"PHP Endpoint Error: {e}")
    return False

def test_ai_script():
    """Test the AI script query functionality"""
    import subprocess
    try:
        # Test with different Python commands
        for cmd in ['python', 'py', 'python3']:
            try:
                result = subprocess.run([cmd, 'ai_script.py', 'Find an IS graduate'], 
                                      capture_output=True, text=True, timeout=30,
                                      cwd='c:/xampp/htdocs/hrims/main')
                if result.returncode == 0:
                    print(f"AI Script Success with '{cmd}':")
                    print(f"Output: {result.stdout}")
                    return True
                else:
                    print(f"AI Script Error with '{cmd}': {result.stderr}")
            except FileNotFoundError:
                print(f"Command '{cmd}' not found")
            except subprocess.TimeoutExpired:
                print(f"Command '{cmd}' timed out")
    except Exception as e:
        print(f"Error testing AI script: {e}")
    return False

if __name__ == "__main__":
    print("=== Testing AI System ===")
    print("\n1. Testing AI Server...")
    ai_server_ok = test_ai_server()
    
    print("\n2. Testing PHP Endpoint...")
    php_ok = test_php_endpoint()
    
    print("\n3. Testing AI Script...")
    script_ok = test_ai_script()
    
    print(f"\n=== Summary ===")
    print(f"AI Server: {'✓' if ai_server_ok else '✗'}")
    print(f"PHP Endpoint: {'✓' if php_ok else '✗'}")
    print(f"AI Script: {'✓' if script_ok else '✗'}")