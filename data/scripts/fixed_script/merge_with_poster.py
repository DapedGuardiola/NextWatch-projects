import pandas as pd
import json
import re

# Read poster results
poster_df = pd.read_csv('data/raw/poster_results.csv')

# Extract poster_path from URL
def extract_poster_path(url):
    """Extract poster_path from URL like https://image.tmdb.org/t/p/w500/fsdfsdfwqe13bxRGut2Yh118RyNa6ruXXCp.jpg"""
    match = re.search(r'/([^/]+\.jpg)$', str(url))
    if match:
        return match.group(1)
    return None

poster_df['poster_path'] = poster_df['poster_url'].apply(extract_poster_path)

# Create mapping from tmdb_id to poster_path
poster_map = dict(zip(poster_df['tmdb_id'], poster_df['poster_path']))

print(f"[INFO] Loaded {len(poster_map)} poster records")

# Read TMDB movies
tmdb_df = pd.read_csv('data/raw/tmdb_5000_movies.csv')

# Filter TMDB movies that have matching poster data
merged_df = tmdb_df[tmdb_df['id'].isin(poster_map.keys())].copy()

# Add poster_path variable
merged_df['poster_path'] = merged_df['id'].map(poster_map)

# Parse genres JSON and extract names
def extract_genres(genres_json):
    try:
        genres = json.loads(genres_json)
        return [g['name'] for g in genres]
    except:
        return []

merged_df['genres'] = merged_df['genres'].apply(extract_genres)

# Convert to list of dictionaries (JSON format)
result_movies = merged_df.to_dict(orient='records')

# Save as JSON
with open('data/processed/tmdb_with_poster.json', 'w', encoding='utf-8') as f:
    json.dump(result_movies, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Merged {len(result_movies)} movies with poster data")
print(f"[SUCCESS] Output saved to: data/processed/tmdb_with_poster.json")
print(f"\nSample movie:")
if result_movies:
    print(json.dumps(result_movies[0], indent=2, ensure_ascii=False))
