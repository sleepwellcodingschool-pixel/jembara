
const CACHE_NAME = 'live-chat-v1.0.0';
const urlsToCache = [
    '/chat/',
    '/chat/index.html',
    '/chat/chat-app.js',
    '/chat/manifest.json',
    '../assets/css/style.css',
    '../assets/images/logo.svg',
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

// Install event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch event
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // Return cached version or fetch from network
                if (response) {
                    return response;
                }
                
                return fetch(event.request).then((response) => {
                    // Don't cache if not a valid response
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }
                    
                    // Clone the response
                    const responseToCache = response.clone();
                    
                    caches.open(CACHE_NAME)
                        .then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    
                    return response;
                });
            })
            .catch(() => {
                // Return offline page for navigation requests
                if (event.request.destination === 'document') {
                    return caches.match('/chat/');
                }
            })
    );
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Push event for notifications
self.addEventListener('push', (event) => {
    console.log('[Service Worker] Push Received.');
    
    const options = {
        body: 'Anda memiliki pesan chat baru!',
        icon: '../assets/images/logo.svg',
        badge: '../assets/images/logo.svg',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: '1'
        },
        actions: [
            {
                action: 'explore',
                title: 'Buka Chat',
                icon: '../assets/images/logo.svg'
            },
            {
                action: 'close',
                title: 'Tutup',
                icon: '../assets/images/logo.svg'
            }
        ]
    };
    
    if (event.data) {
        const data = event.data.json();
        options.body = data.message || options.body;
        options.title = data.title || 'Live Chat';
    }
    
    event.waitUntil(
        self.registration.showNotification('Live Chat - JEMBARA', options)
    );
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('[Service Worker] Notification click Received.');
    
    event.notification.close();
    
    if (event.action === 'explore') {
        // Open chat app
        event.waitUntil(
            clients.openWindow('/chat/')
        );
    } else if (event.action === 'close') {
        // Just close notification
        event.notification.close();
    } else {
        // Default action
        event.waitUntil(
            clients.openWindow('/chat/')
        );
    }
});

// Background sync
self.addEventListener('sync', (event) => {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

function doBackgroundSync() {
    // Implement background sync logic here
    console.log('[Service Worker] Background sync triggered');
}
