import pandas as pd
import json

# Read tmdb_cleaned.json to get valid movie IDs
with open('data/processed/tmdb_cleaned.json', 'r', encoding='utf-8') as f:
    cleaned_movies = json.load(f)

# Extract movie IDs from cleaned data
valid_movie_ids = set(movie['id'] for movie in cleaned_movies)
print(f"[INFO] Found {len(valid_movie_ids)} valid movie IDs from tmdb_cleaned.json")

# Read credits CSV
credits_df = pd.read_csv('data/raw/tmdb_5000_credits.csv')
print(f"[INFO] Loaded {len(credits_df)} total credits records")

# Filter credits by valid movie IDs
filtered_credits = credits_df[credits_df['movie_id'].isin(valid_movie_ids)].copy()
print(f"[INFO] Filtered to {len(filtered_credits)} matching credits records")

# Parse cast and crew JSON strings
def parse_cast(cast_str):
    try:
        return json.loads(cast_str)
    except:
        return []

def parse_crew(crew_str):
    try:
        return json.loads(crew_str)
    except:
        return []

filtered_credits['cast'] = filtered_credits['cast'].apply(parse_cast)
filtered_credits['crew'] = filtered_credits['crew'].apply(parse_crew)

# Convert to list of dictionaries
credits_list = filtered_credits.to_dict(orient='records')

# Save as JSON
with open('data/processed/movie_credits.json', 'w', encoding='utf-8') as f:
    json.dump(credits_list, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Extracted {len(credits_list)} credits records to JSON")
print(f"[SUCCESS] Output saved to: data/processed/movie_credits.json")
