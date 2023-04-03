import { FetchOptions } from "ofetch";

export interface DifferentFetchOptions extends FetchOptions {
    //Let's you not automatically execute the API call. Returns an empty ref instead
    immediate?: boolean;
    lazy?: boolean;
}

export default async function<T>(url: string|(() => string), options: DifferentFetchOptions = {}, key?: string) {
    return await useAsyncData(key ?? 'how', () =>  useGet<T>(typeof url == 'function' ? url() : url, options), { 
        lazy: options.lazy,
        immediate: options.immediate
    });
    //TODO: decide the thing with caching. Caching can be useful in certain situations, however we can't use it for a few things.
    //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
}