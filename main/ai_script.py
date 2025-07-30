import sys
import io
import fitz  # PyMuPDF
from llama_cpp import Llama
from docx import Document
import os

os.environ["LLAMA_LOG_LEVEL"] = "ERROR"
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

# --- Extractors ---
def extract_text_from_pdf(path):
    text = ""
    doc = fitz.open(path)
    for page in doc:
        text += page.get_text()
    return text.strip()

def extract_text_from_docx(path):
    doc = Document(path)
    return "\n".join([p.text for p in doc.paragraphs if p.text.strip()])

# --- Read full prompt from file ---
if len(sys.argv) < 2:
    print("No prompt file provided.")
    sys.exit(1)

with open(sys.argv[1], "r", encoding="utf-8") as f:
    prompt = f.read().strip()

# --- Check for optional resume file ---
if len(sys.argv) >= 3:
    file_path = os.path.join("../uploads", sys.argv[2].strip())
    if os.path.isfile(file_path):
        ext = os.path.splitext(file_path)[1].lower()
        if ext == ".pdf":
            doc_text = extract_text_from_pdf(file_path)
        elif ext == ".docx":
            doc_text = extract_text_from_docx(file_path)
        else:
            print("Unsupported file type.")
            sys.exit(1)

        system_prompt = "You are an HR AI assistant. Summarize this resume and highlight skills and recommend roles."
        prompt = f"{prompt}\n\n{doc_text}"
    else:
        print("File not found:", file_path)
        sys.exit(1)
else:
    system_prompt = "You are an AI assistant inside a Human Resources Management System. Respond clearly to questions or greetings."

# --- Prompt Construction ---
full_prompt = f"[INST] <<SYS>>\n{system_prompt}\n<</SYS>>\n\n{prompt} [/INST]"

# Optional: print for debugging
# print("=== DEBUG PROMPT START ===")
# print(full_prompt)
# print("=== DEBUG PROMPT END ===\n")

# --- Load the model ---
llm = Llama(
    model_path="../assets/ai/mistral-7b-instruct-v0.2.Q4_K_M.gguf",
    n_ctx=4096,
    n_threads=8,
    n_gpu_layers=20,
    verbose=False
)

# --- Generate response ---
response = llm(full_prompt, max_tokens=512, stop=["</s>"])
print(response["choices"][0]["text"].strip())
