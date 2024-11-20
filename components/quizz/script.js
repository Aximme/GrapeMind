// Sélection des éléments DOM
const swiper = document.querySelector('#swiper');
const like = document.querySelector('#like');
const dislike = document.querySelector('#dislike');
const message = document.querySelector('#message');
const finishContainer = document.getElementById('finish-container');
const finishButton = document.getElementById('finish-btn');

// URLs des images des goûts
const urls = [
  '/GrapeMind/assets/gouts/agrume.jpeg',
  '/GrapeMind/assets/gouts/floral.jpeg',
  '/GrapeMind/assets/gouts/fruit_rouge.jpeg',
  '/GrapeMind/assets/gouts/boisé.jpeg',
  '/GrapeMind/assets/gouts/fruit_noir.jpeg'
];

// Textes des cartes correspondant aux goûts
const texts = [
  'Est-ce que vous aimez les goûts agrumes ?',
  'Est-ce que vous aimez les goûts floraux ?',
  'Est-ce que vous aimez les goûts fruits rouges ?',
  'Est-ce que vous aimez les goûts boisés ?',
  'Est-ce que vous aimez les goûts fruits noirs ?'
];

// Résultats du quiz avec les colonnes correspondant aux goûts
let quizResults = {
  agrumes: null,
  floral: null,
  fruit_rouge: null,
  boisé: null,
  fruit_noir: null
};

let cardCount = 0; // Compteur de cartes

// Fonction pour ajouter une nouvelle carte
function appendNewCard() {
  if (cardCount >= urls.length) {
    checkForRemainingCards();
    return;
  }

  const card = new Card({
    imageUrl: urls[cardCount],
    text: texts[cardCount],
    cardIndex: cardCount, // Ajout de l'index pour identification
    onDismiss: checkForRemainingCards,
    onLike: () => {
      console.log(`Card ${cardCount} liked`);
      saveResult(cardCount, 'like');
      like.style.animationPlayState = 'running';
      like.classList.toggle('trigger');
    },
    onDislike: () => {
      console.log(`Card ${cardCount} disliked`);
      saveResult(cardCount, 'dislike');
      dislike.style.animationPlayState = 'running';
      dislike.classList.toggle('trigger');
    }
  });

  swiper.append(card.element);
  cardCount++;

  // Ajuste l'index pour les animations des cartes restantes
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  cards.forEach((card, index) => {
    card.style.setProperty('--i', index);
  });
}

// Fonction pour enregistrer les résultats dans `quizResults`
function saveResult(cardIndex, action) {
  const keyMap = ['agrumes', 'floral', 'fruit_rouge', 'boisé', 'fruit_noir'];
  console.log(`Saving result for card index: ${cardIndex}`);

  if (cardIndex < 0 || cardIndex >= keyMap.length) {
    console.error(`Index invalide : ${cardIndex}`);
    return;
  }

  const key = keyMap[cardIndex];
  quizResults[key] = action === 'like' ? 'oui' : 'non';

  console.log(`Result saved: { key: ${key}, action: ${action} }`);
  console.log('Updated quizResults:', quizResults);
}

// Vérifie s'il reste des cartes et affiche le bouton de fin si nécessaire
function checkForRemainingCards() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0 && cardCount >= urls.length) {
    message.style.display = 'block';
    like.style.display = 'none';
    dislike.style.display = 'none';
    finishContainer.style.display = 'block';
  } else {
    message.style.display = 'none';
  }
}

// Fonction pour envoyer les résultats à la base de données
function sendResultsToDatabase() {
  fetch('save_results.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      user_id: 1, // Remplacez par l'ID utilisateur réel
      ...quizResults
    })
  })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Résultats sauvegardés avec succès !');
          window.location.href = '/GrapeMind/index.php';
        } else {
          console.error('Erreur :', data.error);
        }
      })
      .catch(error => {
        console.error('Erreur lors de l\'envoi des résultats :', error);
      });
}

// Gestion des boutons "like" et "dislike"
like.addEventListener('click', () => {
  const currentCard = Array.from(swiper.querySelectorAll('.card:not(.dismissing)')).pop();
  if (!currentCard) return;
  currentCard.classList.add('like', 'dismissing');
  saveResult(cardCount - swiper.querySelectorAll('.card').length, 'like');
  currentCard.remove();
  appendNewCard();
  checkForRemainingCards();
});

dislike.addEventListener('click', () => {
  const currentCard = Array.from(swiper.querySelectorAll('.card:not(.dismissing)')).pop();
  if (!currentCard) return;
  currentCard.classList.add('dislike', 'dismissing');
  saveResult(cardCount - swiper.querySelectorAll('.card').length, 'dislike');
  currentCard.remove();
  appendNewCard();
  checkForRemainingCards();
});

// Gestion du bouton de fin
finishButton.addEventListener('click', sendResultsToDatabase);

// Génération initiale des cartes
for (let i = 0; i < urls.length; i++) {
  appendNewCard();
}
