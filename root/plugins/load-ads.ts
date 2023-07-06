

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.hook('page:finish', () => {
        if (process.client && (window.egAps && typeof(window.egAps.reinstate) === "function")) {
            console.log(`Reinstate ads [URL: ${window.location.href}]`);
            window.egAps.reinstate();
        }
    });

    nuxtApp.hook('app:mounted', () => {
        if (process.client && (window.egAps && typeof(window.egAps.reinstate) === "function")) {
            console.log(`Launch ads`);
            window.egAps.launchAds();
        }
    });
})
