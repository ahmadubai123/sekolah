const CACHE_NAME = 'madrasahku-v2';
const STATIC_CACHE = 'madrasahku-static-v2';
const DYNAMIC_CACHE = 'madrasahku-dynamic-v2';

const STATIC_ASSETS = [
    '/Aplikasi Absensi SISWA/',
    '/Aplikasi Absensi SISWA/index.php',
    '/Aplikasi Absensi SISWA/manifest.json',
    '/Aplikasi Absensi SISWA/assets/css/style.css',
    '/Aplikasi Absensi SISWA/assets/img/logo.png'
];

const API_ENDPOINTS = [
    '/Aplikasi Absensi SISWA/auth/login_process.php',
    '/Aplikasi Absensi SISWA/siswa/simpan.php',
    '/Aplikasi Absensi SISWA/guru/absensi_guru.php'
];

self.addEventListener('install', event => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('[Service Worker] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
                    .map(key => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    const pathname = url.pathname;

    // Skip caching for PHP action files (CRUD operations)
    // Don't cache: hapus_*.php, edit_*.php, login_process.php, etc.
    const skipCache = pathname.includes('hapus_') || 
                    pathname.includes('edit_') || 
                    pathname.includes('login_process') ||
                    pathname.includes('simpan') ||
                    pathname.includes('reset_password') ||
                    (pathname.includes('.php') && url.searchParams.toString() !== '');

    // Always fetch directly for action files
    if (skipCache) {
        event.respondWith(fetch(request));
        return;
    }

    // For navigation requests (HTML pages), fetch directly first
    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }

    // Handle GET requests with caching
    if (request.method === 'GET') {
        event.respondWith(cacheFirst(request));
    } else {
        event.respondWith(fetch(request));
    }
});

async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        return caches.match('/Aplikasi Absensi SISWA/index.php');
    }
}

async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.log('[Service Worker] Network failed, saving for later sync');

        const clonedRequest = request.clone();
        const formData = await clonedRequest.formData();
        const body = {};
        formData.forEach((value, key) => body[key] = value);

        const syncItem = {
            url: request.url,
            method: 'POST',
            body: body,
            timestamp: Date.now(),
            status: 'pending'
        };

        const db = await openDB();
        const tx = db.transaction('sync_queue', 'readwrite');
        const store = tx.objectStore('sync_queue');
        await store.add(syncItem);

        if (self.registration.sync) {
            await self.registration.sync.register('sync-data');
        }

        return new Response(JSON.stringify({
            success: true,
            offline: true,
            message: 'Data disimpan offline. Akan disinkronkan saat online.'
        }), {
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('MadrasahKu', 1);
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('sync_queue')) {
                db.createObjectStore('sync_queue', { keyPath: 'id', autoIncrement: true });
            }
            if (!db.objectStoreNames.contains('offline_cache')) {
                db.createObjectStore('offline_cache');
            }
        };
    });
}

self.addEventListener('sync', event => {
    if (event.tag === 'sync-data') {
        event.waitUntil(syncPendingData());
    }
});

async function syncPendingData() {
    try {
        const db = await openDB();
        const tx = db.transaction('sync_queue', 'readwrite');
        const store = tx.objectStore('sync_queue');
        const getAll = store.getAll();
        
        getAll.onsuccess = async () => {
            const pendingItems = getAll.result;
            for (const item of pendingItems) {
                try {
                    const formData = new FormData();
                    for (const [key, value] of Object.entries(item.body)) {
                        formData.append(key, value);
                    }
                    
                    const response = await fetch(item.url, {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        const deleteTx = db.transaction('sync_queue', 'readwrite');
                        const deleteStore = deleteTx.objectStore('sync_queue');
                        await deleteStore.delete(item.id);
                    }
                } catch (err) {
                    console.error('[Sync] Failed to sync item:', item.id, err);
                }
            }
            
            const clients = await self.clients.matchAll();
            clients.forEach(client => {
                client.postMessage({ type: 'SYNC_COMPLETE' });
            });
        };
    } catch (err) {
        console.error('[Sync] Error:', err);
    }
}

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});