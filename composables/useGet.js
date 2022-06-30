import queryString from 'query-string';

export default async function(url, options) {
    const token = useCookie('XSRF-TOKEN');
    const headers = useRequestHeaders(['cookie']);

    //No point running this for non GET
    if (options && options.params && (!options.method || options.method == 'GET')) {
        //This retarded code is brought you by stupid web standards https://blog.shalvah.me/posts/fun-stuff-representing-arrays-and-objects-in-query-strings
        //tl;dr - PHP and JS cannot agree on the format for arrays in queries, we shall use PHP's one.
        url += '?'+queryString.stringify(options.params, { arrayFormat: 'bracket' })
    }

    return await $fetch(url, {
        baseURL: 'http://localhost:8000',
        headers: {
            accept: 'application/json', //Avoids redirects and makes sure we get JSON response.
            referer: 'localhost:3000', //Uneeded in production
            cookie: headers.cookie,
            'X-XSRF-TOKEN': token.value
        },
        credentials: "include", //Required as it doesn't send cookies and stuff otherwise
        ...options,
        params: {}
    });
}