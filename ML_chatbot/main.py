from flask import Flask, request, jsonify
import json, os
import numpy as np
from xgboost import XGBClassifier
from sklearn.multioutput import MultiOutputClassifier
import joblib

from data_loader import fetch_csv_data, build_label_vocab
from model import compute_metrics
from config import XGB_PARAMS

app = Flask(__name__)

wine_to_food_model = None
food_to_wine_model = None
food_label_vocab = None
wine_label_vocab = None
type_index = None
grape_index = None
wine_features = None
metrics_wine = None
metrics_food = None

def load_and_train_models():
    global wine_to_food_model, food_to_wine_model
    global food_label_vocab, wine_label_vocab, type_index, grape_index, wine_features
    global metrics_wine, metrics_food

    data = fetch_csv_data()
    if not data:
        print("Aucune donnée trouvée dans le CSV.")
        return

    food_label_vocab = build_label_vocab(data, 'foods')
    wine_label_vocab = build_label_vocab(data, 'wine')

    type_set = set(entry['type'] if entry['type'] else "" for entry in data)
    grape_set = set()
    for entry in data:
        for g in entry['grapes']:
            grape_set.add(g)
    type_list = sorted(type_set)
    grape_list = sorted(grape_set)
    type_index = {t: i for i, t in enumerate(type_list)}
    grape_index = {g: i for i, g in enumerate(grape_list)}

    n_wines = len(data)
    X_wine = np.zeros((n_wines, len(type_index) + len(grape_index)), dtype=int)
    Y_wine = np.zeros((n_wines, len(food_label_vocab)), dtype=int)
    wine_features = {}
    for i, entry in enumerate(data):
        wine_name = entry['wine']
        wine_type = entry['type']
        grapes_list = entry['grapes']
        if wine_type in type_index:
            X_wine[i, type_index[wine_type]] = 1
        for g in grapes_list:
            if g in grape_index:
                X_wine[i, len(type_index) + grape_index[g]] = 1
        for food in entry['foods']:
            food_lower = food.lower()
            if food_lower in food_label_vocab:
                Y_wine[i, food_label_vocab[food_lower]] = 1
        wine_features[wine_name.lower()] = {
            "name": wine_name,
            "type": wine_type,
            "grapes": grapes_list
        }

    wine_to_food_model = MultiOutputClassifier(XGBClassifier(**XGB_PARAMS))
    wine_to_food_model.fit(X_wine, Y_wine)
    Y_wine_pred = wine_to_food_model.predict(X_wine)
    metrics_wine = compute_metrics(Y_wine, Y_wine_pred)
    print("Performances vin->aliments :", metrics_wine)

    food_occurrences = []
    wine_occurrences = []
    for entry in data:
        wine_lower = entry['wine'].lower()
        for food in entry['foods']:
            food_occurrences.append(food.lower())
            wine_occurrences.append(wine_lower)

    n_foods_occ = len(food_label_vocab)
    X_food_occ = np.zeros((len(food_occurrences), n_foods_occ), dtype=int)
    for i, food in enumerate(food_occurrences):
        if food in food_label_vocab:
            X_food_occ[i, food_label_vocab[food]] = 1

    Y_food_occ = np.zeros((len(wine_occurrences), len(wine_label_vocab)), dtype=int)
    for i, wine in enumerate(wine_occurrences):
        if wine in wine_label_vocab:
            Y_food_occ[i, wine_label_vocab[wine]] = 1

    food_to_wine_model = MultiOutputClassifier(XGBClassifier(**XGB_PARAMS))
    food_to_wine_model.fit(X_food_occ, Y_food_occ)
    Y_food_pred = food_to_wine_model.predict(X_food_occ)
    metrics_food = compute_metrics(Y_food_occ, Y_food_pred)
    print("Performances aliment->vins :", metrics_food)

    joblib.dump(wine_to_food_model, 'wine_to_food_model.pkl')
    joblib.dump(food_to_wine_model, 'food_to_wine_model.pkl')
    with open('food_label_vocab.json', 'w') as f:
        json.dump(food_label_vocab, f)
    with open('wine_label_vocab.json', 'w') as f:
        json.dump(wine_label_vocab, f)
    with open('type_index.json', 'w') as f:
        json.dump(type_index, f)
    with open('grape_index.json', 'w') as f:
        json.dump(grape_index, f)
    with open('wine_features.json', 'w') as f:
        json.dump(wine_features, f)

@app.route('/train', methods=['POST'])
def train_endpoint():
    load_and_train_models()
    if wine_to_food_model is None or food_to_wine_model is None:
        return jsonify({"status": "❌ Erreur survenue pdt l'entraînement."})
    return jsonify({
        "status": "✅ Entraînement terminé",
        "metrics": {
            "wine_to_food": {k: float(v) for k, v in compute_metrics(np.zeros(1), np.zeros(1)).items()} 
            if False else {k: (float(v) if isinstance(v, (float, int)) else v) 
                           for k, v in metrics_wine.items()}, 
            "food_to_wine": {k: (float(v) if isinstance(v, (float, int)) else v) 
                             for k, v in metrics_food.items()}
        }
    })

def determine_query_type(query):
    query_lower = query.lower().strip()
    first_word = query_lower.split()[0] if query_lower else ""
    if first_word == "vin":
        return "vin"
    elif first_word == "aliment":
        return "aliment"
    else:
        return "aliment"

@app.route('/predict', methods=['POST'])
def predict_endpoint():
    global wine_to_food_model, food_to_wine_model, food_label_vocab, wine_label_vocab
    global type_index, grape_index, wine_features
    if wine_to_food_model is None or food_to_wine_model is None:
        return jsonify({"error": "🟥 Modèles non entraînés. Lancer l'endpoint /train avant de prédire."})
    req_data = request.get_json()
    query = req_data.get('query', '')
    if not query:
        return jsonify({"error": "Aucune requête reçue."})

    query_type = determine_query_type(query)
    if query_type not in ["vin", "aliment"]:
        return jsonify({"error": "Format de requête invalide."})

    query_lower = query.lower().strip()
    if query_type == "vin":
        tokens = query_lower.split()
        search_name = ""
        search_type = ""
        search_grapes = []

        i = 1  # skip "vin"
        while i < len(tokens):
            if tokens[i] == "type" and i + 1 < len(tokens):
                search_type = tokens[i + 1]
                i += 2
            elif tokens[i] == "grapes":
                i += 1
                while i < len(tokens) and tokens[i] not in ["type"]:
                    search_grapes.append(tokens[i])
                    i += 1
            else:
                search_name += tokens[i] + " "
                i += 1

        search_name = search_name.strip()

        X_query = np.zeros((1, len(type_index) + len(grape_index)), dtype=int)

        if search_type in type_index:
            X_query[0, type_index[search_type]] = 1

        for g in search_grapes:
            if g in grape_index:
                X_query[0, len(type_index) + grape_index[g]] = 1

        for key, wine_info in wine_features.items():
            if search_name and search_name in wine_info["name"].lower():
                if wine_info["type"] in type_index:
                    X_query[0, type_index[wine_info["type"]]] = 1
                for g in wine_info["grapes"]:
                    if g in grape_index:
                        X_query[0, len(type_index) + grape_index[g]] = 1

        if X_query.sum() == 0:
            return jsonify({"error": f"Vin ou attributs inconnus dans la requête : nom='{search_name}', type='{search_type}', cépages={search_grapes}."})

        model = wine_to_food_model
        label_vocab = food_label_vocab
    else:
        search_term = query_lower[len("aliment"):].strip()
        if search_term not in food_label_vocab:
            return jsonify({"error": f"Aliment '{search_term}' inconnu dans la base de données."})
        X_query = np.zeros((1, len(food_label_vocab)), dtype=int)
        X_query[0, food_label_vocab[search_term]] = 1
        model = food_to_wine_model
        label_vocab = wine_label_vocab

    probs_list = []
    for estimator in model.estimators_:
        prob = estimator.predict_proba(X_query)[0][1] if estimator.classes_.shape[0] > 1 else 0.0
        probs_list.append(prob)
    probs = np.array(probs_list)

    inv_label_vocab = {v: k for k, v in label_vocab.items()}
    top10_indices = probs.argsort()[-10:][::-1]
    recommendations = [inv_label_vocab[idx] for idx in top10_indices]

    debug_info = {}
    if query_type == "vin":
        debug_info["input_search_name"] = search_name
        debug_info["input_type"] = search_type
        debug_info["input_grapes"] = search_grapes
    else:
        debug_info["food_input"] = search_term
    debug_info["top_predictions"] = [
        {"item": inv_label_vocab[idx], "probability": float(probs[idx])} for idx in top10_indices
    ]
    debug_info["threshold_used"] = 0.4

    return jsonify({
        "query": query,
        "recommendations": recommendations,
        "debug_info": debug_info
    })

if __name__ == '__main__':
    if os.path.exists('wine_to_food_model.pkl') and os.path.exists('food_to_wine_model.pkl'):
        with open('food_label_vocab.json', 'r') as f:
            food_label_vocab = json.load(f)
        with open('wine_label_vocab.json', 'r') as f:
            wine_label_vocab = json.load(f)
        with open('type_index.json', 'r') as f:
            type_index = json.load(f)
        with open('grape_index.json', 'r') as f:
            grape_index = json.load(f)
        with open('wine_features.json', 'r') as f:
            wine_features = json.load(f)
        wine_to_food_model = joblib.load('wine_to_food_model.pkl')
        food_to_wine_model = joblib.load('food_to_wine_model.pkl')
        print("✅  Modèles pré-entraînés chargés.\n")
    app.run(debug=False, port=5000, use_reloader=False)






    """
    Performances vin->aliments : {'precision_micro': 0.8734746154667172, 'precision_macro': 0.6231063458276684, 'recall_micro': 0.885477278191873, 'recall_macro': 0.5612745227396416, 'f1_micro': 0.8794349951941159, 'f1_macro': 0.5668385399127993, 'subset_accuracy': 0.7156485613171257, 'hamming_loss': 0.02589500948863928, 'jaccard_micro': np.float64(0.7848139031849034), 'jaccard_macro': np.float64(0.486477143851615)}
    Performances aliment->vins : {'precision_micro': 1.0, 'precision_macro': 1.0, 'recall_micro': 1.0, 'recall_macro': 1.0, 'f1_micro': 1.0, 'f1_macro': 1.0, 'subset_accuracy': 1.0, 'hamming_loss': 0.0, 'jaccard_micro': np.float64(1.0), 'jaccard_macro': np.float64(1.0)}


    curl -X POST http://127.0.0.1:5050/predict \
  -H "Content-Type: application/json" \
  -d '{"query": "aliment beef"}'
{
  "debug_info": {
    "food_input": "beef",
    "threshold_used": 0.4,
    "top_predictions": [
      {
        "item": "chateau la canorgue luberon blanc",
        "probability": 0.69
      },
      {
        "item": "clarendelle amberwine",
        "probability": 0.67
      },
      {
        "item": "chateau de beauregard saumur brut cuvee classique",
        "probability": 0.65
      },
      {
        "item": "chateau malartic-lagraviere la reserve de malartic (le sillage) blanc",
        "probability": 0.65
      },
      {
        "item": "la grande cuvee de dourthe bordeaux sauvignon blanc",
        "probability": 0.65
      },
      {
        "item": "la cave d'augustin florent montagne-saint-emilion",
        "probability": 0.65
      },
      {
        "item": "terra vita vinum bulles de schiste",
        "probability": 0.65
      },
      {
        "item": "rousseau freres touraine noble joue rose",
        "probability": 0.65
      },
      {
        "item": "chateau carbonnieux pessac-leognan blanc (grand cru classe de graves)",
        "probability": 0.65
      },
      {
        "item": "bouvet ladubay bouvet chenin zero saumur extra-brut",
        "probability": 0.65
      }
    ]
  },
  "query": "aliment beef",
  "recommendations": [
    "chateau la canorgue luberon blanc",
    "clarendelle amberwine",
    "chateau de beauregard saumur brut cuvee classique",
    "chateau malartic-lagraviere la reserve de malartic (le sillage) blanc",
    "la grande cuvee de dourthe bordeaux sauvignon blanc",
    "la cave d'augustin florent montagne-saint-emilion",
    "terra vita vinum bulles de schiste",
    "rousseau freres touraine noble joue rose",
    "chateau carbonnieux pessac-leognan blanc (grand cru classe de graves)",
    "bouvet ladubay bouvet chenin zero saumur extra-brut"
  ]
}








QUERY VIN --> ALIMENT


curl -X POST http://127.0.0.1:5050/predict \
  -H "Content-Type: application/json" \
  -d '{"query": "vin Merlot"}'
{
  "debug_info": {
    "input_search_term": "merlot",
    "threshold_used": 0.4,
    "top_predictions": [
      {
        "item": "poultry",
        "probability": 0.6674760244115083
      },
      {
        "item": "beef",
        "probability": 0.6265
      },
      {
        "item": "vegetarian",
        "probability": 0.5330096852300242
      },
      {
        "item": "hard cheese",
        "probability": 0.4667700878676111
      },
      {
        "item": "maturated cheese",
        "probability": 0.44180472400188253
      },
      {
        "item": "shellfish",
        "probability": 0.4259826649958229
      },
      {
        "item": "game meat",
        "probability": 0.4194166666666666
      },
      {
        "item": "lamb",
        "probability": 0.40340031796324693
      },
      {
        "item": "pork",
        "probability": 0.39972881328811816
      },
      {
        "item": "veal",
        "probability": 0.3479223180399651
      }
    ]
  },
  "query": "vin Merlot",
  "recommendations": [
    "poultry",
    "beef",
    "vegetarian",
    "hard cheese",
    "maturated cheese",
    "shellfish",
    "game meat",
    "lamb",
    "pork",
    "veal"
  ]
}


XGBoost Model :




{
  "metrics": {
    "food_to_wine": {
      "f1_macro": 0.0,
      "f1_micro": 0.0,
      "hamming_loss": 0.10757921006944444,
      "jaccard_macro": 0.0,
      "jaccard_micro": 0.0,
      "precision_macro": 0.0,
      "precision_micro": 0.0,
      "recall_macro": 0.0,
      "recall_micro": 0.0,
      "subset_accuracy": 0.0
    },
    "wine_to_food": {
      "f1_macro": 0.4659279456412886,
      "f1_micro": 0.864563626143143,
      "hamming_loss": 0.028731343283582088,
      "jaccard_macro": 0.38843044162648865,
      "jaccard_micro": 0.7614373170082512,
      "precision_macro": 0.49753680612863516,
      "precision_micro": 0.8693823468631098,
      "recall_macro": 0.4563365372632814,
      "recall_micro": 0.8597980283722049,
      "subset_accuracy": 0.6535364415038211
    }
  },
  "status": "Entra\u00eenement termin\u00e9"
}

curl.exe -X POST http://127.0.0.1:5050/train
{
  "metrics": {
    "food_to_wine": {
      "f1_macro": 0.0,
      "f1_micro": 0.0,
      "hamming_loss": 5.425347222222222e-05,
      "jaccard_macro": 0.0,
      "jaccard_micro": 0.0,
      "precision_macro": 0.0,
      "precision_micro": 0.0,
      "recall_macro": 0.0,
      "recall_micro": 0.0,
      "subset_accuracy": 0.0
    },
    "wine_to_food": {
      "f1_macro": 0.4659279456412886,
      "f1_micro": 0.864563626143143,
      "hamming_loss": 0.028731343283582088,
      "jaccard_macro": 0.38843044162648865,
      "jaccard_micro": 0.7614373170082512,
      "precision_macro": 0.49753680612863516,
      "precision_micro": 0.8693823468631098,
      "recall_macro": 0.4563365372632814,
      "recall_micro": 0.8597980283722049,
      "subset_accuracy": 0.6535364415038211
    }
  },
  "status": "Entra\u00eenement termin\u00e9"
}



    """