import json

# Read tmdb_with_poster.json
with open('data/processed/tmdb_with_poster.json', 'r', encoding='utf-8') as f:
    movies = json.load(f)

# Fields to remove
fields_to_remove = ['homepage', 'status', 'revenue', 'production_countries', 'keywords', 'budget', 'title']

# Clean the data
cleaned_movies = []

for movie in movies:
    cleaned_movie = {}

    for key, value in movie.items():
        # Skip unwanted fields
        if key in fields_to_remove:
            continue

        # Rename vote_average to rating
        if key == 'vote_average':
            cleaned_movie['rating'] = value
        # Rename vote_count to rating_count
        elif key == 'vote_count':
            cleaned_movie['rating_count'] = value
        # Rename original_title to title
        elif key == 'original_title':
            cleaned_movie['title'] = value
        else:
            cleaned_movie[key] = value

    cleaned_movies.append(cleaned_movie)

# Save cleaned data
with open('data/processed/tmdb_cleaned.json', 'w', encoding='utf-8') as f:
    json.dump(cleaned_movies, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Cleaned {len(cleaned_movies)} movies")
print(f"[SUCCESS] Output saved to: data/processed/tmdb_cleaned.json")
