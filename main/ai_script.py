from llama_cpp import Llama
from pymongo import MongoClient
import sys
import re
import json

if len(sys.argv) < 2:
    print("Please provide a prompt")
    sys.exit(1)

prompt = sys.argv[1]

# --- Connect to MongoDB ---
client = MongoClient("mongodb://localhost:27017/")
db = client["hrims_db"]

# --- Degree mapping for common names ---
degree_map = {
    "Information Systems": "BSIS",
    "Computer Engineering": "BSCpE",
    "Information Technology": "BSIT",
    "Business Administration": "BSBA",
    "Accounting": "BSA"
}

def get_graduates(degree):

    """Return a dictionary of employees and applicants by degree."""
    query = {"education.college.degree": {"$regex": degree, "$options": "i"}}
    print(f"[DEBUG] Detected degree from prompt: {degree}")

    print(f"[DEBUG] Querying employees with: {query}")
    employees = list(db.employee.find(query, {"name": 1, "_id": 0}))
    print(f"[DEBUG] Employees result: {employees}")

    print(f"[DEBUG] Querying applicants with: {query}")
    applicants = list(db.applicants.find(query, {"name": 1, "_id": 0}))
    print(f"[DEBUG] Applicants result: {applicants}")

    return {
        "employees": [e["name"] for e in employees],
        "applicants": [a["name"] for a in applicants]
    }


# --- Try to detect degree/course in the prompt ---
degree = None

# First try regex for codes like BSIS, BSCpE
match = re.search(r"\b(BS\w+)\b", prompt, re.IGNORECASE)
if match:
    degree = match.group(1).upper()
else:
    # Try mapping from degree names in prompt
    for key, val in degree_map.items():
        if key.lower() in prompt.lower():
            degree = val
            break

# --- Query database ---
mongo_result = None
if degree:
    graduates = get_graduates(degree)
    if graduates["employees"] or graduates["applicants"]:
        result_lines = []
        if graduates["employees"]:
            result_lines.append(f"Employees ({len(graduates['employees'])}):")
            result_lines.extend(f"- {name}" for name in graduates["employees"])
        if graduates["applicants"]:
            result_lines.append(f"Applicants ({len(graduates['applicants'])}):")
            result_lines.extend(f"- {name}" for name in graduates["applicants"])
        mongo_result = "\n".join(result_lines)
    else:
        mongo_result = f"No data found for degree: {degree}"
else:
    mongo_result = "No recognizable degree mentioned in prompt"

# --- Load AI model only if DB didn't answer ---
llm = Llama(
    model_path="../assets/ai/mistral-7b-instruct-v0.2.Q4_K_M.gguf",
    n_gpu_layers=-1,
    n_threads=8,
    n_batch=1024,
    use_mlock=True,
    verbose=False
)

if mongo_result and "No data found" not in mongo_result:
    # Use DB results directly
    print(mongo_result)
else:
    # Fallback to AI reasoning
    final_prompt = f"User asked: {prompt}\nDatabase query result: {mongo_result}\nAnswer accordingly."
    output = llm(
        final_prompt,
        max_tokens=512,
        temperature=0.3,
        top_p=0.9
    )
    print(output["choices"][0]["text"].strip())
