let currentSlide = 0; // index du slide initial
const itemsPerSlide = 5; // nb elements par slide

// gestion des mvts de slides
function displaySlide() {
    const track = document.querySelector('.carousel-track');
    const items = track.querySelectorAll('.carousel-item');
    const totalItems = items.length;
    const totalSlides = Math.ceil(totalItems / itemsPerSlide);
    const start = currentSlide * itemsPerSlide;
    const end = Math.min(start + itemsPerSlide, totalItems);

    track.style.transform = `translateX(-${start * (100 / itemsPerSlide)}%)`;
}

function nextSlide() {
    const items = document.querySelectorAll('.carousel-item');
    const totalSlides = Math.ceil(items.length / itemsPerSlide);
    currentSlide = (currentSlide + 1) % totalSlides;
    displaySlide();
}

function previousSlide() {
    const items = document.querySelectorAll('.carousel-item');
    const totalSlides = Math.ceil(items.length / itemsPerSlide);
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    displaySlide();
}

document.addEventListener('DOMContentLoaded', displaySlide);