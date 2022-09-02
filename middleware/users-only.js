import { useStore } from '../store';
// Moves users from pages that are for guests only (login & register generally)

export default defineNuxtRouteMiddleware((to) => {
    const { $pinia } = useNuxtApp();
    const { user } = useStore($pinia);

    if (!user) {
        showError({ statusCode: 401, statusMessage: "You must be logged in to access this page", fatal: true});
    }
});