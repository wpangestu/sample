importScripts('https://www.gstatic.com/firebasejs/8.6.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.6.2/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyCIFe2fIgT8U2S5HtThUKB6-hRGiOXIc5o",
    authDomain: "zippy-world-298704.firebaseapp.com",
    projectId: "zippy-world-298704",
    storageBucket: "zippy-world-298704.appspot.com",
    messagingSenderId: "652852667154",
    appId: "1:652852667154:web:88b2b19d35eb9c345ec9cb",
    measurementId: "G-SNT2TZQ2SL"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    // console.log(payload.data.title);
    // console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.message,
        icon: payload.data.image
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});