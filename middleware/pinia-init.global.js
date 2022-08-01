import { useStore } from '../store';

export default defineNuxtRouteMiddleware(async (to, from) => {
    const { $pinia } = useNuxtApp();
    const store = useStore($pinia);

    if (process.server) {
        await store.nuxtServerInit();
    }

    await store.init();
});