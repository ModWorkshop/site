import { useStore } from '../store';
// Moves users from pages that are for guests only (login & register generally)

export default defineNuxtRouteMiddleware(() => {
    const { $pinia } = useNuxtApp();
    const { user } = useStore($pinia);
    if (user) {
        return '/';
    }
});