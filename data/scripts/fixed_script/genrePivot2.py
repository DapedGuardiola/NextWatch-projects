import json
import os

INPUT_FILE = "data/processed/tmdb_cleaned.json"
GENRE_MAPPING_FILE = "data/processed/genres_with_id.json"
OUTPUT_FILE = "data/processed/movie_genres.json"

# =========================
# Load mapping (O(1) lookup)
# =========================
with open(GENRE_MAPPING_FILE, "r", encoding="utf-8") as f:
    genre_list = json.load(f)

genre_to_id = {g["name"]: g["id"] for g in genre_list}

# =========================
# Process movies
# =========================
results = []

with open(INPUT_FILE, "r", encoding="utf-8") as f:
    movies = json.load(f)

    for movie in movies:
        try:
            movie_id = movie["id"]
            genres = movie.get("genres", [])

            for genre in genres:
                genre_id = genre_to_id.get(genre)

                if genre_id:
                    results.append({
                        "movie_id": movie_id,
                        "genre_id": genre_id
                    })

        except Exception:
            continue

# =========================
# Save result
# =========================
os.makedirs(os.path.dirname(OUTPUT_FILE), exist_ok=True)

with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
    json.dump(results, f, ensure_ascii=False)

print(f"Saved {len(results)} rows to {OUTPUT_FILE}")