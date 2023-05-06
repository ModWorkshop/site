import { reloadToken } from '../utils/helpers';
async function check() {
    console.log('Checking for CSRF token...');
    if (!useCookie('XSRF-TOKEN').value) {
        console.log("Fetching CSRF token. " );
        reloadToken();
    }
}

export default defineNuxtPlugin(() => {
    if (process.client) {
        // Set XSRF token for SSR
        setInterval(check, 300000); // Check every 5 minutes
        check();
    }
});