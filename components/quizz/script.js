
const swiper = document.querySelector('#swiper');
const like = document.querySelector('#like');
const dislike = document.querySelector('#dislike');
const message = document.querySelector('#message');

const urls = [
  '/assets/gouts/agrume.jpeg',
  '/assets/gouts/floral.jpeg',
  '/assets/gouts/fruit_rouge.jpeg',
  '/assets/gouts/boise.jpeg',
  '/assets/gouts/fruit_noir.jpeg'
];

const texts = [
  'Est ce que vous aimez les gouts agrumes ?',
  'Est ce que vous aimez les gouts floraux ?',
  'Est ce que vous aimez les gouts fruits rouges ?',
  'Est ce que vous aimez les gouts boisé ?',
  'Est ce que vous aimez les gouts fruits noirs ?'
];


let cardCount = 0;


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
  const cards = Array.from(swiper.querySelectorAll('.card:not(.dismissing)'));
  const currentCard = cards[cards.length - 1];
  if (!currentCard) return;

  currentCard.classList.add('like', 'dismissing');

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

  setTimeout(() => {
    currentCard.remove();
    appendNewCard();
    checkForRemainingCards();
  }, 500);
}




like.addEventListener('click', likeCard);
dislike.addEventListener('click', dislikeCard);

for (let i = 0; i < 5; i++) {
  appendNewCard();
}
