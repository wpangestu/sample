importScripts('https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.1/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyBd-hpKWU00hnmJq9HaEzVbWQwXnbiF0F8",
    authDomain: "tesfirebase-cf18a.firebaseapp.com",
    databaseURL: "https://tesfirebase-cf18a.firebaseio.com",
    projectId: "tesfirebase-cf18a",
    storageBucket: "tesfirebase-cf18a.appspot.com",
    messagingSenderId: "285443598816",
    appId: "1:285443598816:web:7dfa6d01d2bff7ea176385"
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