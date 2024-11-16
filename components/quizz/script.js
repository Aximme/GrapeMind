
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


let cardCount = 0;


function appendNewCard() {
  if (cardCount >= urls.length) {
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



function checkForRemainingCards() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0 && cardCount >= urls.length) {
    message.style.display = 'block';
    like.style.display = 'none';
    dislike.style.display = 'none';
  } else {
    message.style.display = 'none';
  }
}



function likeCard() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0) return;

  const currentCard = cards[0];
  currentCard.classList.add('like', 'dismissing');

  setTimeout(() => {
    currentCard.remove();
    checkForRemainingCards();
  }, 500);
}


function dislikeCard() {
  const cards = swiper.querySelectorAll('.card:not(.dismissing)');
  if (cards.length === 0) return;

  const currentCard = cards[0];
  currentCard.classList.add('dislike', 'dismissing');

  setTimeout(() => {
    currentCard.remove();
    checkForRemainingCards();
  }, 500);
}



like.addEventListener('click', likeCard);
dislike.addEventListener('click', dislikeCard);

for (let i = 0; i < 5; i++) {
  appendNewCard();
}
