importScripts('https://www.gstatic.com/firebasejs/3.5.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/3.5.2/firebase-messaging.js');

firebase.initializeApp({
    'messagingSenderId': '726949968874'
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
    const n = (payload && payload.notification) || {};
    const data = (payload && payload.data) || {};

    // Mensagens incident-nearby chegam data-only — o cliente web não calcula
    // proximidade nem mostra notificação para esses casos.
    if (!n.title && !n.body) {
        return Promise.resolve();
    }

    const title = n.title || 'Fogos.pt';
    const options = {
        body: n.body || '',
        icon: n.icon || '/img/logo.svg',
        data: data,
    };

    if (data.fireId) {
        options.data.click_url = '/fogo/' + data.fireId;
    }

    return self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.click_url) || '/';
    event.waitUntil(clients.openWindow(url));
});
