self.addEventListener('install', event => {
  self.skipWaiting();
});
self.addEventListener('activate', event => {
  console.log('Service Worker aktiviert');
});
self.addEventListener('fetch', event => {
  // Offline-Fallback könnte hier ergänzt werden
});
