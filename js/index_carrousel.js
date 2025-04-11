let currentSlide = 0;
const itemsPerSlide = 4;

document.addEventListener('DOMContentLoaded', function() {
    loadRecommendedWines();
});

function loadRecommendedWines() {
    const winesData = getWinesDataFromPage();

    if (winesData && winesData.length > 0) {
        populateCarousel(winesData);
    } else {
        fetch("get_recommended_wines.php")
            .then(response => response.json())
            .then(wines => {
                if (wines.length === 0) {
                    console.error("Aucune recommandation reçue.");
                    return;
                }
                console.log("Vins reçus : ", wines);
                populateCarousel(wines);
            })
            .catch(error => console.error("Erreur lors du chargement des recommandations :", error));
    }
}

function getWinesDataFromPage() {
    return null;
}

function populateCarousel(wines) {
    const track = document.querySelector('.carousel-track');
    if (!track) {
        console.error("Élément .carousel-track introuvable !");
        return;
    }

    track.innerHTML = '';
    console.log("🛠 Ajout des vins au carrousel...");

    wines.forEach(wine => {
        console.log("🍷 Ajout du vin :", wine.name);

        const wineItem = document.createElement('div');
        wineItem.classList.add('carousel-item');

        wineItem.innerHTML = `
            <a href="/components/wine/set_vin_id.php?id=${wine.idwine}" class="carousel-item-link">
                <img src="${wine.thumb}" alt="${wine.name}" class="wine-thumbnail">
                <div class="wine-details">
                    <h3>${wine.name}</h3>
                    <p class="wine-price">${wine.price} €</p>
                </div>
            </a>
        `;

        track.appendChild(wineItem);
    });

    updateCarouselWidth();
    displaySlide();
}

function updateCarouselWidth() {
    const track = document.querySelector('.carousel-track');
    const items = document.querySelectorAll('.carousel-item');

    if (!track || items.length === 0) return;

    const itemWidth = items[0].offsetWidth || 200;
    const totalWidth = items.length * itemWidth;

    track.style.width = `${totalWidth}px`;

    console.log("📏 Largeur mise à jour de .carousel-track:", totalWidth, "px");
}

function displaySlide() {
    const track = document.querySelector('.carousel-track');
    const items = document.querySelectorAll('.carousel-item');
    const carousel = document.querySelector('.carousel');

    if (!track || !carousel || items.length === 0) {
        console.error("Éléments du carousel manquants");
        return;
    }

    const itemWidth = items[0].getBoundingClientRect().width;
    const visibleWidth = carousel.getBoundingClientRect().width;
    const totalWidth = itemWidth * items.length;

    const maxOffset = Math.max(0, totalWidth - visibleWidth);
    let offset = currentSlide * itemWidth * itemsPerSlide;

    offset = Math.max(0, Math.min(offset, maxOffset));

    track.style.transform = `translateX(-${offset}px)`;
    console.log(`Slide ${currentSlide}: offset=${offset}, itemWidth=${itemWidth}, totalWidth=${totalWidth}`);
}

function nextSlide() {
    const items = document.querySelectorAll('.carousel-item');
    const totalSlides = Math.ceil(items.length / itemsPerSlide);

    if (items.length === 0) return;

    currentSlide = (currentSlide < totalSlides - 1) ? currentSlide + 1 : 0;
    displaySlide();
}

function previousSlide() {
    const items = document.querySelectorAll('.carousel-item');
    const totalSlides = Math.ceil(items.length / itemsPerSlide);

    if (items.length === 0) return;

    currentSlide = (currentSlide > 0) ? currentSlide - 1 : totalSlides - 1;
    displaySlide();
}

window.addEventListener('resize', function() {
    updateCarouselWidth();
    displaySlide();
});
