import json

# Read movie_credits_clean.json
with open('data/processed/movie_credits_clean.json', 'r', encoding='utf-8') as f:
    credits = json.load(f)

# Transform to pivot table format (one row per movie-actor relationship)
pivot_data = []

for credit in credits:
    movie_id = credit.get('movie_id')
    cast = credit.get('cast', [])

    # Create one entry for each actor
    for actor in cast:
        pivot_data.append({
            'movie_id': movie_id,
            'cast_id': actor['id']
        })

# Save as JSON
with open('data/processed/movie_cast_pivot.json', 'w', encoding='utf-8') as f:
    json.dump(pivot_data, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Converted to pivot table format")
print(f"[SUCCESS] Total movie-actor relationships: {len(pivot_data)}")
print(f"[SUCCESS] Output saved to: data/processed/movie_cast_pivot.json")
print(f"\nSample:")
print(json.dumps(pivot_data[:6], indent=2, ensure_ascii=False))
