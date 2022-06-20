import { useStore } from '../store';
// Moves users from pages that are for guests only (login & register generally)

export default defineNuxtRouteMiddleware((to) => {
    const { user } = useStore();
    if (user) {
        return '/';
    }
});