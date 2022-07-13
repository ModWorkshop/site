import { $fetch } from 'ohmyfetch';
import { reloadToken } from '../utils/helpers';
export default defineNuxtPlugin(nuxtApp => {
    if (process.client) {
        // Set XSRF token for SSR
        async function check() {
            if (!useCookie('XSRF-TOKEN').value) {
                console.log("Fetching CSRF token. " );
                reloadToken();
            }
        }
        setInterval(check, 30000); // Check every 5 minutes
        check();
    }
});