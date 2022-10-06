import { useStore } from '../store';

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { $pinia } = useNuxtApp();
    const store = useStore($pinia);

    if (to.path !== from.path || to.fullPath === from.fullPath) {
        //Don't keep the game since we could go to the home page where there's no specificed game.
        store.currentGame = null;
        //https://github.com/nuxt/framework/issues/6475
        try {
            if (!store.user) {
                await store.attemptLoginUser(false);
            }
            if (store.user) {
                await store.getNotificationCount();
            }
        } catch (error) {
            console.log(error);
            store.userIsLoading = false;
            
            if (!error.response) {
                showError({ statusCode: 502, statusMessage: 'API is unreachable. Please wait a bit and try again.', fatal: true });
            } else if (error.response.status === 500) {
                showError({ statusCode: 500, statusMessage: 'API Error! Please report to the admins!', fatal: true });
            }
        }
    }
});