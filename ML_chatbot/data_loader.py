import json
import re
from collections import Counter
from db_connexion import get_connection
import torch
from torch.utils.data import Dataset
import ast

def parse_list_field(field_str):
    try:
        return json.loads(field_str)
    except Exception:
        try:
            return ast.literal_eval(field_str)
        except Exception as e:
            print(f"Error parsing field: {field_str}, error: {e}")
            return []

def fetch_joined_data():
    connection = get_connection()
    cursor = connection.cursor(dictionary=True)
    query = """
        SELECT DISTINCT
            d.idwine,
            d.Type,
            d.Grapes,
            d.Harmonize,
            s.name
        FROM descriptifs AS d
        INNER JOIN `grape-mind`.scrap AS s on d.idwine = s.idwine
    """
    cursor.execute(query)
    data = []
    for row in cursor:
        wine_name = row['name']
        wine_type = row['Type'] if row['Type'] else ""
        grapes_str = row['Grapes'] if row['Grapes'] else ""
        harmonize_str = row['Harmonize'] if row['Harmonize'] else ""
        
        grapes = parse_list_field(grapes_str)
        foods = parse_list_field(harmonize_str)
        
        if foods:  # On conserve uniquement si la liste d'aliments n'est pas vide.
            data.append({
                'wine': wine_name,
                'type': wine_type,
                'grapes': grapes,
                'foods': foods
            })
    cursor.close()
    connection.close()
    return data

def tokenize(text):
    # eviter les caracteres alpha numériques
    return re.findall(r'\w+', text.lower())

def build_vocab(texts, min_freq=1):
    counter = Counter()
    for text in texts:
        tokens = tokenize(text)
        counter.update(tokens)
    vocab = {word: idx+2 for idx, (word, count) in enumerate(counter.items()) if count >= min_freq}
    vocab['<PAD>'] = 0
    vocab['<UNK>'] = 1
    return vocab

def encode_text(text, vocab):
    tokens = tokenize(text)
    return [vocab.get(token, vocab['<UNK>']) for token in tokens]

def build_label_vocab(data, key):
    """
Pour l'instant on se dirige vers une recherche vin->aliments ou aliments->vin a l'aide d'un mot (foods ou wine) ici.
Dans le futur, sur l'UI du chat on ferra choisir ca a l'utilisateur avant, c'est plus pratique !
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





#------------------------------------------------------------------------------
# Création des datasets pour les deux cas :
#               1 : vin --> aliments 
#               2 : aliments --> vin
#------------------------------------------------------------------------------
class WineToFoodDataset(Dataset):
    """
    ICI --> On a un vin et on prédit les aliments qui s'y accordent.
    Pour chaque enregistrement, l'input est une chaîne :
      vin <wine> type <type> grapes <grape>

    La on renvoie un vecteur indiquant quels aliments (basés sur la colonne Harmonize) sont associé au/aux vin(s) donné en input par l'utilisateur.
    """
    def __init__(self, data, text_vocab, food_vocab):
        self.samples = []
        self.text_vocab = text_vocab
        self.food_vocab = food_vocab
        for entry in data:
            input_text = "vin " + entry['wine'].lower() + " type " + entry['type'].lower()
            if entry['grapes']:
                input_text += " grapes " + " ".join([g.lower() for g in entry['grapes']])
            input_encoded = encode_text(input_text, text_vocab)
            label = [0] * len(food_vocab)
            for food in entry['foods']:
                food_lower = food.lower()
                if food_lower in food_vocab:
                    label[food_vocab[food_lower]] = 1
            self.samples.append((input_encoded, torch.tensor(label, dtype=torch.float)))
    
    def __len__(self):
        return len(self.samples)
    
    def __getitem__(self, idx):
        return self.samples[idx]

class FoodToWineDataset(Dataset):
    """
    ICI --> On a un aliment, on prédit le vin qui se marie le mieux avec celui-ci.
      L'user rentre : aliment <food>
      On renvoie un vecteur pour le vin (basé sur la colonne wine [les noms])
    """
    def __init__(self, data, text_vocab, wine_vocab):
        self.samples = []
        self.text_vocab = text_vocab
        self.wine_vocab = wine_vocab
        for entry in data:
            for food in entry['foods']:
                input_text = "aliment " + food.lower()
                input_encoded = encode_text(input_text, text_vocab)
                label = [0] * len(wine_vocab)
                wine_lower = entry['wine'].lower()
                if wine_lower in wine_vocab:
                    label[wine_vocab[wine_lower]] = 1
                self.samples.append((input_encoded, torch.tensor(label, dtype=torch.float)))
    
    def __len__(self):
        return len(self.samples)
    
    def __getitem__(self, idx):
        return self.samples[idx]
