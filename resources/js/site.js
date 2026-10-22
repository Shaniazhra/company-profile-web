// Public website script.
// Alpine is only needed on pages that use it (x-data), so it is loaded on demand.
// The admin keeps using resources/js/app.js.
if (document.querySelector('[x-data]')) {
    import('alpinejs').then(({ default: Alpine }) => {
        window.Alpine = Alpine;
        Alpine.start();
    });
}