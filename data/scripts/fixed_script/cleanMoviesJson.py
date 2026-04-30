import json
import os

# Define paths
input_path = os.path.join(os.path.dirname(__file__), '..', 'processed', 'tmdb_cleaned.json')
output_path = os.path.join(os.path.dirname(__file__), '..', 'processed', 'movie_fix.json')

print("Loading tmdb_5000_movies.json...")
with open(input_path, 'r', encoding='utf-8') as f:
    movies = json.load(f)

print(f"Total movies: {len(movies)}")

# Process each movie
cleaned_movies = []

for idx, movie in enumerate(movies):
    if (idx + 1) % 1000 == 0:
        print(f"Processing: {idx + 1}/{len(movies)}")

    # Remove unwanted columns
    columns_to_remove = ['production_companies', 'spoken_languages', 'genres']
    for col in columns_to_remove:
        movie.pop(col, None)

    # # Process genres - extract only names
    # if 'genres' in movie:
    #     try:
    #         genres_data = json.loads(movie['genres'])
    #         genre_names = [g['name'] for g in genres_data]
    #         movie['genres'] = genre_names
    #     except (json.JSONDecodeError, KeyError, TypeError):
    #         movie['genres'] = []

    # Rename columns
    if 'id' in movie:
        movie['tmdb_movie_id'] = movie.pop('id')

    cleaned_movies.append(movie)

# Save cleaned data
with open(output_path, 'w', encoding='utf-8') as f:
    json.dump(cleaned_movies, f, ensure_ascii=False, indent=2)

print("\n" + "="*50)
print("DATA CLEANING COMPLETE")
print("="*50)
print(f"Removed columns: status, spoken_languages, production_countries")
print(f"Genres: extracted names only (array format)")
print(f"Renamed: vote_average -> rating")
print(f"Renamed: vote_count -> rating_count")
print(f"\nTotal cleaned records: {len(cleaned_movies)}")
print(f"Output: {output_path}")
print("="*50)
