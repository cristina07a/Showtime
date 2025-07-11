import pandas as pd
import json
from sklearn.metrics.pairwise import cosine_similarity

INPUT_CSV = 'filtered_artist_playlist.csv'

df = pd.read_csv(INPUT_CSV)

df_matrix = df[['playlistname', 'artistname']]

#matrix for artists - 1 if in playlist, 0 if not
playlist_artist_matrix = df_matrix.pivot_table(
    index='playlistname',
    columns='artistname',
    aggfunc=lambda x: 1,
    fill_value=0
)

# cosine similarity
similarity_matrix = pd.DataFrame(
    cosine_similarity(playlist_artist_matrix.T),
    index=playlist_artist_matrix.columns,
    columns=playlist_artist_matrix.columns
)

def recommend_artists(artist_name, top_n=5):

    artist_name = artist_name.strip()

    #check if artist exists in the similarity matrix
    if artist_name not in similarity_matrix.index:
        return f"Artistul '{artist_name}' nu exista in dataset."

    #get similarity scores for the given artist and sort them in descending order to get most similar artists first
    similar_scores = similarity_matrix[artist_name].sort_values(ascending=False).drop(labels=[artist_name])

    #return top_n most similar artists
    return similar_scores.head(top_n)

top_n = 5
recommendations = {}

for artist in similarity_matrix.index:
        #get the artist's similarity scores row (similarities to all other artists)
        #sort by similarity score descending
    similar_artists = similarity_matrix.loc[artist].drop(artist).sort_values(ascending=False).head(top_n).index.tolist()
    recommendations[artist] = similar_artists

with open('artist_recommendations.json', 'w', encoding='utf-8') as f:
    json.dump(recommendations, f, ensure_ascii=False, indent=4)

print("Done. Saved in artist_recommendations.json")
