import pandas as pd

import pandas as pd

# Charger le fichier CSV
df = pd.read_csv('scrap_vivino_4.csv', sep=',', on_bad_lines='skip')
# Fusionner les deux colonnes : si 'price' est NaN, utiliser 'moyenne_price'
df['price'] = df['price'].fillna(df['moyenne_price'])

# Supprimer la colonne 'moyenne_price' si elle n'est plus nécessaire
df.drop(columns=['moyenne_price'], inplace=True)

# Sauvegarder le DataFrame modifié dans un nouveau fichier CSV
df.to_csv('scrap_vivino_5.csv', index=False)




'''

Conversion des decimaux en points a la place des virgules


df = pd.read_csv('scrap_vivino_3.csv', sep=';', on_bad_lines='skip')
df['moyenne_price'] = df['moyenne_price'].str.replace(',', '.', regex=False)
df.to_csv('scrap_vivino_4.csv', index=False)
'''


