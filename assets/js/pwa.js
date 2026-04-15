const PWA = {
    isOnline: navigator.onLine,
    syncStatus: document.getElementById('sync-status'),

    init() {
        this.updateOnlineStatus();
        this.registerServiceWorker();
        this.setupEventListeners();
        this.checkPendingSync();
    },

    updateOnlineStatus() {
        this.isOnline = navigator.onLine;
        this.showNetworkStatus();
    },

    showNetworkStatus() {
        const statusEl = document.getElementById('network-status');
        if (statusEl) {
            statusEl.className = this.isOnline ? 'online' : 'offline';
            statusEl.textContent = this.isOnline ? '🟢 Online' : '⚠️ Offline';
        }

        const syncEl = document.getElementById('sync-status');
        if (syncEl) {
            syncEl.style.display = this.isOnline ? 'none' : 'block';
        }
    },

    registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/Aplikasi Absensi SISWA/service-worker.js')
                .then(registration => {
                    console.log('PWA Service Worker registered:', registration.scope);
                })
                .catch(error => {
                    console.error('PWA Service Worker registration failed:', error);
                });
        }

        if ('sync' in window.BackgroundSyncManager.prototype) {
            navigator.serviceWorker.ready.then(registration => {
                registration.sync.register('sync-data').catch(err => {
                    console.log('Background Sync not supported:', err);
                });
            });
        }
    },

    setupEventListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.showNetworkStatus();
            this.syncPendingData();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showNetworkStatus();
        });

        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data.type === 'SYNC_COMPLETE') {
                this.showSyncComplete();
            }
        });

        document.addEventListener('submit', event => {
            // Only intercept if truly offline at submit time
            if (!navigator.onLine) {
                event.preventDefault();
                this.handleOfflineSubmit(event.target);
            }
        });
    },

    async handleOfflineSubmit(form) {
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        try {
            const db = await this.openDB();
            const tx = db.transaction('sync_queue', 'readwrite');
            const store = tx.objectStore('sync_queue');
            
            await store.add({
                url: form.action || window.location.href,
                method: 'POST',
                body: data,
                timestamp: Date.now(),
                status: 'pending'
            });

            alert('Anda sedang offline. Data akan disinkronkan saat koneksi kembali.');
            form.reset();
        } catch (err) {
            console.error('Failed to save offline:', err);
            alert('Gagal menyimpan data offline.');
        }
    },

    openDB() {
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
    },

    async checkPendingSync() {
        try {
            const db = await this.openDB();
            const tx = db.transaction('sync_queue', 'readonly');
            const store = tx.objectStore('sync_queue');
            const count = await new Promise((resolve) => {
                store.count().onsuccess = () => resolve(store.result);
            });

            if (count > 0 && this.isOnline) {
                this.syncPendingData();
            }
        } catch (err) {
            console.error('Error checking pending sync:', err);
        }
    },

    async syncPendingData() {
        if (!this.isOnline) return;

        try {
            const db = await this.openDB();
            const tx = db.transaction('sync_queue', 'readwrite');
            const store = tx.objectStore('sync_queue');
            const items = await new Promise((resolve) => {
                const request = store.getAll();
                request.onsuccess = () => resolve(request.result);
            });

            for (const item of items) {
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
                        await new Promise((resolve) => {
                            const req = deleteStore.delete(item.id);
                            req.onsuccess = () => resolve();
                        });
                    }
                } catch (err) {
                    console.error('Sync failed for item:', item.id);
                }
            }

            this.showSyncComplete();
        } catch (err) {
            console.error('Sync error:', err);
        }
    },

    showSyncComplete() {
        const toast = document.createElement('div');
        toast.className = 'sync-toast';
        toast.innerHTML = '✔ Data berhasil disinkronkan';
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2ecc71;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => PWA.init());
} else {
    PWA.init();
}