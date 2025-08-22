import { FetchError } from 'ofetch';
import { useStore } from '~/store';

export default defineNuxtRouteMiddleware(async (to, from) => {
	const { $pinia, $i18n } = useNuxtApp();
	const store = useStore($pinia);
	const loadedData = useState('loadedData', () => false);
	const startedAutoReloadData = useState('startedAutoLoadedData', () => false);

	if (!loadedData.value || to.path !== from.path || to.fullPath === from.fullPath) {
		// Don't keep the game since we could go to the home page where there's no specificed game.
		store.currentGame = null;

		try {
			if (!loadedData.value) {
				await store.reloadSiteData(true);
			} else if (!startedAutoReloadData.value) {
				store.prepareToReloadSiteData();
				startedAutoReloadData.value = true;
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

		loadedData.value = true;
	}
});
