// Pour use le chat il faut démarrer flask (flemme de faire autre chose pour exec du python depuis js):
// cd python3 ML_chatbot/Mistral_API/process_chatmsg.py


document.addEventListener('DOMContentLoaded', function() {
  const chatbotContainer = document.getElementById('chatbot-container');
  const chatbotBubble = document.getElementById('chatbot-bubble');
  const closeButton = document.getElementById('chatbot-close-button');
  const sendButton = document.getElementById('chatbot-send');
  const inputField = document.getElementById('chatbot-input');
  const messagesContainer = document.getElementById('chatbot-messages');

  chatbotBubble.addEventListener('click', function() {
    chatbotBubble.style.display = 'none';
    chatbotContainer.classList.remove('chatbot-closed');
    chatbotContainer.style.display = 'flex';
  });

  closeButton.addEventListener('click', function() {
    chatbotContainer.style.display = 'none';
    chatbotContainer.classList.add('chatbot-closed');
    chatbotBubble.style.display = 'flex';
  });

  sendButton.addEventListener('click', sendMessage);
  inputField.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      sendMessage();
    }
  });

  function sendMessage() {
      const userMessage = inputField.value.trim();
      if (userMessage === '') return;
    
      displayMessage(userMessage, 'user');
      inputField.value = '';
    
      fetch('http://127.0.0.1:5001/chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: userMessage })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data && data.reply) {
          displayMessage(data.reply, 'bot');
        } else {
          displayMessage("Désolé, je n'ai pas pu obtenir de réponse.", 'bot');
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        displayMessage("Erreur lors de l'envoi du message.", 'bot');
      });
  }

  function displayMessage(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chatbot-message', sender);
    messageDiv.innerHTML = text;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
});

function selectSuggestion(vinId) {
  fetch("/GrapeMind/components/wine/set_vin_id.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "vin_id=" + encodeURIComponent(vinId)
  })
  .then(() => window.location.href = "/GrapeMind/components/wine/wine-details.php")
  .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
}

function decodeHTML(text) {
  var txt = document.createElement("textarea");
  txt.innerHTML = text;
  return txt.value;
}
