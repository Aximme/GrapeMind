const swiper = document.querySelector('#swiper');
const like = document.querySelector('#like');
const dislike = document.querySelector('#dislike');
const message = document.querySelector('#message');
const finishContainer = document.getElementById('finish-container');
const finishButton = document.getElementById('finish-btn');
const statusMessage = document.getElementById('status');

const urls = [
  '/GrapeMind/assets/gouts/agrume.jpeg',
  '/GrapeMind/assets/gouts/floral.jpeg',
  '/GrapeMind/assets/gouts/fruit_rouge.jpeg',
  '/GrapeMind/assets/gouts/boisé.jpeg',
  '/GrapeMind/assets/gouts/fruit_noir.jpeg'
];

const texts = [
  'Est ce que vous aimez les gouts agrumes ?',
  'Est ce que vous aimez les gouts floraux ?',
  'Est ce que vous aimez les gouts fruits rouges ?',
  'Est ce que vous aimez les gouts boisé ?',
  'Est ce que vous aimez les gouts fruits noirs ?'
];

let cardCount = 0; // Compteur de cartes
let quizResults = []; // Stocke les résultats du quiz (like/dislike)

// Fonction pour créer une nouvelle carte
function appendNewCard() {
  if (cardCount >= urls.length) {
    checkForRemainingCards();
    return;
  }

  const card = new Card({
    imageUrl: urls[cardCount % urls.length],
    text: texts[cardCount % texts.length],
    onDismiss: checkForRemainingCards,
    onLike: () => {
      like.style.animationPlayState = 'running';
      like.classList.toggle('trigger');
      saveResult(cardCount, 'like'); // Enregistre le like
    },
    onDislike: () => {
      dislike.style.animationPlayState = 'running';
      dislike.classList.toggle('trigger');
      saveResult(cardCount, 'dislike'); // Enregistre le dislike
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

// Fonction pour enregistrer les résultats du quiz
function saveResult(cardIndex, action) {
  quizResults.push({
    card: texts[cardIndex % texts.length], // Question associée
    action: action                         // Action : "like" ou "dislike"
  });
}

// Vérifie s'il reste des cartes et affiche le bouton de fin si nécessaire
function checkForRemainingCards() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0 && cardCount >= urls.length) {
    message.style.display = 'block';
    like.style.display = 'none';
    dislike.style.display = 'none';

    // Affiche le bouton pour envoyer les résultats
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
      user_id: 1,
      results: quizResults
    })
  })
      .then(response => response.json())
      .then(data => {
        console.log('Réponse JSON:', data);
        if (data.success) {
          console.log('Résultats sauvegardés avec succès !');
          // Redirige vers index.php après succès
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
function likeCard() {
  const cards = Array.from(swiper.querySelectorAll('.card:not(.dismissing)'));
  const currentCard = cards[cards.length - 1];
  if (!currentCard) return;

  currentCard.classList.add('like', 'dismissing');
  saveResult(cardCount - cards.length, 'like'); // Enregistre le like

  setTimeout(() => {
    currentCard.remove();
    appendNewCard();
    checkForRemainingCards();
  }, 500);
}

function dislikeCard() {
  const cards = Array.from(swiper.querySelectorAll('.card:not(.dismissing)'));
  const currentCard = cards[cards.length - 1];
  if (!currentCard) return;

  currentCard.classList.add('dislike', 'dismissing');
  saveResult(cardCount - cards.length, 'dislike'); // Enregistre le dislike

  setTimeout(() => {
    currentCard.remove();
    appendNewCard();
    checkForRemainingCards();
  }, 500);
}

// Gestion des clics sur les boutons
like.addEventListener('click', likeCard);
dislike.addEventListener('click', dislikeCard);

// Gestion du bouton de fin
finishButton.addEventListener('click', () => {
  sendResultsToDatabase();
});

// Génération initiale des cartes
for (let i = 0; i < 5; i++) {
  appendNewCard();
}
