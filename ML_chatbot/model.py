"""
Calcul des métriques de performance pour un modèle multi-label.

Contenu :
- Précision, rappel, F1-score (micro/macro)
- Subset accuracy, hamming loss, jaccard (micro/macro)
- Génère la matrice de confusion multilabel (non retournée)

Utilisation :
- Appelé après l'entraînement pour évaluer les prédictions.
- Importé par main.py
"""

from sklearn.metrics import precision_score, recall_score, f1_score, accuracy_score, hamming_loss, jaccard_score, multilabel_confusion_matrix

def compute_metrics(Y_true, Y_pred):
    """
    Calcule les métriques de performance pour un modèle multi-label.
    Retourne un dictionnaire contenant :
      - Précision micro/macro moyennée
      - Rappel micro/macro moyenné
      - F1-score micro/macro moyenné
      - Exactitude par sous-ensemble (subset accuracy)
      - Perte de Hamming
      - Jaccard micro/macro (intersection sur union)
    """
    metrics = {}
    metrics['precision_micro'] = precision_score(Y_true, Y_pred, average='micro', zero_division=0)
    metrics['precision_macro'] = precision_score(Y_true, Y_pred, average='macro', zero_division=0)
    metrics['recall_micro'] = recall_score(Y_true, Y_pred, average='micro', zero_division=0)
    metrics['recall_macro'] = recall_score(Y_true, Y_pred, average='macro', zero_division=0)
    metrics['f1_micro'] = f1_score(Y_true, Y_pred, average='micro', zero_division=0)
    metrics['f1_macro'] = f1_score(Y_true, Y_pred, average='macro', zero_division=0)
    metrics['subset_accuracy'] = accuracy_score(Y_true, Y_pred)
    metrics['hamming_loss'] = hamming_loss(Y_true, Y_pred)
    metrics['jaccard_micro'] = jaccard_score(Y_true, Y_pred, average='micro')
    metrics['jaccard_macro'] = jaccard_score(Y_true, Y_pred, average='macro')
    # Matrices de confusion multi-étiquettes (non retournées ici, calculées pour analyse)
    cm = multilabel_confusion_matrix(Y_true, Y_pred)
    return metrics