import { FetchError } from 'ofetch';
import { useStore } from '../store';


export default defineNuxtRouteMiddleware(async (to, from) => {
    const { $pinia, $i18n } = useNuxtApp();
    const store = useStore($pinia);
    
    const doAsync = useState('reloadSiteDataAsync', () => true);

    let firstTimeOnClient = true;

    if (to.path !== from.path || to.fullPath === from.fullPath) {
        //Don't keep the game since we could go to the home page where there's no specificed game.
        store.currentGame = null;
        //https://github.com/nuxt/framework/issues/6475
        try {
            if (doAsync.value) {
                await store.reloadSiteData(true);
                doAsync.value = false;
            } else {
                store.reloadSiteData(!firstTimeOnClient);
                firstTimeOnClient = false;
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