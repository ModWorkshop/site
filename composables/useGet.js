export default async function(url, options) {
    const token = useCookie('XSRF-TOKEN');
    const headers = useRequestHeaders(['cookie']);

    return await $fetch(url, {
        baseURL: 'http://localhost:8000',
        headers: {
            referer: 'localhost:3000',
            cookie: headers.cookie,
            'X-XSRF-TOKEN': token.value
        },
        credentials: "include",
        ...options
    });
}