import { useStore } from '~~/store';
export default function() {
    const store = useStore();

    if (!store.user) {
        showError({ statusCode: 401, statusMessage: "You must be logged in to access this page", fatal: true});
    } else if (store.isBanned) {
        showError({ statusCode: 401, statusMessage: "Banned users cannot access this page", fatal: true});
    }
}