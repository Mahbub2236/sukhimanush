/**
 * সুখী মানুষ (Sukhi Manush) Health Suite - Progressive Web App Service Worker
 * Version: 2.0.0
 *
 * Strategies:
 * 1. Network-only (Pass-through) for API endpoints (/api/*) & authentication
 * 2. Stale-While-Revalidate for core static assets (CSS, JS, Images, Web Fonts)
 * 3. Network-First with Cache fallback for navigation routes
 */

const CACHE_NAME = 'sukhi-manush-pwa-v2';

// Pre-cache critical core shell assets
const PRECACHE_ASSETS = [
  '/',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/icons/apple-touch-icon.png',
  '/plugins/bootstrap/css/bootstrap.min.css',
  '/plugins/icofont/icofont.min.css'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch((err) => {
        console.warn('Pre-caching partial assets failed, continuing...', err);
      });
    }).then(() => self.skipWaiting())
  );
});

// Activate Event - Clean up stale old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // 1. CRITICAL: NEVER cache API calls or non-GET requests (100% Real-time guarantee)
  if (request.method !== 'GET' || url.pathname.startsWith('/api/') || url.pathname.startsWith('/sanctum/')) {
    return; // Direct network bypass
  }

  // 2. Navigation requests (HTML page loads): Network-First, fallback to cached '/'
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // If valid response, clone into cache
          if (response && response.status === 200) {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
          }
          return response;
        })
        .catch(async () => {
          // Offline fallback
          const cached = await caches.match(request);
          if (cached) return cached;
          const rootCached = await caches.match('/');
          if (rootCached) return rootCached;
          return new Response('<h1>অফলাইন মোড</h1><p>আপনার ইন্টারনেট সংযোগ চেক করুন।</p>', {
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
          });
        })
    );
    return;
  }

  // 3. Static Assets (CSS, JS, Fonts, Images): Stale-While-Revalidate
  if (
    url.pathname.match(/\.(css|js|woff2?|ttf|eot|png|jpg|jpeg|svg|webp|gif|ico)$/i) ||
    url.hostname.includes('fonts.googleapis.com') ||
    url.hostname.includes('fonts.gstatic.com') ||
    url.hostname.includes('cdnjs.cloudflare.com')
  ) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        const fetchPromise = fetch(request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
          }
          return networkResponse;
        }).catch(() => null);

        // Return cached version immediately if available, else await network
        return cachedResponse || fetchPromise;
      })
    );
    return;
  }

  // Default: Network with cache fallback
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  );
});

// 4. Push Notifications (100% Free Browser Push API)
self.addEventListener('push', (event) => {
  let data = {
    title: 'Novena Health',
    body: 'আপনার অ্যাপয়েন্টমেন্ট বা স্বাস্থ্যসেবা সংক্রান্ত আপডেট এসেছে।',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    url: '/patient/dashboard'
  };

  if (event.data) {
    try {
      data = Object.assign(data, event.data.json());
    } catch (e) {
      data.body = event.data.text();
    }
  }

  const options = {
    body: data.body,
    icon: data.icon,
    badge: data.badge,
    vibrate: [200, 100, 200],
    data: {
      url: data.url || '/'
    },
    actions: [
      { action: 'open', title: 'দেখুন' },
      { action: 'close', title: 'বাতিল' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Notification Click Handler (Directs patient to video chamber or dashboard)
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
      for (let client of windowClients) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});

