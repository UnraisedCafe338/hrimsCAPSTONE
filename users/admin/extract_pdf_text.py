import sys
import fitz  # PyMuPDF

if len(sys.argv) < 2:
    print("No PDF file provided.")
    sys.exit(1)

path = sys.argv[1]
doc = fitz.open(path)
text = ""
for page in doc:
    text += page.get_text()
print(text.strip())
