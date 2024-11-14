import pandas as pd
import os
from tqdm import tqdm

"""
Auteur : Marchionni Hugo    
Date : 18/10/2024

Fonction : merge_csv_files

Description :
La fonction `merge_csv_files` permet de fusionner plusieurs fichiers CSV contenant des évaluations de vins en un seul fichier CSV. Elle prend un répertoire d'entrée contenant les fichiers CSV à fusionner et un chemin pour le fichier de sortie.

Paramètres :
- input_dir (str) : Répertoire contenant les fichiers CSV filtrés à fusionner.
- output_file (str) : Chemin et nom du fichier de sortie dans lequel les données fusionnées seront enregistrées.

Fonctionnalité :
1. Liste et trie tous les fichiers CSV dans le répertoire d'entrée qui commencent par `filtered_output_file_` et se terminent par `.csv`.
2. Écrit le contenu du premier fichier dans le fichier de sortie avec l'en-tête.
3. Ajoute les autres fichiers CSV au fichier de sortie sans réécrire l'en-tête pour éviter les duplications.
4. Utilise une lecture par morceaux (chunks) pour économiser la mémoire, en particulier avec de gros fichiers.

À la fin de la fusion, le fichier de sortie contiendra toutes les données de chaque fichier d'évaluation, consolidées en un seul fichier CSV.

"""


def merge_csv_files(input_dir, output_file):
    # Liste tous les fichiers CSV dans le répertoire d'entrée
    csv_files = [f for f in os.listdir(input_dir) if f.startswith('filtered_output_file_') and f.endswith('.csv')]
    
    # Trie les fichiers pour s'assurer qu'ils sont traités dans l'ordre
    csv_files.sort(key=lambda x: int(x.split('_')[3].split('.')[0]))
    
    # Ouvre le fichier de sortie en mode écriture
    with open(output_file, 'w', newline='') as outfile:
        # Traite le premier fichier séparément pour écrire l'en-tête
        first_file = os.path.join(input_dir, csv_files[0])
        df = pd.read_csv(first_file)
        df.to_csv(outfile, index=False)
        
        # Traite les fichiers restants
        for file in tqdm(csv_files[1:], desc="Fusion des fichiers"):
            file_path = os.path.join(input_dir, file)
            # Lit le fichier par morceaux pour économiser la mémoire
            # Utilise les noms de colonnes du premier fichier
            for chunk in pd.read_csv(file_path, chunksize=100000, header=None, names=df.columns):
                chunk.to_csv(outfile, header=False, index=False, mode='a')

# Définir le répertoire d'entrée et le fichier de sortie
input_dir = 'filtered_output'
output_file = 'merged_wine_ratings.csv'

# Exécuter la fusion
merge_csv_files(input_dir, output_file)

print(f"Tous les fichiers ont été fusionnés dans {output_file}")

# Vérifier le nombre total de lignes dans le fichier fusionné
total_rows = sum(1 for _ in open(output_file)) - 1  # -1 pour l'en-tête
print(f"Nombre total de lignes dans le fichier fusionné : {total_rows}")