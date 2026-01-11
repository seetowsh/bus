// pwabuilder-sw.js
// v2.0 - Fixes storage leak and implements smart API caching

importScripts("https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.sw.js");

// INCREMENT THIS VERSION TO TRIGGER CLEANUP
// Changing this name is what tells the SW to delete the old "pwabuilder-offline-page" cache
const CACHE_NAME = "seetow-app-v2"; 
const API_CACHE_NAME = "seetow-api-v1";
const OFFLINE_PAGE = "offline.html";

// --- INSTALL: Force Immediate Update ---
self.addEventListener("install", (event) => {
  // Force this new SW to become the "waiting" worker immediately
  self.skipWaiting();

  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // Pre-cache the offline page
      return cache.add(OFFLINE_PAGE);
    })
  );
});

// --- ACTIVATE: The "Debt Jubilee" (Cleanup) ---
self.addEventListener("activate", (event) => {
  // Take control of all pages immediately (don't wait for refresh)
  event.waitUntil(clients.claim());

  // Delete ALL old caches that don't match our new names
  // This is what frees up the 1GiB on user devices
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME && cacheName !== API_CACHE_NAME) {
            console.log(`[SW] Cleaning up old cache: ${cacheName}`);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// --- FETCH: The Smart Logic ---
self.addEventListener("fetch", (event) => {
  // 1. Only handle GET requests
  if (event.request.method !== "GET") return;

  const url = new URL(event.request.url);

  // 2. STRATEGY A: API REQUESTS (Network First + Expiry)
  if (url.hostname.includes("seetow.workers.dev")) {
    event.respondWith(handleApiRequest(event.request));
    return;
  }

  // 3. STRATEGY B: APP SHELL (Stale-While-Revalidate)
  // CSS, JS, HTML, Images - Serve fast from cache, update in background
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      // Return cached response immediately if available
      const fetchPromise = fetch(event.request)
        .then((networkResponse) => {
          // Update the cache with the new version
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, networkResponse.clone());
          });
          return networkResponse;
        })
        .catch(() => {
            // If network fails and we have nothing in cache, show offline page
            // (Only for navigation requests like refreshing the page)
            if (event.request.mode === 'navigate') {
                return caches.match(OFFLINE_PAGE);
            }
        });

      return cachedResponse || fetchPromise;
    })
  );
});

// --- HELPER: API Logic ---
async function handleApiRequest(request) {
  // A. NORMALIZATION: Strip jQuery's cache-buster ("_")
  const urlObj = new URL(request.url);
  urlObj.searchParams.delete('_'); 
  const cleanRequest = new Request(urlObj.toString(), {
    mode: 'cors',
    credentials: 'omit',
    headers: request.headers
  });

  try {
    // B. TRY NETWORK FIRST
    const networkResponse = await fetch(request);
    
    // If successful, save using the CLEAN key
    if (networkResponse.ok) {
      const cache = await caches.open(API_CACHE_NAME);
      await cache.put(cleanRequest, networkResponse.clone());
    }
    
    return networkResponse;

  } catch (error) {
    // C. FALLBACK TO CACHE
    const cache = await caches.open(API_CACHE_NAME);
    const cachedResponse = await cache.match(cleanRequest);

    if (cachedResponse) {
      // D. CHECK EXPIRY (3600 seconds / 1 Hour)
      const dateHeader = cachedResponse.headers.get('date');
      if (dateHeader) {
        const cachedTime = new Date(dateHeader).getTime();
        const ageInSeconds = (Date.now() - cachedTime) / 1000;

        if (ageInSeconds < 3600) {
           // Optional: Add a custom header to let your frontend know this is stale data
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
    
    // E. FINAL FALLBACK: JSON Error (prevents UI crash)
    return new Response(JSON.stringify({ error: "Offline and cache expired" }), {
      headers: { "Content-Type": "application/json" }
    });
  }
}
