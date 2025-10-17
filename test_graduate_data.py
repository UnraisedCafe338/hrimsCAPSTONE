import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), 'users', 'admin'))

from pymongo import MongoClient
import re

def test_graduate_data():
    """Test to see what graduate data is actually in the database"""
    try:
        # Connect to MongoDB
        client = MongoClient("mongodb://localhost:27017/")
        db = client["hrims_db"]
        
        print("Checking for BSIS graduates...")
        
        # Check both collections
        for collection_name in ["employee", "applicants"]:
            print(f"\n--- Checking {collection_name} collection ---")
            collection = db[collection_name]
            
            # Build query for BSIS graduates
            query = {
                '$or': [
                    {'education.college.degree': {'$regex': 'bsis', '$options': 'i'}},
                    {'education.college.degree': {'$regex': 'information system', '$options': 'i'}},
                ]
            }
            
            cursor = collection.find(query)
            found_count = 0
            
            for doc in cursor:
                found_count += 1
                print(f"\nDocument {found_count}:")
                print(f"  Personal Info: {doc.get('personal_info', {})}")
                print(f"  Education: {doc.get('education', {})}")
                print(f"  Position: {doc.get('position_applied', 'N/A')}")
                
                # Try to construct full name
                personal_info = doc.get('personal_info', {})
                first_name = personal_info.get('first_name', '')
                middle_name = personal_info.get('middle_name', '')
                last_name = personal_info.get('last_name', '')
                full_name = f"{first_name} {middle_name} {last_name}".strip()
                print(f"  Constructed Name: '{full_name}'")
                
            if found_count == 0:
                print("  No BSIS graduates found in this collection")
                
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    test_graduate_data()