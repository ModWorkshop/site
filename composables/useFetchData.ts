import type { FetchOptions } from "ofetch";
import hash from 'object-hash';
import { addSeconds, parseISO } from "date-fns";

export interface DifferentFetchOptions extends FetchOptions {
    //Let's you not automatically execute the API call. Returns an empty ref instead
    immediate?: boolean;
    lazy?: boolean;
    cacheData?: boolean;
}

const expirations = {};

export default function<T>(url: string|(() => string), options: DifferentFetchOptions = {}, key?: string) {
    const nuxtApp = useNuxtApp();

    key ??='';
    key += hash(JSON.stringify({
        url: typeof url == 'function' ? url() : url,
        params: options.params
    }));


    return useAsyncData<T>(key ?? '', () =>  useGet<T>(typeof url == 'function' ? url() : url, options), { 
        lazy: options.lazy,
        immediate: options.immediate,
        transform(input) {
            expirations[key] = addSeconds(new Date(), 30).toISOString();
            return input;
        },
        getCachedData(key) {
            const expiration = options.cacheData && Object.hasOwn(expirations, key);
            
            if (!expiration) {
                return nuxtApp.isHydrating ? nuxtApp.payload.data[key] : nuxtApp.static.data[key];
            }

            const data = nuxtApp.payload.data[key] || nuxtApp.static.data[key];

            if (parseISO(expirations[key]) > new Date()) {
                return data;
            }

            return nuxtApp.static.data[key];
        }
    });
    //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
}