/*
    Animation de chargement avant affichage complet du site.

    Contenu :
    - Affiche un écran avec un gif loader centré.
    - Applique des styles CSS dynamiquement pour gérer l’effet fade-out.
    - Supprime le loader après le chargement complet de la page.

    Utilisation :
    - Inclu dans toutes les pages nécessitant un effet de chargement.
    - loader.gif dans /assets/images/.
*/

function loadjQuery(callback) {
    let script = document.createElement('script');
    script.src = "https://code.jquery.com/jquery-3.6.0.min.js";
    script.type = 'text/javascript';
    script.onload = function() {
        callback();
    };
    document.head.appendChild(script);
}

loadjQuery(function() {
    const loaderStyles = `
        .loader-wrapper {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loader-wrapper img {
            width: auto;
            height: auto;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 1s ease-out;
        }
    `;
    const styleSheet = document.createElement('style');
    styleSheet.type = 'text/css';
    styleSheet.innerText = loaderStyles;
    document.head.appendChild(styleSheet);

    const loaderHTML = `
        <div class="loader-wrapper">
            <img src="/GrapeMind/assets/images/loader.gif" alt="Chargement...">
        </div>
    `;
    document.body.insertAdjacentHTML('afterbegin', loaderHTML);

    $(window).on("load", function () {
        $(".loader-wrapper").fadeOut("slow", function () {
            $(this).remove();
        });
    });
});
