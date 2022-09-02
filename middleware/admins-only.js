import { useStore } from '../store';

export default defineNuxtRouteMiddleware(() => {
    const { $pinia } = useNuxtApp();
    const { user, hasPermission } = useStore($pinia);
    
    if (!user || !hasPermission('admin')) {
        showError({ statusCode: 401, statusMessage: "You don't have permission to view this page", fatal: true});
    }
});