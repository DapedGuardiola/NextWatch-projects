import json
import math

# Read cleaned JSON
with open('data/processed/actor.json', 'r', encoding='utf-8') as f:
    movies = json.load(f)

def replace_nan(obj):
    """Recursively replace NaN with empty string"""
    if isinstance(obj, dict):
        return {k: replace_nan(v) for k, v in obj.items()}
    elif isinstance(obj, list):
        return [replace_nan(item) for item in obj]
    elif isinstance(obj, float) and math.isnan(obj):
        return None
    else:
        return obj

# Replace all NaN values
cleaned_actors = [replace_nan(movie) for movie in movies]

# Save
with open('data/processed/actors_fix.json', 'w', encoding='utf-8') as f:
    json.dump(cleaned_actors, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Replaced all NaN values with empty strings")
print(f"[SUCCESS] Total movies: {len(cleaned_actors)}")
