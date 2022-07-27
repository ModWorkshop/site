import { useStore } from '../store';

export default defineNuxtRouteMiddleware((to) => {
    const { user, hasPermission } = useStore();
    
    if (!user || !hasPermission('admin')) {
        throwError("You don't have permission to view this page");
    }
});