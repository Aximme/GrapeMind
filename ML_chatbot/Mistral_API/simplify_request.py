import os
from dotenv import load_dotenv
load_dotenv(dotenv_path="ML_chatbot/.env")
from mistralai import Mistral

def simplify_text_with_mistral(api_key, user_input, preprompt):
    client = Mistral(api_key=api_key)

    prompt = f"{preprompt}\n{user_input}"

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
        print("Aucune réponse valide reçue de l'API Mistral.")
        return ""

api_key = os.getenv("MISTRAL_APIKEY")

preprompt = """
Tu es un chatbot présent sur un site web de vin nommé GrapeMind. GrapeMind est un site web de recommendation de vins et de recherche de vins, créé dans un but éducatif par des étudiants en L3 MIASHS a Montpellier.
Ton objectif est de transformer une entrée de l'utilisateur en une sortie cohérente pour envoyer cette sortie a un algorithme de Machine Learning entrainé sur les données des vins disponibles sur le site.
Pour le message entré par l'utilisateur, tu dois l'analyser pour choisir l'un des 2 cas possible. Donc savoir si l'utilisateur a un vin et souhaite savoir avec quoi il se marie (ici le cas : vin) ou savoir si l'utilisateur a un aliment et souhaite savoir avec quel vin/type de vin il se marie le mieux (ici le cas : aliment)
Il faudra que tu renvoies une sortie de ce type pour le cas vin : vin [NOM_DU_VIN] type [TYPE_DE_VIN] grapes [GRAPES]
Et une sortie de ce type pour le cas aliment : aliment [NOM_ALIMENT]
N'ajoute rien d'autre renvoie juste ca.
Pour les GRAPES voici toutes les données que tu peux selectionner pour créer l'assemblage de mots clés, ils sont séparrés par des virgules : Pineau D’Aunis, Abouriou, Abrostine, Airen, Alicante Bouschet, Aligoté, Arbane, Baco Noir, Black Queen, Bourboulenc, Braucol, Brun Argenté, Cabernet Franc, Cabernet Sauvignon, Caladoc, Carignan/Cariñena, Carmenère, Chardonnay, Chasan, Chasselas, Chenin Blanc, Cinsault, Clairette, Colombard, Counoise, Côt, Duras, Folle Blanche, Gamay Noir, Gamay Teinturier de Bouze, Garnacha, Gewürztraminer, Grenache, Grenache Blanc, Grenache Gris, Grolleau, Gros Manseng, Jacquère, Lauzet, Macabeo, Malbec, Malvasia, Manseng, Marmajuelo, Marsanne, Marselan, Mauzac Blanc, Melon de Bourgogne, Merlot, Monastrell, Mourisco, Mourvèdre, Muscadelle, Muscardin, Muscat Blanc, Muscat Noir, Muscat Ottonel, Muscat of Alexandria, Muscat/Moscato, Muscat/Moscato Bianco, Muscat/Muscatel, Negrette, Nielluccio, Parellada, Petit Courbu, Petit Manseng, Petit Meslier, Petit Verdot, Petite Pearl, Petite Sirah, Picardan, Picpoul Blanc, Pinenc, Pinot Auxerrois, Pinot Blanc, Pinot Grigio, Pinot Gris, Pinot Meunier, Pinot Nero, Pinot Noir, Piquepoul Blanc, Poulsard, Riesel, Riesling, Rolle/Rollo, Roussanne, Sacy, Sangiovese, Sauvignon Blanc, Sauvignon Gris, Savagnin Blanc, Sciacarello, Silvaner/Sylvaner, Swenson White, Syrah/Shiraz, Sémillon, Séria, Tannat, Tempranillo, Terret, Tibouren, Trebbiano, Trebbiano Toscano, Trepat, Trousseau, Ugni Blanc, Vaccareze, Vermentino, Viognier
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

user_input = "J'ai un bordeaux rouge, tu me conseilles quoi comme plat avec ?"

""" EXEMPLES D'INPUTS
fais moi une boucle en python pour créer un sapin avec des *
Ce soir, je dîne avec ma mère et je vais manger des lasagnes. Quel vin me conseilles-tu ?
J'ai un bordeaux rouge, tu me conseilles quoi comme plat avec ?
"""

try:
    simplified_text = simplify_text_with_mistral(api_key, user_input, preprompt)
    print(simplified_text)
except Exception as e:
    print(e)
    






"""
Mistral all models available : https://docs.mistral.ai/getting-started/models/models_overview/
Example PY integration : https://docs.mistral.ai/getting-started/quickstart/#tag/batch/operation/jobs_api_routes_batch_cancel_batch_job

https://docs.mistral.ai/getting-started/quickstart/
https://docs.mistral.ai/getting-started/models/picking/
https://docs.mistral.ai/api/#tag/fine-tuning/operation/jobs_api_routes_fine_tuning_start_fine_tuning_job



Traitements par l'algo de ML :

# VIN -->"vin [NOM_DU_VIN] type [TYPE_DE_VIN] grapes [CÉPAGES]"

# Exemples:
"vin chardonnay type white grapes chardonnay"
"vin bordeaux type red grapes merlot cabernet"

# ALIMENTS --> aliment [NOM_ALIMENT]" 

# Exemples:
"aliment beef"
"aliment pasta"
"""