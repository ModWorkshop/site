export default async function<T = unknown>(url: string, options?) {
    const token = useCookie('XSRF-TOKEN', { readonly: true });
    const headers = useRequestHeaders();
    const { public: config } = useRuntimeConfig();
    const allConfig = useRuntimeConfig();

    const headersToSend: Record<string, string> = {
        accept: 'application/json', //Avoids redirects and makes sure we get JSON response.
    };

    headersToSend.referer = config.siteUrl;

    if (headers.cookie) {
        headersToSend['cookie'] = headers.cookie;
    }
    
    if (token.value) {
        headersToSend['X-XSRF-TOKEN'] = token.value;
    }

    // This *should* be safe! If we use Caddy to proxy, Caddy should ignore all of these (if give by user) and set it by itself
    if (import.meta.server) {
        headersToSend['x-forwarded-proto'] = headers['X-forwarded-proto'];
        headersToSend['x-forwarded-host'] = headers['x-forwarded-host'];
        headersToSend['x-forwarded-for'] = headers['x-forwarded-for'];
        headersToSend['x-forwarded-ip'] = headers['x-forwarded-for'];
    }

    //No point running this for non GET
    if (options && options.params && (!options.method || options.method == 'GET')) {
        //This retarded code is brought you by stupid web standards https://blog.shalvah.me/posts/fun-stuff-representing-arrays-and-objects-in-query-strings
        //tl;dr - PHP and JS cannot agree on the format for arrays in queries, we shall use PHP's one.
        
        url += buildQueryParams(options.params);
    }

    const res = await $fetch<T>(url, {
        baseURL: import.meta.client ? config.apiUrl : allConfig.innerApiUrl,
        ...options,
        headers: headersToSend,
        credentials: "include", //Required as it doesn't send cookies and stuff otherwise
        params: {}
    });

    return res;
}