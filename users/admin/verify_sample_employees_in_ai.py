import sys
from pymongo import MongoClient

def verify_sample_employees():
    # Connect to MongoDB
    client = MongoClient("mongodb://localhost:27017/")
    db = client["hrims_db"]
    
    # Check employees collection
    employees_collection = db["employees"]
    
    print("Checking sample employees in employees collection...")
    
    # Find our sample employees
    sample_employees = list(employees_collection.find({
        "personal_info.email": {"$regex": ".*@company.com$"}
    }))
    
    print(f"Found {len(sample_employees)} sample employees")
    
    for emp in sample_employees:
        first_name = emp.get("personal_info", {}).get("first_name", "N/A")
        last_name = emp.get("personal_info", {}).get("last_name", "N/A")
        email = emp.get("personal_info", {}).get("email", "N/A")
        
        college_degree = emp.get("education", {}).get("college", {}).get("degree", "N/A")
        college_school = emp.get("education", {}).get("college", {}).get("school", "N/A")
        
        print(f"  {first_name} {last_name} ({email})")
        print(f"    College: {college_degree} from {college_school}")
        
        # Check if they have masteral
        masteral = emp.get("education", {}).get("masteral", {})
        if masteral:
            masteral_degree = masteral.get("degree", "")
            masteral_school = masteral.get("school", "")
            if masteral_degree or masteral_school:
                print(f"    Masteral: {masteral_degree} from {masteral_school}")
                
        # Check if they have doctoral
        doctoral = emp.get("education", {}).get("doctoral", {})
        if doctoral:
            doctoral_degree = doctoral.get("degree", "")
            doctoral_school = doctoral.get("school", "")
            if doctoral_degree or doctoral_school:
                print(f"    Doctoral: {doctoral_degree} from {doctoral_school}")
        
        print()
    
    # Test specific queries
    print("Testing specific queries...")
    
    # Test Information Technology query
    it_employees = list(employees_collection.find({
        "personal_info.email": {"$regex": ".*@company.com$"},
        "$or": [
            {"education.college.degree": {"$regex": "Information Technology", "$options": "i"}},
            {"education.masteral.degree": {"$regex": "Information Technology", "$options": "i"}},
            {"education.doctoral.degree": {"$regex": "Information Technology", "$options": "i"}}
        ]
    }))
    
    print(f"Found {len(it_employees)} sample employees with Information Technology degrees:")
    for emp in it_employees:
        first_name = emp.get("personal_info", {}).get("first_name", "N/A")
        last_name = emp.get("personal_info", {}).get("last_name", "N/A")
        degree = emp.get("education", {}).get("college", {}).get("degree", "N/A")
        print(f"  {first_name} {last_name} - {degree}")
    
    # Test Computer Science query
    cs_employees = list(employees_collection.find({
        "personal_info.email": {"$regex": ".*@company.com$"},
        "$or": [
            {"education.college.degree": {"$regex": "Computer Science", "$options": "i"}},
            {"education.masteral.degree": {"$regex": "Computer Science", "$options": "i"}},
            {"education.doctoral.degree": {"$regex": "Computer Science", "$options": "i"}}
        ]
    }))
    
    print(f"\nFound {len(cs_employees)} sample employees with Computer Science degrees:")
    for emp in cs_employees:
        first_name = emp.get("personal_info", {}).get("first_name", "N/A")
        last_name = emp.get("personal_info", {}).get("last_name", "N/A")
        degree = emp.get("education", {}).get("college", {}).get("degree", "N/A")
        print(f"  {first_name} {last_name} - {degree}")

if __name__ == "__main__":
    verify_sample_employees()