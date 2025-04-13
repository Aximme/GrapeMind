# 🍷 GRAPE MIND 
## Recommandation Personnalisée de Vins Français

Bienvenue dans **notre plateforme interactive** de recommandations de vins français ! 🥂 Ce projet a pour objectif de transformer la manière dont les amateurs de vin découvrent et choisissent les vins qui leur correspondent le mieux, grâce à une expérience personnalisée et immersive.

---

## 🔗 Accéder au site

👉 [Cliquez ici pour découvrir GrapeMind](grapemind.fr/index.php) 🍇🍷

---

## 🌟 Fonctionnalités Clés

### 🎯 1. Quiz à l'Inscription & Algorithme de Recommandation

Lors de l'inscription, chaque utilisateur est invité à remplir un **quiz interactif** qui explore ses goûts, ses habitudes de consommation et ses préférences en matière de vin (fruité, sec, ...). Ce questionnaire ludique et accessible permet également de répondre *"Je ne sais pas"* ou de passer certaines questions pour ne jamais bloquer l’expérience.

Les réponses collectées sont ensuite analysées par un **algorithme de recommandation basé sur un système de points**. Chaque choix attribue un certain nombre de points à des profils de vins spécifiques. Ces points sont ensuite cumulés pour établir un **profil de goût personnalisé**. Plus un utilisateur interagit avec le site, plus l’algorithme affine ses suggestions (ajouts de vins a la cave, au grenier...). 🍷✨

---

### 🎛️ 2. Personnalisation des Critères de Recherche

Personnalisez à tout moment vos critères de recherche grâce au menu dédié en haut à gauche de la page. Ajustez des filtres tels que le **prix**, la **couleur**, la **région**, le **cépage**, etc., pour affiner vos recommandations.  
Par exemple : **des vins bio du Sud de la France, entre 20€ et 30€, avec un faible impact carbone** 🌱

---

### 🍽️ 3. Accords Mets & Vins

Pour les accords mets/vins, découvrez notre **ChatBot intelligent**, basé sur un modèle **XGBoost multi-label**. Il vous suffit de discuter naturellement avec lui pour qu’il vous recommande des **vins adaptés à vos plats**, ou des **plats qui sublimeront votre bouteille**. Aussi simpel que de discuter avec votre sommelier ! 🤖🍷

---

### 🗺️ 4. Carte des Vins Interactive

Découvrez la France viticole via une **carte interactive** ! Cliquez sur une région pour afficher ses domaines et parcourez les différents domaines pour voir les vins qu'ils proposent ! 🌍

---

### 🗓️ 5. Calendrier des Événements Viticoles

Ne ratez plus jamais un événement 🍇 ! Dégustations, foires, visites de vignobles, tout est répertorié dans un **calendrier interactif** que vous pouvez filtrer par région ou type d'événement. Un rappel par mail pourra être envoyé pour manquer aucun évènement. 📅

---

### ❤️ 6. Favoris - "Ma Cave à Vin"

Créez votre propre **cave à vin virtuelle** avec vos favoris ! Ajoutez des notes personnelles, enregistrez vos découvertes, et organisez votre cave selon différents critères pour toujours retrouver les vins que vous avez aimés. 📦🍷

---

### 🚫 7. Grenier - Vins à Exclure

Vous ne voulez plus voir certains vins ? Envoyez-les au **Grenier** ! Ils disparaissent de vos recommandations, mais vous pouvez toujours les récupérer si vous changez d'avis ! 🎯📉

---

## 🎉 En quoi ce projet est innovant ?

Notre projet se distingue par :

- L'intégration d'un **algorithme de recommandation personnalisée**.
- Un chatbot inédit pour des recommendations précises sur des associations mets/vins.
- Une **dimension culturelle** grâce a l'intégration d'un calendrier des évènements vitivoles. 
- Une interface **interactive, moderne et épurée**  qui facilite l'exploration du monde du vin et la compréension des différents critères.

Embarquez dans un voyage à travers les vignobles de France ! 🇫🇷🍷

---

## 🧰 Technologies Utilisées

- 🐍 **Python** — Back-end pour le machine learning (chatbot)
- 🔥 **Flask** — API pour entrainer & requêter l'algorithme de ML
- 📊 **XGBoost Multi-Label** — Algorithme final choisi
- 🎨 **HTML/CSS/JavaScript** — Frontend dynamique
- 🗺️ **Leaflet** — Carte interactive des régions viticoles
- 🐘 **MySQL** — Base de données relationnelle
- 🔧 **Nginx + Bash + UFW** — Déploiement en production sur VPS OVH