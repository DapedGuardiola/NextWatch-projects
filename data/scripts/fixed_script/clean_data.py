import json
import re

# Read merged movies
with open('data/processed/merged_movies.json', 'r', encoding='utf-8') as f:
    movies = json.load(f)

# Fields to remove
fields_to_remove = ['homepage', 'status', 'revenue', 'production_countries', 'keywords']

# Clean the data
cleaned_movies = []

for movie in movies:
    # Create new movie object
    cleaned_movie = {}

    for key, value in movie.items():
        # Skip unwanted fields
        if key in fields_to_remove:
            continue

        # Extract ID path from poster_link
        if key == 'poster_link':
            # Extract the ID part: MV5BMTQ0NjUzMDMyOF5BMl5BanBnXkFtZTgwODA1OTU0MDE
            match = re.search(r'(MV5[A-Za-z0-9_-]+)', value)
            if match:
                cleaned_movie['poster_id'] = match.group(1)
        # Rename vote_average to rating
        elif key == 'vote_average':
            cleaned_movie['rating'] = value
        # Rename vote_count to rating_count
        elif key == 'vote_count':
            cleaned_movie['rating_count'] = value
        else:
            cleaned_movie[key] = value

    cleaned_movies.append(cleaned_movie)

# Save cleaned data
with open('data/processed/merged_movies_clean.json', 'w', encoding='utf-8') as f:
    json.dump(cleaned_movies, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Cleaned {len(cleaned_movies)} movies")
print(f"[SUCCESS] Output saved to: data/processed/merged_movies_clean.json")
print(f"\nSample cleaned movie:")
if cleaned_movies:
    print(json.dumps(cleaned_movies[0], indent=2, ensure_ascii=False))
