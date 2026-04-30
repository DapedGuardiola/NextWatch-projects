import json
from pathlib import Path
import pandas as pd

root = Path(__file__).resolve().parents[2]
input_path = root / "data" / "processed" / "tmdb_5000_credits.json"
output_path = root / "data" / "processed" / "castname.json"

df = pd.read_json(input_path)
df["cast_list"] = df["cast"].apply(json.loads)
df["cast_names"] = df["cast_list"].apply(lambda cast: [member["name"] for member in cast])

castname_data = df[["movie_id", "title", "cast_names"]].to_dict(orient="records")
output_path.parent.mkdir(parents=True, exist_ok=True)
with open(output_path, "w", encoding="utf-8") as f:
    json.dump(castname_data, f, ensure_ascii=False, indent=2)

print(f"Saved cast names to: {output_path}")