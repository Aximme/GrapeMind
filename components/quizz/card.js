class Card {
  constructor({ imageUrl, text, cardIndex, onDismiss, onLike, onDislike }) {
    this.imageUrl = imageUrl;
    this.text = text;
    this.cardIndex = cardIndex; // Stocker l'index de la carte
    this.onDismiss = onDismiss;
    this.onLike = onLike;
    this.onDislike = onDislike;
    this.#init();
  }

  #startPoint;
  #offsetX;
  #offsetY;

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
    const { clientX, clientY } = e;
    this.#startPoint = { x: clientX, y: clientY };

    const moveListener = (e) => this.#handleMove(e.clientX, e.clientY);
    const endListener = () => this.#endMove(moveListener, endListener);

    document.addEventListener('mousemove', moveListener);
    document.addEventListener('mouseup', endListener);
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

    const threshold = this.element.clientWidth * 0.5;
    if (Math.abs(this.#offsetX) > threshold) {
      const direction = this.#offsetX > 0 ? 1 : -1;
      direction === 1 ? this.onLike() : this.onDislike();
      this.#dismiss(direction);
    } else {
      this.element.style.transform = '';
    }
  }

  #dismiss(direction) {
    this.element.style.transition = 'transform 1s';
    this.element.style.transform = `translate(${direction * window.innerWidth}px, ${this.#offsetY}px) rotate(${90 * direction}deg)`;
    this.element.classList.add('dismissing');

    setTimeout(() => {
      this.element.remove();
      if (this.onDismiss) this.onDismiss();
    }, 1000);
  }
}
