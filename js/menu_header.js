/*
    Gestion du menu déroulant de navigation (responsive).

    Contenu :
    - Affiche/masque le menu dropdown au clic sur le bouton.
    - Ferme automatiquement le menu si l’utilisateur clique ailleurs.

    Utilisation :
    - Appelé automatiquement à la fin du chargement du DOM.
    - Nécessite les éléments HTML avec les IDs #menu-toggle et #dropdown-menu.
*/

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const dropdownMenu = document.getElementById('dropdown-menu');

    // Toggle le menu déroulant quand on clique sur l'icône de menu
    menuToggle.addEventListener('click', function () {
        if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
            dropdownMenu.style.display = 'block';
        } else {
            dropdownMenu.style.display = 'none';
        }
    });

    // Cacher le menu quand on clique en dehors
    document.addEventListener('click', function (event) {
        if (!menuToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
});
