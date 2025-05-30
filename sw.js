// sw.js
const CACHE_NAME = "file-sorter-v8-cache-v4";

const FILES_TO_CACHE = [
  // Core
  "index.php",
  "manifest.json",

  // JavaScript
  "js/server-output.js",
  "js/ui_handlers.js",
  "js/date-ui.js",
  "js/folder-picker.js",
  "js/meta-workflow.js",
  "js/copy-workflow.js",

  // CSS
  "css/file-sort.css",
  "css/themes/theme_first/css/base.css",
  "css/themes/theme_first/css/theme.css",
  "css/themes/theme_first/css/components.css",
  "css/themes/theme_first/css/layout.css",
  "css/themes/theme_first/css/logo.css",

  // Icons
  "icons/icon-192.png",
  "icons/icon-512.png",
];

// Install → Cache all listed files
self.addEventListener("install", (event) => {
  console.log("[SW] Install");
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("[SW] Pre-caching static resources");
      return Promise.all(
        FILES_TO_CACHE.map((url) =>
          cache
            .add(url)
            .catch((err) =>
              console.warn(`[SW] ❌ Caching failed for ${url}:`, err)
            )
        )
      );
    })
  );
  self.skipWaiting();
});

// Activate → Clean up old caches
self.addEventListener("activate", (event) => {
  console.log("[SW] Activate");
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            console.log("[SW] Deleting old cache:", key);
            return caches.delete(key);
          }
        })
      )
    )
  );
  self.clients.claim();
});

// Fetch → Respond from cache, then network
self.addEventListener("fetch", (event) => {
  if (event.request.method !== "GET") return;

  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      return (
        cachedResponse ||
        fetch(event.request).catch((err) => {
          console.warn("[SW] Fetch error:", err);
          throw err;
        })
      );
    })
  );
});
