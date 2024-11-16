// DOM
const swiper = document.querySelector('#swiper');
const like = document.querySelector('#like');
const dislike = document.querySelector('#dislike');
const message = document.querySelector('#message'); // Nouveau DOM pour le message

// constants
const urls = [
  
  'quizz_agrumes.png',
  'quizz_floral.png' ,
  'quizz_fruit_rouge.png',
  'quizz_boise.png',
  'quizz_fruit_noir.png'
  
    
];

// variables
let cardCount = 0;

// functions
function appendNewCard() {
  if (cardCount >= urls.length) { // Arrête de générer de nouvelles cartes si toutes ont été affichées
    checkForRemainingCards();
    return;
  }

  const card = new Card({
    imageUrl: urls[cardCount % urls.length],
    onDismiss: checkForRemainingCards,
    onLike: () => {
      like.style.animationPlayState = 'running';
      like.classList.toggle('trigger');
    },
    onDislike: () => {
      dislike.style.animationPlayState = 'running';
      dislike.classList.toggle('trigger');
    }
  });
  swiper.append(card.element);
  cardCount++;

  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  cards.forEach((card, index) => {
    card.style.setProperty('--i', index);
  });
}


// Fonction pour vérifier s'il reste des cartes
function checkForRemainingCards() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0 && cardCount >= urls.length) {
    message.style.display = 'block'; // Affiche le message
    like.style.display = 'none'; // Cache les boutons
    dislike.style.display = 'none';
  } else {
    message.style.display = 'none'; // Cache le message si des cartes existent
  }
}


// Fonction pour simuler le swipe lors d'un clic sur "like"
function likeCard() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0) return;

  const currentCard = cards[0];
  currentCard.style.transition = 'transform 1s';
  currentCard.style.transform = `translate(${window.innerWidth}px, -100px) rotate(45deg)`;
  currentCard.classList.add('dismissing');

  setTimeout(() => {
    currentCard.remove();
    appendNewCard();
    checkForRemainingCards();
  }, 1000);
}

// Fonction pour simuler le swipe lors d'un clic sur "dislike"
function dislikeCard() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0) return;

  const currentCard = cards[0];
  currentCard.style.transition = 'transform 1s';
  currentCard.style.transform = `translate(-${window.innerWidth}px, -100px) rotate(-45deg)`;
  currentCard.classList.add('dismissing');

  setTimeout(() => {
    currentCard.remove();
    appendNewCard();
    checkForRemainingCards();
  }, 1000);
}

// Ajouter des événements de clic sur les boutons "like" et "dislike"
like.addEventListener('click', likeCard);
dislike.addEventListener('click', dislikeCard);

// Générer les 5 premières cartes
for (let i = 0; i < 5; i++) {
  appendNewCard();
}
