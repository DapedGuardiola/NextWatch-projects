import json
import sys

input_data = sys.stdin.read()
vectors = json.loads(input_data)

total_genres = 20  # sesuai jumlah genre

matrix = []

for i in range(total_genres):
    # Film yang punya genre i
    films_with_genre = [v for v in vectors if len(v) > i and v[i] == 1]
    total = len(films_with_genre)
    
    if i == 19:
        print(f"DEBUG: Jumlah film untuk genre_id 1 (indeks 0) adalah {total} film.")
        if total > 0:
            print(f"Contoh sample vektor film pertama: {films_with_genre[19]}")
    
    cooccurrence = []
    for j in range(total_genres):
        if i == j:
            cooccurrence.append(1.0)
        elif total == 0:
            cooccurrence.append(0.0)
        else:
            #dalam indeks ke i film apa yang genre indeks ke j nya = 1 -> dijumlah 1n, n = banyak film yang memenuhi syarat
            both = sum(1 for v in films_with_genre if len(v) > j and v[j] == 1)  
            cooccurrence.append(round(both / total, 6))
    
    matrix.append({
        'genre_id': i + 1,  # map_id = index + 1
        'cooccurrence_vector': cooccurrence
    })
    
# Simpan ke JSON
with open('genre_cooccurrence.json', 'w') as f:
    json.dump(matrix, f, indent=2)

print(f"Selesai! {len(matrix)} genre diproses.")