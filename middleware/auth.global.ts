import { useStore } from '../store';

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { $pinia } = useNuxtApp();
    const store = useStore($pinia);

    if (to.path !== from.path || to.fullPath === from.fullPath) {
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
        }
    }
});