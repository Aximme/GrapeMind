"""
Extraction des plats associés à Harmonize.

Fonctionnement :
- Lecture CSV avec 'Harmonize' (liste de plats par vin).
- Évalue chaque chaîne de liste en liste réelle (via eval).
- Agrège et trie les noms de plats uniques.
- Génère un fichier CSV contenant tous les plats distincts ('all_harmonize.csv').

Utilisation :
- Source : get_meals_name.csv
- Sortie : all_harmonize.csv
"""

import pandas as pd

input_file = '/Users/maxime/Desktop/Universite/Gestion de Projet/GrapeMind/data_dump/Python_cleanData/get_meals_name.csv'
df = pd.read_csv(input_file)

harmonize_list = set()
for items in df['Harmonize']:
    items_list = eval(items)
    harmonize_list.update(items_list)

unique_harmonize_list = sorted(harmonize_list)

output_df = pd.DataFrame(unique_harmonize_list, columns=['Dish'])

output_file = 'all_harmonize.csv'
output_df.to_csv(output_file, index=False)

print(f"Fichier CSV de sortie généré avec succès : {output_file}")
