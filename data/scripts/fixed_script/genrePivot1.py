import json
from pathlib import Path

def extract_unique_genres(json_file_path, output_format='with_id'):
    """
    Extract unique genres from TMDB cleaned JSON data

    Args:
        json_file_path: Path to the cleaned JSON file
        output_format: 'with_id' (generate ID) or 'name_only' (just genre names)

    Returns:
        List of genres (dict or string depending on format)
    """

    # Read JSON file
    with open(json_file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Extract unique genres
    unique_genres = set()
    for item in data:
        if 'genres' in item and isinstance(item['genres'], list):
            for genre in item['genres']:
                unique_genres.add(genre)

    # Sort for consistency
    sorted_genres = sorted(list(unique_genres))

    # Format output
    if output_format == 'with_id':
        result = [
            {"id": idx + 1, "name": genre}
            for idx, genre in enumerate(sorted_genres)
        ]
    else:  # name_only
        result = sorted_genres

    return result


def save_genres(genres, output_path, format_type='json'):
    """Save extracted genres to file"""

    with open(output_path, 'w', encoding='utf-8') as f:
        if format_type == 'json':
            json.dump(genres, f, indent=2, ensure_ascii=False)
        elif format_type == 'csv':
            import csv
            writer = csv.writer(f)
            if isinstance(genres[0], dict):
                writer.writerow(['id', 'name'])
                for genre in genres:
                    writer.writerow([genre['id'], genre['name']])
            else:
                writer.writerow(['name'])
                for genre in genres:
                    writer.writerow([genre])


# Main execution
if __name__ == "__main__":
    input_file = Path("data/processed/tmdb_cleaned.json")

    # Option 1: With ID
    genres_with_id = extract_unique_genres(str(input_file), 'with_id')
    save_genres(genres_with_id, "data/processed/genres_with_id.json")
    print(f"[OK] Saved {len(genres_with_id)} genres with ID -> data/processed/genres_with_id.json")
    print(f"     Format: {genres_with_id[:2]}...\n")

    # Option 2: Name only
    genres_name_only = extract_unique_genres(str(input_file), 'name_only')
    save_genres(genres_name_only, "data/processed/genres_names.json")
    print(f"[OK] Saved {len(genres_name_only)} genre names -> data/processed/genres_names.json")
    print(f"     Format: {genres_name_only[:3]}...\n")

    # Also save as CSV if needed
    save_genres(genres_with_id, "data/processed/genres_with_id.csv", 'csv')
    print(f"[OK] Saved as CSV -> data/processed/genres_with_id.csv")
