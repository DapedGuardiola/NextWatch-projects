import json
import os

# path
INPUT_FILE = "data/processed/tmdb_cleaned.json"
GENRE_MAPPING_FILE = "data/processed/genres_with_id.json"
OUTPUT_FILE = "data/processed/movies_genre_vector.json"

# load genre mapping
with open(GENRE_MAPPING_FILE, "r", encoding="utf-8") as f:
    genre_mapping_list = json.load(f)

# ubah ke dictionary: {genre_name: index}
genre_to_index = {g["name"]: g["id"] for g in genre_mapping_list}

# total jumlah genre (panjang vector)
VECTOR_SIZE = len(genre_to_index)

results = []

with open(INPUT_FILE, "r", encoding="utf-8") as f:
    data = json.load(f)

    for movie in data:
        try:
            movie_id = movie["id"]
            genres = movie.get("genres", [])

            # init vector 0
            vector = [0] * VECTOR_SIZE

            for genre in genres:
                if genre in genre_to_index:
                    idx = genre_to_index[genre]
                    vector[idx] = 1

            results.append({
                "movie_id": movie_id,
                "vector": vector
            })

        except Exception as e:
            # skip data rusak
            continue

# pastikan folder ada
os.makedirs(os.path.dirname(OUTPUT_FILE), exist_ok=True)

# simpan hasil
with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
    json.dump(results, f, ensure_ascii=False)

print(f"Saved {len(results)} records to {OUTPUT_FILE}")