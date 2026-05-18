import json
from pathlib import Path

path_source = Path('data/processed/movies.json')
path_output = Path('data/processed/normalized_movie.json')
with open(path_source, 'r',encoding='utf-8') as f:
    movies = json.load(f)

normalized = []
max_rating = max(movie.get('rating') or 0 for movie in movies)
max_popularity = max(movie.get('popularity') or 0 for movie in movies)
max_rating_count = max(movie.get('rating_count') or 0 for movie in movies)

for movie in movies:
    movie_id = movie.get('tmdb_movie_id')
    rating = movie.get('rating')
    popularity = movie.get('popularity')
    rating_count = movie.get('rating_count')
    
    n_rating = round(rating / max_rating,2)
    n_popularity = round(popularity / max_popularity,2)
    n_rating_count = round(rating_count / max_rating_count,2)
    
    normalized.append({
        'movie_id':movie_id,
        'n_rating':n_rating,
        'n_popularity':n_popularity,
        'n_rating_count':n_rating_count,
    })

with open (path_output,'w',encoding='utf-8') as f:
    json.dump(normalized,f,indent=2)