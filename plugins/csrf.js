import { $fetch } from 'ohmyfetch';
export default defineNuxtPlugin(nuxtApp => {
    const token = useCookie('XSRF-TOKEN');
    const opt = { baseURL: 'http://localhost:8000', credentials: "include", headers: {Referer: 'localhost:3000', 'X-XSRF-TOKEN': token.value} };

    if (process.client) {
        // Set XSRF token for SSR
        async function check() {
            if (!useCookie('XSRF-TOKEN').value) {
                console.log("Fetching CSRF token. " );
                await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
                    credentials: "include"
                });
    
                opt['X-XSRF-TOKEN'] = useCookie('XSRF-TOKEN').value;
            }
        }
        setInterval(check, 30000); // Check every 5 minutes
        check();
    }

    //Just a new instance of fetch that links to our API
    const ftch = $fetch.create(opt);

    return {
        provide: {
            ftch,
            t(str) {
                return str
            }
        }
    }
});