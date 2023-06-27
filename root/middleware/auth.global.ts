import { FetchError } from 'ofetch';
import { useStore } from '../store';

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { $pinia, $i18n } = useNuxtApp();
    const store = useStore($pinia);

    if (to.path !== from.path || to.fullPath === from.fullPath) {
        if (process.client && (window.egAps && typeof(window.egAps.reinstate) === "function")) {
            console.log('Navigate egAps');
            window.egAps.reinstate()
        }

        //Don't keep the game since we could go to the home page where there's no specificed game.
        store.currentGame = null;
        //https://github.com/nuxt/framework/issues/6475
        try {
            if (!store.user) {
                await store.attemptLoginUser(false);
            }
            if (store.user) {
                await store.reloadSiteData(true);
            }
        } catch (error) {
            if (error instanceof FetchError) {
                if (!error.response) {
                    showError({ statusCode: 502, statusMessage: $i18n.t('error_502'), fatal: true });
                } else if (error.response.status === 500) {
                    showError({ statusCode: 500, statusMessage: $i18n.t('error_500'), fatal: true });
                }
            }
        }
    }
});