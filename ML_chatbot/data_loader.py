"""
Préparation des données en entrée du modèle.

Contenu :
- Lit les données du CSV clean.
- Parse les champs textuels ('Grapes', 'Harmonize') en listes interprétable par Python.
- Construit les vocabulaires indexés pour les aliments ou les noms de vin.

Utilisation :
- Chargé par le script d'entraînement principal (main.py).

Dépendances :
- csv, json, ast, config
"""

import json
import ast
import csv
from config import CSV_FILE_PATH

def parse_list_field(field_str):
    try:
        return json.loads(field_str)
    except Exception:
        try:
            return ast.literal_eval(field_str)
        except Exception as e:
            print(f"Error parsing field: {field_str}, error: {e}")
            return []

def fetch_csv_data():
    data = []
    with open(CSV_FILE_PATH, newline='', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile, delimiter=';')
        for row in reader:
            wine_name = row.get('name') or row.get('Name')
            wine_type = row.get('Type', '') or ""
            grapes_str = row.get('Grapes', '') or ""
            harmonize_str = row.get('Harmonize', '') or ""
            grapes = parse_list_field(grapes_str) if grapes_str else []
            foods = parse_list_field(harmonize_str) if harmonize_str else []
            if foods:
                data.append({
                    'wine': wine_name,
                    'type': wine_type,
                    'grapes': grapes,
                    'foods': foods
                })
    return data

def build_label_vocab(data, key):
    """
    Construit un vocabulaire d'étiquettes (wine ou foods) -> index
    """
    items = set()
    if key == 'foods':
        for entry in data:
            for food in entry['foods']:
                items.add(food.lower())
    elif key == 'wine':
        for entry in data:
            items.add(entry['wine'].lower())
    label_vocab = {item: idx for idx, item in enumerate(sorted(items))}
    return label_vocab