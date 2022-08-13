import { useStore } from '../store';

export default defineNuxtRouteMiddleware(async () => {
    const { $pinia } = useNuxtApp();
    const store = useStore($pinia);

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
});