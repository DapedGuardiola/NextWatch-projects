import json
import math

with open('data/processed/movie_fix.json', 'r', encoding='utf-8') as f:
    movies = json.load(f)

def clean_value(obj):
    if isinstance(obj, dict):
        return {k: clean_value(v) for k, v in obj.items()}
    
    elif isinstance(obj, list):
        return [clean_value(item) for item in obj]
    
    # handle NaN
    elif isinstance(obj, float) and math.isnan(obj):
        return None
    
    # 🔥 handle empty string
    elif isinstance(obj, str) and obj.strip() == "":
        return None
    
    else:
        return obj

cleaned_movies = [clean_value(movie) for movie in movies]

with open('data/processed/movies_ready.json', 'w', encoding='utf-8') as f:
    json.dump(cleaned_movies, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Cleaned empty string & NaN to null")