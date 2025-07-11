import pandas as pd
import random

INPUT_CSV = 'spotify_dataset.csv'
OUTPUT_CSV = 'filtered_artist_playlist.csv'

GENRES = [
    'Pop', 'Rock', 'Alternative', 'Indie', 'Metal', 'Punk', 'Jazz', 'Blues', 'Classical',
    'Hip Hop', 'Rap', 'R&B', 'Soul', 'Funk', 'Electronic', 'House', 'Techno', 'Trance',
    'Dubstep', 'Drum and Bass', 'Reggae', 'Ska', 'Country', 'Folk', 'Latin', 'Salsa',
    'Reggaeton', 'K-Pop', 'J-Pop', 'World', 'Gospel', 'Opera', 'Ambient', 'New Age',
    'Soundtrack', 'Musical', 'Lo-fi', 'Chillout', 'Industrial', 'Grunge', 'Emo', 'Hardcore'
]

df = pd.read_csv(INPUT_CSV, quotechar='"', on_bad_lines='skip')
df.columns = [col.strip().strip('"') for col in df.columns]
df = df[['playlistname', 'artistname']]
df = df.drop_duplicates(subset=['playlistname', 'artistname'])

# count in how many playlists an artist is featured
artist_counts = df.groupby('artistname')['playlistname'].nunique()

#keep first 500 featured artists
top_artists = artist_counts.sort_values(ascending=False).head(500).index

df_filtered = df[df['artistname'].isin(top_artists)].copy()
df_filtered['musicGenre'] = df_filtered['artistname'].apply(lambda _: random.choice(GENRES))

df_filtered.to_csv(OUTPUT_CSV, index=False)
