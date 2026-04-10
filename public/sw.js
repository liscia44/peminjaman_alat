// ✅ CACHE NAMES
const CACHE_NAME = 'peminjaman-alat-v1';
const STATIC_ASSETS = [
    '/peminjaman-guest',  // ✅ Default route
    '/',
    '/app-manifest.json',
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap',
    'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js',
];

// ✅ INSTALL EVENT
self.addEventListener('install', (event) => {
    console.log('✅ Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('✅ Caching static assets');
            return cache.addAll(STATIC_ASSETS).catch(err => {
                console.warn('⚠️ Some assets failed to cache:', err);
                // Continue meskipun ada error
                return Promise.resolve();
            });
        })
    );
    self.skipWaiting();
});

// ✅ ACTIVATE EVENT
self.addEventListener('activate', (event) => {
    console.log('✅ Service Worker activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('🗑️ Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// ✅ FETCH EVENT - NETWORK FIRST dengan fallback to CACHE
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // ✅ Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // ✅ Skip chrome extensions
    if (url.protocol === 'chrome-extension:') {
        return;
    }

    // ✅ NETWORK FIRST untuk API calls, form data, dan dynamic pages
    if (url.pathname.includes('/api/') || 
        url.pathname.includes('/peminjaman') ||
        request.headers.get('accept')?.includes('text/html')) {
        
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Hanya cache successful responses
                    if (!response || response.status !== 200 || response.type === 'error') {
                        return response;
                    }

                    // Clone dan cache response
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseToCache);
                    });
                    return response;
                })
                .catch(() => {
                    console.log('📦 Network failed, trying cache:', request.url);
                    
                    // Return cached version
                    return caches.match(request).then((cachedResponse) => {
                        if (cachedResponse) {
                            console.log('✅ Served from cache:', request.url);
                            return cachedResponse;
                        }

                        // Fallback ke halaman offline jika document request
                        if (request.mode === 'navigate') {
                            console.log('📄 Serving offline fallback for:', request.url);
                            return caches.match('/peminjaman-guest');
                        }

                        return new Response('Offline - Resource not available', { status: 503 });
                    });
                })
        );
        return;
    }

    // ✅ CACHE FIRST untuk static assets (CSS, JS, fonts, images)
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                console.log('📦 Cache hit:', request.url);
                return cachedResponse;
            }

            return fetch(request)
                .then((response) => {
                    // Hanya cache successful responses
                    if (!response || response.status !== 200 || response.type === 'error') {
                        return response;
                    }

                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseToCache);
                    });
                    return response;
                })
                .catch(() => {
                    console.warn('❌ Fetch failed (offline):', request.url);
                    return new Response('Offline', { status: 503 });
                });
        })
    );
});

console.log('✅ Service Worker loaded');