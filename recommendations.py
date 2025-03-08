import mysql.connector
import json
from contextlib import closing

FLAVOR_TRANSLATIONS = {
    "Rouge": "Red", "Blanc": "White", "Rosé": "Rosé",
    "Sucré": "fruit rouge", "Salé": "épices", "Acide": "agrume",
    "Amer": "boisé", "Umami": "fruit sec", "Fraise": "fruit rouge",
    "Myrtille": "fruit noir", "Framboise": "fruit rouge",
    "Cerise": "fruit rouge", "Mangue": "tropical", "Boisé": "boisé",
    "Épicé": "épices", "Vieilli": "Vieillissement", "Floral": "Floral",
    "Végétal": "végétal", "fruit d'arbre fruitier": "fruit d'arbre fruitier",
    "Terreux": "Terreux"
}

PRICE_CATEGORIES = {
    "Moins de 10€": (0, 10),
    "10€ - 20€": (10, 20),
    "20€ - 50€": (20, 50),
    "Plus de 50€": (50, 1000)
}

def connect_db():
    return mysql.connector.connect(
        host="localhost", user="root", password="root",
        database="grape-mind", port=8889
    )

def translate_flavor(text):
    text_cleaned = text.strip().capitalize()
    return FLAVOR_TRANSLATIONS.get(text_cleaned, text_cleaned)

def get_price_range(price_answer):
    return PRICE_CATEGORIES.get(price_answer, (0, 1000))

def get_user_answers(cursor):
    cursor.execute("SELECT * FROM quiz_answers WHERE user_id = 0")
    return cursor.fetchone()

def get_filtered_wines(cursor, min_budget, max_budget):
    query = """
        SELECT d.idwine, d.NameWine_WithWinery, d.Type,
               s.flavorGroup_1, s.flavorGroup_2, s.flavorGroup_3, s.Price, s.thumb
        FROM descriptifs d
        JOIN scrap s ON d.idwine = s.idwine
        WHERE s.Price BETWEEN %s AND %s
        ORDER BY s.Price ASC
    """
    cursor.execute(query, (min_budget, max_budget))
    return cursor.fetchall()

def extract_selected_flavors(user_answers):
    selected_flavors = []
    disliked_flavors = []

    question_to_flavor = {
        "question2_answer": "fruit rouge",
        "question3_answer": "fruit d'arbre fruitier",
        "question4_answer": "fruit noir",
        "question5_answer": "Vieillissement",
        "question6_answer": "boisé",
        "question7_answer": "Terreux",
        "question8_answer": "agrume",
        "question9_answer": "tropical",
        "question10_answer": "épices",
        "question11_answer": "fruit sec",
        "question12_answer": "levure",
        "question13_answer": "végétal",
        "question14_answer": "Floral"
    }

    for question, flavor in question_to_flavor.items():
        if user_answers.get(question) == "Oui":
            selected_flavors.append(flavor)
        elif user_answers.get(question) == "Non":
            disliked_flavors.append(flavor)

    return selected_flavors, disliked_flavors

def score_wines(user_answers, wines):
    scores = []
    selected_flavors, disliked_flavors = extract_selected_flavors(user_answers)

    user_colors_raw = user_answers.get("question1_answer", "").split(",")
    user_colors = [translate_flavor(color.strip()) for color in user_colors_raw]

    for wine in wines:
        score = 0
        wine_id = wine["idwine"]
        wine_price = float(wine["Price"]) if wine["Price"] else 1000
        wine_thumb = wine.get("thumb", "")
        wine_type = wine.get("Type", "").strip().lower()

        if any(user_color.lower() in wine_type for user_color in user_colors):
            color_score = 10
        else:
            color_score = -8

        score += color_score

        wine_flavors = [
            str(wine["flavorGroup_1"]).strip().lower(),
            str(wine["flavorGroup_2"]).strip().lower(),
            str(wine["flavorGroup_3"]).strip().lower()
        ]

        matching_flavors = [flavor for flavor in wine_flavors if flavor in selected_flavors]
        flavor_bonus = 3 * len(matching_flavors)
        score += flavor_bonus

        bad_flavors = [flavor for flavor in wine_flavors if flavor in disliked_flavors]
        flavor_malus = -4 * len(bad_flavors)
        score += flavor_malus

        if score > 0:
            scores.append({
                "idwine": wine_id,
                "name": wine["NameWine_WithWinery"],
                "price": wine_price,
                "score": score,
                "thumb": wine_thumb,
                "details": {
                    "color_score": color_score,
                    "flavor_bonus": flavor_bonus,
                    "flavor_malus": flavor_malus,
                    "matching_flavors": matching_flavors,
                    "bad_flavors": bad_flavors,
                    "wine_colors": wine_type,
                    "user_colors_raw": user_colors_raw,
                    "user_colors_translated": user_colors
                }
            })

    scores.sort(key=lambda x: (-x["score"], x["price"]))

    return scores[:20]

def save_recommendations_json(recommendations):
    data = {"recommendations": recommendations}
    with open("recommendations.json", "w") as file:
        json.dump(data, file, indent=4)

def recommend_wines():
    try:
        with closing(connect_db()) as db, closing(db.cursor(dictionary=True)) as cursor:
            user_answers = get_user_answers(cursor)
            if not user_answers:
                return []

            min_budget, max_budget = get_price_range(user_answers.get("question15_answer"))
            wines = get_filtered_wines(cursor, min_budget, max_budget)

            if not wines:
                return []

            recommendations = score_wines(user_answers, wines)
            save_recommendations_json(recommendations)

            return recommendations
    except mysql.connector.Error as err:
        return []

recommend_wines()
