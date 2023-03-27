import qs from 'qs';

export default async function<T = unknown>(url: string, options?) {
    const token = useCookie('XSRF-TOKEN');
    const headers = useRequestHeaders(['cookie']);
    const { public: config } = useRuntimeConfig();

    const headersToSend: any = {
        accept: 'application/json', //Avoids redirects and makes sure we get JSON response.
    };

    if (!config.is_production) {
        headersToSend.referer = `http://${config.siteUrl}`;
    }

    let protocol;
    if (process.client) {
        protocol = getProtocol(config);
    } else {
        protocol = config.is_production ? '' : 'http://';
    }

    if (headers.cookie) {
        headersToSend['cookie'] = headers.cookie;
    }
    
    if (token.value) {
        headersToSend['X-XSRF-TOKEN'] = token.value;
    }

    //No point running this for non GET
    if (options && options.params && (!options.method || options.method == 'GET')) {
        //This retarded code is brought you by stupid web standards https://blog.shalvah.me/posts/fun-stuff-representing-arrays-and-objects-in-query-strings
        //tl;dr - PHP and JS cannot agree on the format for arrays in queries, we shall use PHP's one.
        
        url += qs.stringify(options.params, { 
            arrayFormat: 'brackets',
            encode: false,
            addQueryPrefix: true
        });
    }

    const res = await $fetch<T>(url, {
        baseURL: protocol + config.apiUrl,
        headers: headersToSend,
        credentials: "include", //Required as it doesn't send cookies and stuff otherwise
        ...options,
        params: {}
    });

    return res;
}