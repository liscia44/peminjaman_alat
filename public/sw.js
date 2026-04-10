const CACHE_NAME = 'peminjaman-v1';
const urlsToCache = [
  '/',
  '/manifest.json',
  '/css/app.css',
];

// ✅ INSTALL - cache files
self.addEventListener('install', event => {
  console.log('🔧 Service Worker installing...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('✅ Cache opened');
      return cache.addAll(urlsToCache).catch(err => {
        console.log('⚠️ Some files not cached:', err);
      });
    })
  );
  self.skipWaiting();
});

// ✅ ACTIVATE - cleanup old caches
self.addEventListener('activate', event => {
  console.log('🔄 Service Worker activating...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('🧹 Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// ✅ FETCH - network first, fallback to cache
self.addEventListener('fetch', event => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') return;

  // Skip chrome extensions
  if (event.request.url.startsWith('chrome-extension://')) return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Cache successful responses
        if (!response || response.status !== 200 || response.type === 'error') {
          return response;
        }

        const responseToCache = response.clone();
        caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, responseToCache);
        });

        return response;
      })
      .catch(() => {
        // Return cached version if offline
        return caches.match(event.request).then(cachedResponse => {
          if (cachedResponse) {
            console.log('📦 Serving from cache:', event.request.url);
            return cachedResponse;
          }

          // Return offline page jika ada
          if (event.request.mode === 'navigate') {
            return caches.match('/');
          }
        });
      })
  );
});

// ✅ BACKGROUND SYNC - untuk kirim data saat online
self.addEventListener('sync', event => {
  if (event.tag === 'sync-peminjaman') {
    console.log('🔄 Background sync triggered');
    event.waitUntil(syncPeminjaman());
  }
});

async function syncPeminjaman() {
  try {
    const db = await openDB('peminjaman-db');
    const unsynced = await db.getAll('peminjaman');

    for (let item of unsynced) {
      if (item.synced) continue;

      try {
        const response = await fetch('/api/peminjaman/guest/store', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': item.csrf_token
          },
          body: JSON.stringify(item)
        });

        if (response.ok) {
          item.synced = true;
          await db.put('peminjaman', item);
          console.log('✅ Synced:', item.id);
        }
      } catch (e) {
        console.error('❌ Sync failed for item:', item.id, e);
      }
    }
  } catch (e) {
    console.error('❌ Sync error:', e);
  }
}