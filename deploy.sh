#!/bin/bash
source .env
PASS=$FTP_PASS


echo "---- Début du déploiement ----"

# 1. Changer vers la branche déploy-site
git checkout for-site-web || { echo "Erreur : Impossible de changer de branche"; exit 1; }

# 2. Fusionner les derniers changements de main
git merge main || { echo "Erreur : Échec de la fusion"; exit 1; }

# 3. Remplacer les chemins relatifs par des chemins absolus
find . -name "*.php" -exec sed -i 's|\.\.\/\.\.\/|/GrapeMind/|g' {} \;

# 4. Se connecter au serveur FTP et envoyer les fichiers
HOST='sy11eo.ftp.infomaniak.com'
USER='sy11eo_github'
PASS='Github2025*'

lftp -f "
open $HOST
user $USER $PASS
mirror -R ./ /web/grapemind/
bye
"

# 5. Pousser les modifications sur déploy-site (facultatif)
git add .
git commit -m "Mise à jour des chemins pour déploiement"
git push origin for-site-web

# Retourner sur la branche main
git checkout main

echo "---- Déploiement terminé avec succès ----"
