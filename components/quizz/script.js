// Sélection des éléments DOM
const swiper = document.querySelector('#swiper');
const like = document.querySelector('#like');
const dislike = document.querySelector('#dislike');
const message = document.querySelector('#message');
const finishContainer = document.getElementById('finish-container');
const finishButton = document.getElementById('finish-btn');

// URLs des images des goûts
const urls = [
  '/../assets/gouts/agrume.jpeg',
  '/../assets/gouts/floral.jpeg',
  '/../assets/gouts/fruit_rouge.jpeg',
  '/../assets/gouts/boisé.jpeg',
  '/../assets/gouts/fruit_noir.jpeg'
];

const texts = [
  'Est-ce que vous aimez les goûts agrumes ?',
  'Est-ce que vous aimez les goûts floraux ?',
  'Est-ce que vous aimez les goûts fruits rouges ?',
  'Est-ce que vous aimez les goûts boisés ?',
  'Est-ce que vous aimez les goûts fruits noirs ?'
];

let quizResults = {
  agrumes: null,
  floral: null,
  fruit_rouge: null,
  boisé: null,
  fruit_noir: null
};

let currentIndex = 0;

// Fonction pour ajouter une nouvelle carte
function appendNewCard() {
  if (currentIndex >= urls.length) {
    checkForRemainingCards();
    return;
  }

  const card = new Card({
    imageUrl: urls[currentIndex],
    text: texts[currentIndex],
    cardIndex: currentIndex,
    onDismiss: () => handleAction(currentIndex, 'dislike'),
    onLike: (index) => handleAction(index, 'like'),
    onDislike: (index) => handleAction(index, 'dislike')
  });

  swiper.append(card.element);
  currentIndex++;
}

function saveResult(cardIndex, action) {
  const keyMap = ['agrumes', 'floral', 'fruit_rouge', 'boisé', 'fruit_noir'];

  if (cardIndex < 0 || cardIndex >= keyMap.length) return;

  const key = keyMap[cardIndex];
  quizResults[key] = action === 'like' ? 'oui' : 'non';

  console.log(`✅ Résultat enregistré : ${key} → ${quizResults[key]}`);
}

function handleAction(cardIndex, action) {
  console.log(`🔹 Action: ${action} pour la carte ${cardIndex}`);
  saveResult(cardIndex, action);
  appendNewCard();
}

function sendResultsToDatabase() {
  console.log("📤 Envoi des données :", JSON.stringify(quizResults));

  fetch('save_results.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(quizResults)
  }).then(response => response.json())
    .then(data => console.log("💡 Réponse serveur :", data))
    .catch(error => console.error("❌ Erreur :", error));
}

like.addEventListener('click', () => handleAction(currentIndex - 1, 'like'));
dislike.addEventListener('click', () => handleAction(currentIndex - 1, 'dislike'));
finishButton.addEventListener('click', sendResultsToDatabase);

appendNewCard();
