"""
Paramétrage global du modèle XGBoost

Contenu :
- Chemin vers le CSV nettoyé contenant les données vins/accords.
- Dictionnaire XGB_PARAMS pour configurer l'entraînement du classifieur XGBoost (nb arbres, profondeur, learning rate, ...)

Utilisation :
- Appelé par les scripts d'entraînement/prédiction du modèle.
"""

# (pour RandomForest)
#N_ESTIMATORS = 100
#Chemin vers csv (W11)
CSV_FILE_PATH = r"C:\Users\maxim\Documents\Développement\ML_chatbot\data\ML_extract_clean_csv.csv"




XGB_PARAMS = {
    'n_estimators': 50,      # Nombre d'arbres
    'max_depth': 9,           # Profondeur maximale de chaque arbre
    'learning_rate': 0.1,     # Taux d'apprentissage
    'subsample': 1.0,         # Sous-échantillonnage des instances
    'colsample_bytree': 1.0,  # Sous-échantillonnage des colonnes
    'random_state': 0,        # Pour garantir la reproductibilité
    'tree_method': 'hist',    # Utilisation de la méthode 'hist' pour le GPU

    #'device': 'cuda'          # Utilisation du GPU --> CUDA
}