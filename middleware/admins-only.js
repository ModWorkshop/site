import { useStore } from '../store';

export default defineNuxtRouteMiddleware((to) => {
    const { user, hasPermission } = useStore();
    
    if (!user || !hasPermission('admin')) {
        showError({ statusCode: 401, statusMessage: "You don't have permission to view this page", fatal: true});
    }
});