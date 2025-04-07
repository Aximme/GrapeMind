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
    
    const loadingCard = createLoadingCard();
    messagesContainer.appendChild(loadingCard);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    const progressBar = loadingCard.querySelector('.progress-bar');
    let width = 0;
    const maxTime = 10000;
    const minWidth = 95;
    const initialInterval = 100;
    let currentInterval = initialInterval;
    let increment = (initialInterval / maxTime) * 100;
    
    const progressInterval = setInterval(() => {
      if (width > 85) {
        currentInterval = initialInterval * 2;
        increment = (currentInterval / maxTime) * 50;
      }
      
      width += increment;
      
      if (width > minWidth) width = minWidth;
      
      progressBar.style.width = width + '%';
    }, currentInterval);
  
    let responseData = null;
    let responseReceived = false;
    
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
      responseData = data;
      responseReceived = true;
      
      if (width >= minWidth) {
        finishLoadingAndDisplayResponse();
      }
      else {
        clearInterval(progressInterval);
        const fastInterval = setInterval(() => {
          width += 2;
          if (width >= 100) {
            clearInterval(fastInterval);
            finishLoadingAndDisplayResponse();
          }
          progressBar.style.width = width + '%';
        }, 20);
      }
    })
    .catch(error => {
      responseReceived = true;
      console.error('Erreur:', error);
      
      if (width >= minWidth) {
        finishLoadingAndDisplayResponse(true);
      } else {
        clearInterval(progressInterval);
        const fastInterval = setInterval(() => {
          width += 2;
          if (width >= 100) {
            clearInterval(fastInterval);
            finishLoadingAndDisplayResponse(true);
          }
          progressBar.style.width = width + '%';
        }, 20);
      }
    });
    
    function finishLoadingAndDisplayResponse(isError = false) {
      progressBar.style.width = '100%';
      
      setTimeout(() => {
        messagesContainer.removeChild(loadingCard);
        
        if (!isError) {
          if (responseData && responseData.reply) {
            displayMessage(responseData.reply, 'bot');
          } else {
            displayMessage("Désolé, je n'ai pas pu obtenir de réponse.", 'bot');
          }
        } else {
          displayMessage("Désolé, je n'ai pas pu obtenir de réponse.", 'bot');
        }
      }, 400);
    }
  }


function createLoadingCard() {
  const loadingCard = document.createElement('div');
  loadingCard.className = 'loading-card';
  
  const textContainer = document.createElement('div');
  textContainer.className = 'loading-card-text';
  
  const textSpan = document.createElement('span');
  textSpan.textContent = 'Votre requête est en cours de traitement';
  
  const loadingDots = document.createElement('span');
  loadingDots.className = 'loading-dots';
  loadingDots.innerHTML = '<span></span><span></span><span></span>';
  
  textContainer.appendChild(textSpan);
  textContainer.appendChild(loadingDots);
  
  const progressBarContainer = document.createElement('div');
  progressBarContainer.className = 'progress-bar-container';
  
  const progressBar = document.createElement('div');
  progressBar.className = 'progress-bar';
  
  progressBarContainer.appendChild(progressBar);
  textContainer.appendChild(progressBarContainer);
  
  const gifContainer = document.createElement('img');
  gifContainer.className = 'loading-card-gif';
  gifContainer.src = '../../assets/images/loader.gif';
  gifContainer.alt = 'Loading';
  
  loadingCard.appendChild(textContainer);
  loadingCard.appendChild(gifContainer);
  
  return loadingCard;
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
  fetch("components/wine/set_vin_id.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "vin_id=" + encodeURIComponent(vinId)
  })
  .then(() => window.location.href = "components/wine/wine-details.php")
  .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
}

function decodeHTML(text) {
  var txt = document.createElement("textarea");
  txt.innerHTML = text;
  return txt.value;
}
