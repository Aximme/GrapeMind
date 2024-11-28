<footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">
            <img src="assets/images/image_header.png" size="1000" alt="Logo du site" class="logo-image">
            <h2>GrapeMind</h2>
        </div>

        <div class="footer-links">
            <h4>Navigation rapide</h4>
            <ul>
                <li><a href="#accueil">Accueil</a></li>
                <li><a href="#apropos">À propos / Contact</a></li>
                <li><a href="#monprofil">Mon profil</a></li>
            </ul>
        </div>

        <div class="footer-legal">
            <h4>Informations</h4>
            <ul>
                <li><a href="#mentions-legales">Mentions légales</a></li>
                <li><a href="#cookies">Politique des cookies</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h4>Contact</h4>
            <p>Email : <a href="mailto:grapemind.upv3@gmail.com">grapemind.upv3@gmail.com</a></p>
            <p>GitHub : <a href="https://github.com/Aximme/GrapeMind" target="_blank">GrapeMind</a></p>
            <div class="footer-social">
                <a href="https://github.com/Aximme/GrapeMind" target="_blank" class="github-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" alt="GitHub" class="social-icon">
                </a>
            </div>
        </div>
    </div>

    <style>
        .footer {
            background-color: #333;
            color: #f2f2f2;
            padding: 40px 20px;
            text-align: center;
            font-family: Arial, sans-serif;
            width: 100%;
            margin-top: auto;
        }

        .footer-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .footer-logo .logo-image {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .footer h4 {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .footer-links ul,
        .footer-legal ul {
            list-style: none;
            padding: 0;
        }

        .footer-links a,
        .footer-legal a,
        .footer-contact a {
            color: #cccccc;
            text-decoration: none;
            font-size: 0.9em;
        }

        .footer-links a:hover,
        .footer-legal a:hover,
        .footer-contact a:hover {
            color: #ffffff;
        }

        .footer-social .social-icon {
            width: 24px;
            height: 24px;
            margin: 10px 5px;
        }

        @media (min-width: 600px) {
            .footer-container {
                flex-direction: row;
                justify-content: space-around;
                align-items: flex-start;
                text-align: left;
            }

            .footer-logo,
            .footer-links,
            .footer-legal,
            .footer-contact {
                max-width: 200px;
            }
        }
    </style>
</footer>
