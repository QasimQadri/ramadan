const admin = require('firebase-admin');

// Service account ka data (GitHub Secrets se aayega)
const serviceAccount = JSON.parse(process.env.FIREBASE_CONFIG);

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount)
});

const message = {
  notification: {
    title: 'Ramadan Mubarak!',
    body: 'Sehri ka waqt honay wala hai.'
  },
  token: 'YOUR_DEVICE_TOKEN_HERE' // Mobile app ka token
};

admin.messaging().send(message)
  .then((response) => console.log('Successfully sent:', response))
  .catch((error) => console.log('Error:', error));
