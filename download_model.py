import os
import sys
from sentence_transformers import SentenceTransformer

model_name = 'paraphrase-multilingual-MiniLM-L12-v2'
target_dir = os.path.join(os.path.dirname(__file__), 'models', model_name)

print(f"Downloading model '{model_name}'...")
model = SentenceTransformer(model_name)

print(f"Saving model copy to local folder: {target_dir}...")
model.save(target_dir)

print("\n[SUCCESS] Model berhasil diunduh dan tersimpan!")
print(f"1. Tersedia di cache global Hugging Face (default SentenceTransformer('{model_name}'))")
print(f"2. Tersedia di folder lokal: {target_dir}")

