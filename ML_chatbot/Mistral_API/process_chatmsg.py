from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import requests
from dotenv import load_dotenv
load_dotenv(dotenv_path="ML_chatbot/.env")
from mistralai import Mistral
import mysql.connector
from dotenv import load_dotenv
load_dotenv(dotenv_path="ML_chatbot/.env")


app = Flask(__name__)
CORS(app)

def simplify_text_with_mistral(api_key, user_input, preprompt):
    client = Mistral(api_key=api_key)
    response = client.chat.complete(
        model="mistral-small-latest", 
        messages=[
            {"role": "system", "content": preprompt},
            {"role": "user", "content": user_input}
        ]
    )
    if response.choices:
        return response.choices[0].message.content.strip()
    else:
        return "Aucune réponse valide reçue de l'API"

api_key = os.getenv("MISTRAL_APIKEY")

preprompt = """Tu es un chatbot présent sur un site web de vin nommé GrapeMind. GrapeMind est un site web de recommendation de vins et de recherche de vins, créé dans un but éducatif par des étudiants en L3 MIASHS a Montpellier.
Ton objectif est de transformer une entrée de l'utilisateur en une sortie cohérente pour envoyer cette sortie a un algorithme de Machine Learning entrainé sur les données des vins disponibles sur le site.
Pour le message entré par l'utilisateur, tu dois l'analyser pour choisir l'un des 2 cas possible. Donc savoir si l'utilisateur a un vin et souhaite savoir avec quoi il se marie (ici le cas : vin) ou savoir si l'utilisateur a un aliment et souhaite savoir avec quel vin/type de vin il se marie le mieux (ici le cas : aliment)
Il faudra que tu renvoies une sortie de ce type pour le cas vin : vin [NOM_DU_VIN] type [TYPE_DE_VIN] grapes [GRAPES]
Et une sortie de ce type pour le cas aliment : aliment [NOM_ALIMENT]
N'ajoute rien d'autre renvoie juste ca.
Pour les GRAPES voici toutes les données que tu peux selectionner pour créer l'assemblage de mots clés, ils sont séparés par des virgules : Pineau D’Aunis, Abouriou, Abrostine, Airen, Alicante Bouschet, Aligoté, Arbane, Baco Noir, Black Queen, Bourboulenc, Braucol, Brun Argenté, Cabernet Franc, Cabernet Sauvignon, Caladoc, Carignan/Cariñena, Carmenère, Chardonnay, Chasan, Chasselas, Chenin Blanc, Cinsault, Clairette, Colombard, Counoise, Côt, Duras, Folle Blanche, Gamay Noir, Gamay Teinturier de Bouze, Garnacha, Gewürztraminer, Grenache, Grenache Blanc, Grenache Gris, Grolleau, Gros Manseng, Jacquère, Lauzet, Macabeo, Malbec, Malvasia, Manseng, Marmajuelo, Marsanne, Marselan, Mauzac Blanc, Melon de Bourgogne, Merlot, Monastrell, Mourisco, Mourvèdre, Muscadelle, Muscardin, Muscat Blanc, Muscat Noir, Muscat Ottonel, Muscat of Alexandria, Muscat/Moscato, Muscat/Moscato Bianco, Muscat/Muscatel, Negrette, Nielluccio, Parellada, Petit Courbu, Petit Manseng, Petit Meslier, Petit Verdot, Petite Pearl, Petite Sirah, Picardan, Picpoul Blanc, Pinenc, Pinot Auxerrois, Pinot Blanc, Pinot Grigio, Pinot Gris, Pinot Meunier, Pinot Nero, Pinot Noir, Piquepoul Blanc, Poulsard, Riesel, Riesling, Rolle/Rollo, Roussanne, Sacy, Sangiovese, Sauvignon Blanc, Sauvignon Gris, Savagnin Blanc, Sciacarello, Silvaner/Sylvaner, Swenson White, Syrah/Shiraz, Sémillon, Séria, Tannat, Tempranillo, Terret, Tibouren, Trebbiano, Trebbiano Toscano, Trepat, Trousseau, Ugni Blanc, Vaccareze, Vermentino, Viognier
Pour les HARMONIZE voici toutes les données que tu peux selectionner pour créer l'assemblage de mots clés, ils sont séparés par des virgules : Aperitif, Appetizer, Asian Food, Barbecue, Beef, Blue Cheese, Cake, Chicken, Citric Dessert, Cured Meat, Dessert, Duck, Fish, Fruit, Fruit Dessert, Game Meat, Goat Cheese, Grilled, Hard Cheese, Lamb, Lean Fish, Light Stews, Maturated Cheese, Meat, Mild Cheese, Mushrooms, Pasta, Pizza, Pork, Poultry, Rich Fish, Risotto, Salad, Seafood, Shellfish, Snack, Soft Cheese, Soufflé, Spicy Food, Sushi, Sweet Dessert, Tomato Dishes, Veal, Vegetarian
Pour les TYPE voici toutes les données que tu peux selectionner pour créer l'assemblage de mots clés, ils sont séparés par des virgules : Dessert, Dessert/Port, Red, Rosé, Sparkling, White
Pour les GRAPES, TYPE et HARMONIZE tu ne peux selectionner qu'un seul mot des listes pour la création de l'assemblage de mots clés. Attention, il faut que le mot soit obligatoirement dans les listes tu ne peux pas en prendre d'autres. SI les mots des listes sont en anglais et l'entrée de l'utilisateur est en français, tu dois traduire les mots en anglais pour créer l'assemblage de mots clés.

<example1>
User Input : Je mange ce soir du poulet avec ma mère, tu me conseilles quoi comme vin ?
Output : aliment chicken
</example1>

<example2>
User Input : J'ai un vin rouge de Bordeaux, tu me conseilles quoi comme plat avec ?
Output : vin bordeaux type red grapes merlot cabernet
</example2>

Si une entrée d'utilisateur est hors le contexte de la recommendation d'accord mets et vins, répond : "Votre demande semble ne pas être en lien direct avec le site. Merci de préciser votre demande."

Voici le message entré par l'utilisateur :
"""

@app.route('/chat', methods=['POST'])
def chat():
    data = request.json
    user_message = data.get('message', '')
    
    if not user_message:
        return jsonify({"reply": "Message vide"})
        
    try:
        user_input = simplify_text_with_mistral(api_key, user_message, preprompt)
        print(f"Sortie apres mistral : {user_input}")

        if user_input == "Votre demande semble ne pas être en lien direct avec le site. Merci de préciser votre demande.":
            return jsonify({"reply": user_input})
        

        ml_output = requests.post(
            'http://127.0.0.1:5000/predict',
            json={"query": user_input}
        )

        if ml_output.status_code == 200:
            prediction_data = ml_output.json()
            reco = prediction_data.get("recommendations")
            if reco is None:
                return jsonify({"reply": "Aucune recommandation trouvée."})
            
            if isinstance(reco, list):
                wines = [wine.strip() for wine in reco]
            else:
                wines = [wine.strip() for wine in reco.split(',')]
            
            # On recup le 1er mot de l'input pour savoir comment formuler
            query_type = user_input.lower().split()[0] if user_input else ""
            if query_type == "vin":
                response_message = "Voici des suggestions d'aliments qui se marient bien avec ce vin :<br>"
                for item in wines[:5]:
                    response_message += f"- {item}<br>"
            else:
                response_message = "Voici des recommendations de vins en accord avec la nourriture que vous avez mentionnée :<br>"
                for wine in wines:
                    wine_record = get_wine_id_by_name(wine)
                    if wine_record:
                        wine_id, wine_name = wine_record
                        link_html = f'<a href="#" onclick="selectSuggestion({wine_id})">Voir le vin</a>'
                        response_message += f"- {wine_name}<br>{link_html}<br>"
                    else:
                        response_message += f"- {wine}<br><em>Non trouvé dans notre base</em><br>"
                        
            return jsonify({"reply": response_message})
        else:
            return jsonify({"reply": f"Une erreur est survenue : {ml_output.text}"})
                
    except Exception as e:
        return jsonify({"reply": f"Erreur: {str(e)}"})


def get_wine_id_by_name(wine_name):
    import mysql.connector
    conn = mysql.connector.connect(
        host=os.getenv("DB_HOST"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASSWORD"),
        database=os.getenv("DB_NAME")
    )
    cursor = conn.cursor()
    query = "SELECT DISTINCT s.idwine, s.name FROM scrap AS s WHERE s.name LIKE %s LIMIT 1"
    cursor.execute(query, (wine_name,))
    result = cursor.fetchone()
    cursor.close()
    conn.close()
    return result


if __name__ == "__main__":
    app.run(host='127.0.0.1', port=5001, debug=True) #Port 5000 : ML || Port 5001 : preprocess api Mistral
