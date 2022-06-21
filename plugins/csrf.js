import { $fetch } from 'ohmyfetch';
export default defineNuxtPlugin(nuxtApp => {
    const fetchInstance = $fetch.create({ baseURL: 'http://localhost:8000', credentials: "include", headers: {Referer: 'localhost:3000'} });

    if (process.client) {
        // Set XSRF token for SSR
        async function check() {
            if (!useCookie('XSRF-TOKEN').value) {
                console.log("Fetching CSRF token. " );
                await ftch('/sanctum/csrf-cookie');
            }
        }
        setInterval(check, 30000); // Check every 5 minutes
        check();
    }

    async function ftch(url, opt) {
        opt ??= {};
        opt.headers ??= {};
        opt.headers['X-XSRF-TOKEN'] = useCookie('XSRF-TOKEN').value;

        return await fetchInstance(url, opt);
    }

    return {
        provide: {
            ftch,
            t(str) {
                return str
            }
        }
    }
});