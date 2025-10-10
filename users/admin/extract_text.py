import sys
import fitz  # PyMuPDF
from docx import Document
import os

path = sys.argv[1]
ext = os.path.splitext(path)[1].lower()

if ext == ".pdf":
    doc = fitz.open(path)
    text = "\n".join([p.get_text() for p in doc])
elif ext == ".docx":
    doc = Document(path)
    text = "\n".join([p.text for p in doc.paragraphs if p.text.strip()])
else:
    text = ""

print(text.strip())
