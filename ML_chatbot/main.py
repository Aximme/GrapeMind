from flask import Flask, request, jsonify
import torch
from torch.utils.data import DataLoader
import torch.optim as optim
import json, os

from data_loader import (fetch_joined_data, build_vocab, build_label_vocab,
                         WineToFoodDataset, FoodToWineDataset, encode_text)
from model import TextClassifier, train_model, predict
from config import TRAINING_EPOCHS, BATCH_SIZE, LEARNING_RATE, EMBEDDING_DIM, HIDDEN_DIM

app = Flask(__name__)
device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')

# Variables globales
wine_to_food_model = None
food_to_wine_model = None
text_vocab = None
food_label_vocab = None
wine_label_vocab = None

def collate_fn(batch):
    texts, labels = zip(*batch)
    return list(texts), torch.stack(labels)

def load_and_train_models():
    global wine_to_food_model, food_to_wine_model, text_vocab, food_label_vocab, wine_label_vocab
    data = fetch_joined_data()
    if not data:
        print("Aucune donnée trouvée.")
        return
    
    texts_for_vocab = []
    for entry in data:
        # vin --> aliments : Prise en compte du nom, type et des cépages.
        text_wine = "vin " + entry['wine'].lower() + " type " + entry['type'].lower()
        if entry['grapes']:
            text_wine += " grapes " + " ".join([g.lower() for g in entry['grapes']])
        texts_for_vocab.append(text_wine)
        # aliments --> vin prise en compte de Harmonyze qui donne les aliments compatibles
        for food in entry['foods']:
            texts_for_vocab.append("aliment " + food.lower())
    text_vocab = build_vocab(texts_for_vocab)
    
    food_label_vocab = build_label_vocab(data, 'foods')
    wine_label_vocab = build_label_vocab(data, 'wine')
    
    wine_dataset = WineToFoodDataset(data, text_vocab, food_label_vocab)
    food_dataset = FoodToWineDataset(data, text_vocab, wine_label_vocab)

    # Taille du dataset vin --> aliments : 24349
    # Taille du dataset aliment --> vins : 101793
    print(f"Taille du dataset vin --> aliments : {len(wine_dataset)}")
    print(f"Taille du dataset aliment --> vins : {len(food_dataset)}")
    
    wine_loader = DataLoader(wine_dataset, batch_size=BATCH_SIZE, shuffle=True, collate_fn=collate_fn)
    food_loader = DataLoader(food_dataset, batch_size=BATCH_SIZE, shuffle=True, collate_fn=collate_fn)
    
    vocab_size = len(text_vocab)
    wine_to_food_model = TextClassifier(vocab_size, EMBEDDING_DIM, HIDDEN_DIM, len(food_label_vocab), pad_idx=text_vocab['<PAD>']).to(device)
    food_to_wine_model = TextClassifier(vocab_size, EMBEDDING_DIM, HIDDEN_DIM, len(wine_label_vocab), pad_idx=text_vocab['<PAD>']).to(device)
    
    criterion = torch.nn.BCEWithLogitsLoss()
    optimizer_wine = optim.Adam(wine_to_food_model.parameters(), lr=LEARNING_RATE)
    optimizer_food = optim.Adam(food_to_wine_model.parameters(), lr=LEARNING_RATE)
    
    for epoch in range(TRAINING_EPOCHS):
        loss_wine = train_model(wine_to_food_model, wine_loader, optimizer_wine, criterion, device)
        loss_food = train_model(food_to_wine_model, food_loader, optimizer_food, criterion, device)
        print(f"Epoch {epoch+1}: Loss (vin→aliments): {loss_wine:.4f}, Loss (aliment→vins): {loss_food:.4f}")
    
    torch.save(wine_to_food_model.state_dict(), 'wine_to_food_model.pt')
    torch.save(food_to_wine_model.state_dict(), 'food_to_wine_model.pt')
    with open('text_vocab.json', 'w') as f:
        json.dump(text_vocab, f)
    with open('food_label_vocab.json', 'w') as f:
        json.dump(food_label_vocab, f)
    with open('wine_label_vocab.json', 'w') as f:
        json.dump(wine_label_vocab, f)

@app.route('/train', methods=['POST'])
def train_endpoint():
    load_and_train_models()
    return jsonify({"status": "Entraînement terminé"})

def determine_query_type(query):
    """
    Check le type de la demande. Si mot contenu dans l'input de l'utilisateur appartient
    a la liste des vins alors on retour wine on va donc utilisé le modèle wine_to_food.

    Si elle est perdue ca renvoie vers le modele aliments --> vins [food_to_wine] (cherchez pas de logique y en a pas)
    """
    query_lower = query.lower().strip()
    first_word = query_lower.split()[0]
    if first_word == "vin":
        return "vin"
    elif first_word == "aliment":
        return "aliment"
    else:
        return "aliment"

@app.route('/predict', methods=['POST'])
def predict_endpoint():
    global wine_to_food_model, food_to_wine_model, text_vocab, food_label_vocab, wine_label_vocab
    if wine_to_food_model is None or food_to_wine_model is None:
        return jsonify({"error": "🟥 FAUT ENTRAINER LES MODELES ! \n Tiens nullos : curl -X POST http://127.0.0.1:5000/train \nlance flask avant (python3 main.py)"})
    req_data = request.get_json()
    query = req_data.get('query', '')
    if not query:
        return jsonify({"error": "Aucune query recue;"})
    
    query_type = determine_query_type(query)
    if query_type is None:
        return jsonify({"error": "Format de query invalide. Veuillez commencer par 'vin' ou 'aliment'."})
    
    input_text = query.lower()

    if query_type == "vin":
        model = wine_to_food_model
        label_vocab = food_label_vocab
    else:
        model = food_to_wine_model
        label_vocab = wine_label_vocab

    tokens = input_text.split()
    tokens_in_vocab = [t for t in tokens if t in text_vocab]

    probs = predict(model, input_text, text_vocab, device)
    threshold = 0.4

    inv_label_vocab = {v: k for k, v in label_vocab.items()}

    # Recuperation du top 10 des sorties les + pertinentes
    top10_indices = probs.argsort()[-10:][::-1]
    recommendations = [inv_label_vocab[idx] for idx in top10_indices]

    debug_info = {
        "tokens_found": tokens_in_vocab,
        "query_tokens": tokens,
        "top_predictions": [
            {"item": inv_label_vocab[idx], "probability": float(probs[idx])}
            for idx in top10_indices
        ],
        "threshold_used": threshold
    }
    
    return jsonify({
        "query": query, 
        "recommendations": recommendations,
        "debug_info": debug_info
    })
    
if __name__ == '__main__':
    if os.path.exists('wine_to_food_model.pt') and os.path.exists('food_to_wine_model.pt'):
        import json
        with open('text_vocab.json', 'r') as f:
            text_vocab = json.load(f)
        with open('food_label_vocab.json', 'r') as f:
            food_label_vocab = json.load(f)
        with open('wine_label_vocab.json', 'r') as f:
            wine_label_vocab = json.load(f)
        vocab_size = len(text_vocab)
        from model import TextClassifier
        from config import EMBEDDING_DIM, HIDDEN_DIM
        wine_to_food_model = TextClassifier(vocab_size, EMBEDDING_DIM, HIDDEN_DIM, len(food_label_vocab), pad_idx=text_vocab['<PAD>']).to(device)
        food_to_wine_model = TextClassifier(vocab_size, EMBEDDING_DIM, HIDDEN_DIM, len(wine_label_vocab), pad_idx=text_vocab['<PAD>']).to(device)
        wine_to_food_model.load_state_dict(torch.load('wine_to_food_model.pt', map_location=device))
        food_to_wine_model.load_state_dict(torch.load('food_to_wine_model.pt', map_location=device))
        print("✅   Modeles pre-entrainés chargés\n")
    app.run(debug=True)