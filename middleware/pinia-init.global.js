import { useStore } from '../store';

export default defineNuxtRouteMiddleware(async (to, from) => {
    if (process.server) {
        const { $pinia } = useNuxtApp();
        const store = useStore($pinia);
        await store.nuxtServerInit();
    }
});