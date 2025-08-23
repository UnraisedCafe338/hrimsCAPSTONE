from llama_cpp import Llama
import sys

if len(sys.argv) < 2:
    print("Please provide a prompt")
    sys.exit(1)

prompt = sys.argv[1]

# Load model with GPU acceleration
llm = Llama(
    model_path="../assets/ai/mistral-7b-instruct-v0.2.Q4_K_M.gguf",
    n_gpu_layers=-1,   # Offload all layers to GPU
    n_threads=8,       # Adjust based on CPU cores
    n_batch=1024,      # Larger batch size = faster on GPU
    use_mlock=True,
    verbose=True
)

# Generate response
output = llm(
    prompt,
    max_tokens=512,
    temperature=0.7,
    top_p=0.9
)

print(output["choices"][0]["text"])
