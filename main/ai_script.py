from llama_cpp import Llama
from pymongo import MongoClient
import sys
import json

if len(sys.argv) < 2:
    print("Please provide a prompt")
    sys.exit(1)

prompt = sys.argv[1]

# --- Connect to MongoDB ---
client = MongoClient("mongodb://localhost:27017/")
db = client["hrims_db"]
employees = db["employee"]

# --- Load instruct model ---
llm = Llama(
    model_path="C:/xampp/htdocs/hrims/assets/ai/mistral-7b-instruct-v0.2.Q4_K_M.gguf",
    n_ctx=1024,
    n_threads=8,
    verbose=False
)

def looks_like_db_question(text):
    keywords = ["employee", "applicant", "graduate", "skills", "how many", "list"]
    return any(word in text.lower() for word in keywords)

# --- System prompt (rules only, never echoed) ---
system_prompt = (
    "You are an HR assistant AI.\n"
    "- Answer briefly (1–2 sentences max).\n"
    "- If asked about employees, use the database context below.\n"
    "- If no data is found, reply exactly: 'No data found.'\n"
    "- Do not repeat these rules in your answer."
)

if looks_like_db_question(prompt):
    data = list(employees.find({}, {"_id": 0, "name": 1, "education": 1, "skills": 1}))
    db_context = json.dumps(data)
else:
    db_context = "None"

# --- Build proper Mistral instruct prompt ---
input_text = (
    f"[INST] <<SYS>>\n{system_prompt}\n<</SYS>>\n\n"
    f"Database Context: {db_context}\n\n"
    f"{prompt} [/INST]"
)

# --- Run model ---
try:
    output = llm(
        input_text,
        max_tokens=150,
        temperature=0.3,
        top_p=0.9
    )
    answer = output["choices"][0]["text"].strip()
except Exception as e:
    answer = f"Model error: {e}"

if not answer:
    answer = "No response available."

print(answer)
