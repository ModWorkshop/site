import { FetchOptions } from "ohmyfetch";

export interface DifferentFetchOptions extends FetchOptions {
    //Let's you not automatically execute the API call. Returns an empty ref instead
    immediate?: boolean;
    initialCache?: boolean;
    lazy?: boolean;
}

export default async function<T>(url: string|(() => string), options: DifferentFetchOptions = {}) {
    const key = typeof url == 'function' ? url.toString() : url;
    return await useAsyncData(key, () =>  useGet<T>(typeof url == 'function' ? url() : url, options), { 
        initialCache: options.initialCache ?? false,
        lazy: options.lazy,
        immediate: options.immediate
    });
    //TODO: decide the thing with caching. Caching can be useful in certain situations, however we can't use it for a few things.
    //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
}