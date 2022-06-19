import { $fetch } from 'ohmyfetch';
export default defineNuxtPlugin(nuxtApp => {
    // Set XSRF token for SSR
    const headers = useRequestHeaders(['cookie']);
    const token = useCookie('XSRF-TOKEN');
    const ftch = $fetch.create({ baseURL: 'http://localhost:8000', headers: {Referer: 'localhost:3000', 'X-XSRF-TOKEN': token.value} });

    return {
        provide: {
            ftch,
            t(str) {
                return str
            }
        }
    }
});