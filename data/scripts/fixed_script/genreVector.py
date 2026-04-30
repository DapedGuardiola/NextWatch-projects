import json
import numpy as np
from pathlib import Path

def load_json(filepath):
    """Load JSON file"""
    with open(filepath, 'r', encoding='utf-8') as f:
        return json.load(f)


def create_genre_mapping(genres_list):
    """
    Create mapping dari genre name ke index

    Args:
        genres_list: List of genre dicts with 'id' and 'name'

    Returns:
        Dict mapping genre_name -> index
    """
    return {genre['name']: idx for idx, genre in enumerate(genres_list)}


def build_feature_vectors(movies_data, genre_mapping):
    """
    Build one-hot encoded feature vectors untuk setiap movie

    Args:
        movies_data: List of movie dicts dari tmdb_cleaned.json
        genre_mapping: Dict mapping genre_name -> index

    Returns:
        List of dicts dengan 'id' dan 'vector'
    """
    num_genres = len(genre_mapping)
    feature_vectors = []

    for movie in movies_data:
        movie_id = movie.get('id')
        genres = movie.get('genres', [])

        # Initialize vector with zeros
        vector = [0] * num_genres

        # Set 1 for genres yang ada di movie ini
        for genre in genres:
            if genre in genre_mapping:
                idx = genre_mapping[genre]
                vector[idx] = 1

        feature_vectors.append({
            "id": movie_id,
            "vector": vector
        })

    return feature_vectors


def save_feature_vectors(feature_vectors, output_path):
    """Save feature vectors ke JSON"""
    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(feature_vectors, f, indent=2)


# Main execution
if __name__ == "__main__":
    # Paths
    movies_file = Path("data/processed/tmdb_cleaned.json")
    genres_file = Path("data/processed/genres_with_id.json")
    output_file = Path("data/processed/feature_vectors.json")

    # Load data
    print("[Loading] Movies from tmdb_cleaned.json...")
    movies = load_json(str(movies_file))

    print("[Loading] Genre mapping from genres_with_id.json...")
    genres_list = load_json(str(genres_file))

    # Create genre mapping
    genre_mapping = create_genre_mapping(genres_list)
    print(f"[INFO] Total genres: {len(genre_mapping)}")

    # Build feature vectors
    print("[Processing] Building feature vectors...")
    feature_vectors = build_feature_vectors(movies, genre_mapping)

    # Save results
    print(f"[Saving] Feature vectors for {len(feature_vectors)} movies...")
    save_feature_vectors(feature_vectors, str(output_file))

    print(f"\n[OK] Saved feature vectors -> {output_file}")
    print(f"[Sample] First 2 vectors:")
    for fv in feature_vectors[:2]:
        print(f"  Movie ID {fv['id']}: {fv['vector'][:5]}... (showing first 5)")
