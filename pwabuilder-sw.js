// pwabuilder-sw.js
// v2.1 - Temporarily deactivated OneSignal and expanded pre-caching rules

// DISABLING ONE SIGNAL FOR THIS RELEASE: Commented out to block user prompts
// importScripts("https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.sw.js");

// INCREMENT THIS VERSION TO TRIGGER CLEANUP
const CACHE_NAME = "seetow-app-v3"; // Incremented to flush old asset layers
const API_CACHE_NAME = "seetow-api-v1";
const OFFLINE_PAGE = "offline.html";

// The essential core files required to boot your application completely offline
const PRECACHE_ASSETS = [
  "./",
  "index.html",
  "manifest.json",
  "static/geojson2.js",
  "src/search-engine.js",
  "src/map-engine.js",
  "src/favorites.js",
  "src/transit-api.js",
  "src/geo.js"
];

// --- INSTALL: Force Immediate Update & Populating Cache Pipeline ---
self.addEventListener("install", (event) => {
  self.skipWaiting();

  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("[SW] Deploying core application files to cache storage...");
      // Pre-cache your modern ES modules along with the offline sheet layout
      return cache.addAll(PRECACHE_ASSETS).catch(err => {
        console.error("[SW] Pre-cache queue initialization failure:", err);
        // Fallback to minimal layout block to prevent installation crashes if a path slips
        return cache.add(OFFLINE_PAGE);
      });
    })
  );
});

// --- ACTIVATE: The Cleanup Sweep ---
self.addEventListener("activate", (event) => {
  event.waitUntil(clients.claim());

  // Delete ALL old caches that don't match our updated active names
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME && cacheName !== API_CACHE_NAME) {
            console.log(`[SW] Purging old obsolete cache frame: ${cacheName}`);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// --- FETCH: Smart Request Logic ---
self.addEventListener("fetch", (event) => {
  if (event.request.method !== "GET") return;

  const url = new URL(event.request.url);

  // 1. STRATEGY A: API REQUESTS (Network First + Expiry Cache Fallback)
  if (url.hostname.includes("seetow.workers.dev")) {
    event.respondWith(handleApiRequest(event.request));
    return;
  }

  // 2. STRATEGY B: APP SHELL (Stale-While-Revalidate)
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      const fetchPromise = fetch(event.request)
        .then((networkResponse) => {
          // If response is clean and valid, commit it to the cache record
          if (networkResponse.status === 200) {
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, networkResponse.clone());
            });
          }
          return networkResponse;
        })
        .catch(() => {
          if (event.request.mode === 'navigate') {
            return caches.match(OFFLINE_PAGE);
          }
        });

      return cachedResponse || fetchPromise;
    })
  );
});

// --- HELPER: Async API Engine Logic ---
async function handleApiRequest(request) {
  const urlObj = new URL(request.url);
  urlObj.searchParams.delete('_'); 
  const cleanRequest = new Request(urlObj.toString(), {
    mode: 'cors',
    credentials: 'omit',
    headers: request.headers
  });

  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      const cache = await caches.open(API_CACHE_NAME);
      await cache.put(cleanRequest, networkResponse.clone());
    }
    
    return networkResponse;

  } catch (error) {
    const cache = await caches.open(API_CACHE_NAME);
    const cachedResponse = await cache.match(cleanRequest);

    if (cachedResponse) {
      const dateHeader = cachedResponse.headers.get('date');
      if (dateHeader) {
        const cachedTime = new Date(dateHeader).getTime();
        const ageInSeconds = (Date.now() - cachedTime) / 1000;

        if (ageInSeconds < 3600) {
           const newHeaders = new Headers(cachedResponse.headers);
           newHeaders.append("X-Data-Source", "offline-cache");
           
           return new Response(cachedResponse.body, {
               status: cachedResponse.status,
               statusText: cachedResponse.statusText,
               headers: newHeaders
           });
        }
      }
    }
    
    return new Response(JSON.stringify({ error: "Offline and cache expired" }), {
      headers: { "Content-Type": "application/json" }
    });
  }
}
