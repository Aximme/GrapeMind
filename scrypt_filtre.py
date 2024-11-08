import pandas as pd
import os
from tqdm import tqdm
"""
Auteur : Marchionni Hugo
Date : 18/10/2024

Fonction : process_file

Description :
La fonction `process_file` prend en entrée un fichier CSV contenant des évaluations de vins, un DataFrame contenant des identifiants de vins, et un répertoire de sortie. Elle filtre les évaluations pour ne conserver que celles correspondant aux identifiants de vins présents dans le DataFrame fourni. Le résultat est ensuite sauvegardé dans un nouveau fichier CSV.

Paramètres :
- input_file (str) : Chemin du fichier CSV d'évaluations de vins à traiter.
- wine_ids_df (pd.DataFrame) : DataFrame contenant les identifiants de vins (colonne `idwine`) pour filtrer les évaluations.
- output_dir (str) : Répertoire où le fichier CSV filtré sera sauvegardé.
- has_header (bool) : Indique si le fichier d'entrée possède une ligne d'en-tête (True par défaut).

Retourne :
- tuple : Un tuple contenant le nombre de lignes dans le fichier original et le nombre de lignes dans le fichier filtré.

Fonctionnalité :
1. Charge le fichier CSV d'évaluations de vins, avec ou sans en-tête.
2. Renomme la colonne contenant les identifiants de vins en `idwine`, si nécessaire.
3. Effectue une jointure interne avec le DataFrame `wine_ids_df` pour ne conserver que les évaluations correspondant aux identifiants de vins donnés.
4. Réorganise les colonnes pour mettre `idwine` en première position.
5. Sauvegarde le fichier filtré dans le répertoire spécifié avec un nom de fichier préfixé par `filtered_`.
6. Retourne le nombre de lignes dans le fichier original et dans le fichier filtré.

"""

def process_file(input_file, wine_ids_df, output_dir, has_header=True):
    # Charger le fichier CSV d'évaluation
    if has_header:
        ratings_df = pd.read_csv(input_file)
        if 'WineID' in ratings_df.columns:
            ratings_df = ratings_df.rename(columns={'WineID': 'idwine'})
        elif 'idwine' not in ratings_df.columns:
            print(f"Attention: Le fichier {input_file} n'a pas de colonne 'WineID' ou 'idwine'. Fichier ignoré.")
            return len(ratings_df), 0
    else:
        # Pour les fichiers sans en-tête, on suppose que la colonne 'idwine' est la deuxième
        ratings_df = pd.read_csv(input_file, header=None, names=['RatingID', 'idwine', 'Vintage', 'Rating', 'Date'])
    
    # Effectuer une jointure interne entre les deux DataFrames
    filtered_ratings = pd.merge(ratings_df, wine_ids_df, on='idwine', how='inner')
    
    # Réorganiser les colonnes pour avoir 'idwine' en première position
    columns = ['idwine'] + [col for col in filtered_ratings.columns if col != 'idwine']
    filtered_ratings = filtered_ratings[columns]
    
    # Créer le nom du fichier de sortie
    output_file = os.path.join(output_dir, f"filtered_{os.path.basename(input_file)}")
    
    # Sauvegarder le résultat filtré
    filtered_ratings.to_csv(output_file, index=False, header=has_header)
    
    return len(ratings_df), len(filtered_ratings)

# Charger le fichier des IDs de vins
wine_ids_df = pd.read_csv('vins2_2-4.csv')

# Créer un répertoire pour les fichiers de sortie
output_dir = 'filtered_output'
os.makedirs(output_dir, exist_ok=True)

# Traiter tous les fichiers d'évaluation
total_original_rows = 0
total_filtered_rows = 0

for i in tqdm(range(1, 44), desc="Traitement des fichiers"):
    input_file = f'output_file_{i}.csv'
    if os.path.exists(input_file):
        try:
            # Le premier fichier a un en-tête, les autres non
            has_header = (i == 1)
            original_rows, filtered_rows = process_file(input_file, wine_ids_df, output_dir, has_header)
            total_original_rows += original_rows
            total_filtered_rows += filtered_rows
        except Exception as e:
            print(f"Erreur lors du traitement de {input_file}: {str(e)}")
    else:
        print(f"Le fichier {input_file} n'existe pas. Passage au suivant.")

print(f"\nNombre total de lignes originales : {total_original_rows}")
print(f"Nombre total de lignes après filtrage : {total_filtered_rows}")
print(f"Nombre total de lignes supprimées : {total_original_rows - total_filtered_rows}")
print(f"\nLes fichiers filtrés ont été sauvegardés dans le répertoire '{output_dir}'.")