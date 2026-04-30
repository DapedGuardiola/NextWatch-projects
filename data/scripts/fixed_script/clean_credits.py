import json

# Read movie_credits.json
with open('data/processed/movie_credits.json', 'r', encoding='utf-8') as f:
    credits = json.load(f)

# Clean the data
cleaned_credits = []

for credit in credits:
    # Get top 3 cast members
    cast = credit.get('cast', [])
    if isinstance(cast, str):
        cast = json.loads(cast)

    # Take only top 3 and extract id, name
    top_3_cast = []
    for actor in cast[:3]:  # Top 3
        if 'id' in actor and 'name' in actor:
            top_3_cast.append({
                'id': actor['id'],
                'name': actor['name']
            })

    cleaned_credit = {
        'movie_id': credit.get('movie_id'),
        'cast': top_3_cast
    }

    cleaned_credits.append(cleaned_credit)

# Save cleaned data
with open('data/processed/movie_credits_clean.json', 'w', encoding='utf-8') as f:
    json.dump(cleaned_credits, f, indent=2, ensure_ascii=False)

print(f"[SUCCESS] Cleaned {len(cleaned_credits)} credit records")
print(f"[SUCCESS] Kept top 3 actors per movie")
print(f"[SUCCESS] Output saved to: data/processed/movie_credits_clean.json")
print(f"\nSample:")
if cleaned_credits:
    print(json.dumps(cleaned_credits[0], indent=2, ensure_ascii=False))
