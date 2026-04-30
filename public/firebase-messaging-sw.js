/* global importScripts, firebase */
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: 'AIzaSyCxxu_jTrBrGE8Em1kaqn3wTbCBa8_Ra7M',
  authDomain: 'admob-app-id-6663345165.firebaseapp.com',
  databaseURL: 'https://admob-app-id-6663345165.firebaseio.com',
  projectId: 'admob-app-id-6663345165',
  storageBucket: 'admob-app-id-6663345165.appspot.com',
  messagingSenderId: '726949968874',
  appId: '1:726949968874:web:9ee6c0784c6992a96f4f26'
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  const title = payload?.notification?.title || 'Fogos.pt';
  const options = {
    body: payload?.notification?.body || 'Nova notificação disponível.',
    icon: '/favicon.ico'
  };

  return self.registration.showNotification(title, options);
});
