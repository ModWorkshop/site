import { useStore } from '../store';

export default defineNuxtRouteMiddleware((to) => {
    const { user, hasPermission } = useStore();
    console.log('hello');
    if (!user || !hasPermission('admin')) {
        throwError("You don't have permission to view this page");
    } else {
        console.log("you're fine");
    }
});