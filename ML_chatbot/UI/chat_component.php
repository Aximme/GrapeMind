<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Chatbot Accords Mets et Vins</title>
  <link rel="stylesheet" href="ML_chatbot/UI/chat_style.css"/>
</head>
<body>
  
  <div id="chatbot-container" class="chatbot-closed">
    <div id="chatbot-header">
      <span id="chatbot-title">💬 Chatbot - Accords mets/vins</span>
      <button id="chatbot-close-button">×</button>
    </div>

    <div id="chatbot-messages"></div>

    <div id="chatbot-input-container">
      <input type="text" id="chatbot-input" placeholder="Tapez votre message..." />
      <button id="chatbot-send">Envoyer</button>
    </div>
  </div>

  <div id="chatbot-bubble" class="chatbot-bubble">
    <span>🍇🍽️</span>
  </div>

  <script src="ML_chatbot/UI/chat.js"></script>
</body>
</html>