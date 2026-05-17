import json
from pathlib import Path
from collections import defaultdict

path_movie_genre = Path('data/processed/movie_genres_pivot.json')
output = Path('data/processed/movieVector')
with open(path_movie_genre, 'r', encoding= 'utf-8')as f:
    movieData = json.load(f)

num_genres = 20
movie_genre_list = defaultdict(list)
feature_vector = []

for movie in movieData:
    movie_id = movie.get('movie_id')
    genre_id = movie.get('genre_id')

    movie_genre_list[movie_id].append(genre_id)
for movie_id, genres in movie_genre_list.items():
    vector = [0] * num_genres
    for genre_id in genres:
        vector[genre_id - 1] = 1
    feature_vector.append({"movie_id":movie_id,"vector":vector})

with open(output, 'w', encoding='utf-8')as f:
    json.dump(feature_vector,f,indent=2)