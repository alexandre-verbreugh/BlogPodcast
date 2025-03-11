// Crée ce fichier comme public/js/botman-turbo-fix.js

document.addEventListener('DOMContentLoaded', function() {
      // Désactive les fonctionnalités Turbo sur tous les liens qui ne sont pas explicitement marqués
      document.addEventListener('click', function(event) {
          // Vérifier si l'élément cliqué est un lien ou est à l'intérieur d'un lien
          let element = event.target;
          while (element && element !== document && element.nodeName !== 'A') {
              element = element.parentNode;
          }
          
          // Si c'est un lien sans attribut data-turbo="true", désactive Turbo pour ce lien
          if (element && element.nodeName === 'A' && !element.hasAttribute('data-turbo') && !element.getAttribute('href').startsWith('#')) {
              // Empêche Turbo de gérer ce lien
              event.preventDefault();
              
              // Redirige manuellement (chargement complet de la page)
              window.location.href = element.getAttribute('href');
          }
      }, true); // Le true est important pour capturer l'événement avant Turbo
      
      // Initialisation standard de BotMan
      window.botmanWidget = {
          frameEndpoint: '/botman',
          chatServer: '/botman/chat', 
          introMessage: 'Salut ! 👋 Je suis le chatbot de Tech Libre. Comment puis-je t\'aider ?',
          title: 'Tech Libre Assistant',
          placeholderText: 'Envoie-moi un message...',
          mainColor: '#00cba9',
          bubbleBackground: '#00cba9',
          headerTextColor: '#fff',
          desktopHeight: 500,
          desktopWidth: 400,
          mobileHeight: '80vh',
          mobileWidth: '90vw',
          displayMessageTime: false,
          frameContainer: 'body',
          autoOpen: false,
          hideWhenNotConnected: false,
          useLocalStorage: true,
          

      };
  });