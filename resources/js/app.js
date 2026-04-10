import './bootstrap';

// ✅ PWA - Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => {
                console.log('✅ Service Worker registered:', reg);
                
                // Check for updates setiap 1 menit
                setInterval(() => {
                    reg.update();
                }, 60000);
            })
            .catch(err => console.log('❌ SW registration failed:', err));
    });
}

// ✅ Handle online/offline
window.addEventListener('online', () => {
    console.log('📡 Online - syncing data...');
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        navigator.serviceWorker.ready.then(reg => {
            reg.sync.register('sync-peminjaman');
        });
    }
});

window.addEventListener('offline', () => {
    console.log('📴 Offline - data akan disimpan lokal');
});