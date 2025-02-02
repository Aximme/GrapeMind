class Card {
  constructor({ imageUrl, text, cardIndex, onDismiss, onLike, onDislike }) {
    this.imageUrl = imageUrl;
    this.text = text;
    this.cardIndex = cardIndex; // Stocke l'index de la carte
    this.onDismiss = onDismiss;
    this.onLike = onLike;
    this.onDislike = onDislike;
    this.#init();
  }

  #startPoint = { x: 0, y: 0 };
  #offsetX = 0;
  #offsetY = 0;

  #init() {
    const card = document.createElement('div');
    card.classList.add('card');

    const img = document.createElement('img');
    img.src = this.imageUrl;
    card.append(img);

    const cardText = document.createElement('div');
    cardText.classList.add('card-text');
    cardText.textContent = this.text;
    card.append(cardText);

    this.element = card;
    this.#listenToEvents();
  }

  #listenToEvents() {
    this.element.addEventListener('mousedown', (e) => this.#startMove(e));
    this.element.addEventListener('touchstart', (e) => this.#startMove(e.changedTouches[0]));
    this.element.addEventListener('dragstart', (e) => e.preventDefault());
  }

  #startMove(e) {
    console.log(`🟢 Début du swipe de la carte ${this.cardIndex}`);
    this.#startPoint = { x: e.clientX, y: e.clientY };

    const moveListener = (e) => this.#handleMove(e.clientX, e.clientY);
    const endListener = () => this.#endMove(moveListener, endListener);

    document.addEventListener('mousemove', moveListener);
    document.addEventListener('mouseup', endListener);
    document.addEventListener('touchmove', (e) => moveListener(e.changedTouches[0]));
    document.addEventListener('touchend', endListener);
  }

  #handleMove(x, y) {
    this.#offsetX = x - this.#startPoint.x;
    this.#offsetY = y - this.#startPoint.y;

    const rotate = this.#offsetX * 0.1;
    this.element.style.transform = `translate(${this.#offsetX}px, ${this.#offsetY}px) rotate(${rotate}deg)`;
  }

  #endMove(moveListener, endListener) {
    document.removeEventListener('mousemove', moveListener);
    document.removeEventListener('mouseup', endListener);
    document.removeEventListener('touchmove', moveListener);
    document.removeEventListener('touchend', endListener);

    const threshold = this.element.clientWidth * 0.3; // Ajuste la sensibilité du swipe

    if (Math.abs(this.#offsetX) > threshold) {
      const direction = this.#offsetX > 0 ? 1 : -1;

      if (direction === 1) {
        console.log(`✅ Carte ${this.cardIndex} swipée à droite (LIKE)`);
        this.onLike(this.cardIndex);
      } else {
        console.log(`❌ Carte ${this.cardIndex} swipée à gauche (DISLIKE)`);
        this.onDislike(this.cardIndex);
      }

      this.#dismiss(direction);
    } else {
      console.log("🔄 Swipe annulé, retour à la position initiale");
      this.element.style.transform = '';
    }
  }

  #dismiss(direction) {
    this.element.style.transition = 'transform 0.5s ease-out';
    this.element.style.transform = `translate(${direction * window.innerWidth}px, ${this.#offsetY}px) rotate(${90 * direction}deg)`;
    this.element.classList.add('dismissing');

    setTimeout(() => {
      this.element.remove();
      if (this.onDismiss) this.onDismiss();
    }, 500);
  }
}
