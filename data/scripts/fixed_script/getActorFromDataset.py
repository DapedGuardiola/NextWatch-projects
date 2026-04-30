import pandas as pd
import json
import os

# Define paths
raw_path = os.path.join(os.path.dirname(__file__), '..', 'raw', 'person_images.csv')
output_path = os.path.join(os.path.dirname(__file__), '..', 'processed', 'actor.json')

print("Loading peerson_image.csv...")
df = pd.read_csv(raw_path)

print(f"Total rows: {len(df)}")
print(f"Columns: {list(df.columns)}")

# Convert to list of dicts
movies_list = df.to_dict('records')

# Save to JSON
with open(output_path, 'w', encoding='utf-8') as f:
    json.dump(movies_list, f, ensure_ascii=False, indent=2)

print(f"\nSuccessfully saved to {output_path}")
print(f"Total records: {len(movies_list)}")
