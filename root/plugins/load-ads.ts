import { useStore } from '../store/index';


export default defineNuxtPlugin((nuxtApp) => {
    const launched = ref(false);
    const { ads } = useStore();
    nuxtApp.hook('page:finish', () => {
        if (!launched.value) {
            launched.value = true;
            return;
        }

        console.log("Ad on navigate. Refreshing", ads?.length || 0, " ads");
        for (const ad of ads) {
            ad.onNavigate();
        }
        // console.log('Page finished loading. Attempting to launch or reinstate ads.');
        // if (process.client && (window.egAps && typeof(window.egAps.reinstate) === "function")) {
        //     if (launched.value) { // Ads were not launched yet
        //         console.log(`Reinstate ads [URL: ${window.location.href}]`);
        //         window.egAps.reinstate();
        //     } else { // Launch and set the ref so next time we only reinstate them.
        //         console.log(`Launch ads`);
        //         window.egAps.launchAds();
        //         launched.value = true;
        //     }
        // }
    });
});
