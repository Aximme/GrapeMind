import pandas as pd
import unicodedata

ALLOWED_CHARS = set("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 .,;:!?()[]'\"-")

def remove_accents(text):
    if isinstance(text, str):
        text = unicodedata.normalize('NFKD', text)
        text = text.encode('ASCII', 'ignore').decode('utf-8')
    return text

def row_contains_disallowed(row):
    for cell in row:
        if isinstance(cell, str):
            for c in cell:
                if c not in ALLOWED_CHARS:
                    return True
    return False

def clean_dataframe(df):
    df_clean = df.applymap(remove_accents)
    mask = df_clean.apply(lambda row: not row_contains_disallowed(row), axis=1)
    return df_clean[mask]

def main():
    input_file = "ML_extract.xlsx"
    output_file = "ML_extract_clean.xlsx"
    
    try:
        df = pd.read_excel(input_file)
    except Exception as e:
        print(f"Erreur lors de la lecture du fichier : {e}")
        return
    
    df_clean = clean_dataframe(df)
    
    try:
        df_clean.to_excel(output_file, index=False)
        print(f"Fichier nettoyé sauvegardé sous '{output_file}'.")
    except Exception as e:
        print(f"Erreur lors de l'écriture du fichier : {e}")

if __name__ == '__main__':
    main()