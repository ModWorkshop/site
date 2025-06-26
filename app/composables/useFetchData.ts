import type { FetchOptions } from "ofetch";
import hash from 'object-hash';
import { addSeconds, parseISO } from "date-fns";

export interface DifferentFetchOptions extends FetchOptions {
    //Let's you not automatically execute the API call. Returns an empty ref instead
    immediate?: boolean;
    lazy?: boolean;
    cacheData?: boolean;
}

export default function<T>(url: string|(() => string), options: DifferentFetchOptions = {}, key?: string) {
    const nuxtApp = useNuxtApp();
    const expirations = useState('fetch-expirations', () => { return {} })

    key ??='';
    key += hash(JSON.stringify({
        url: typeof url == 'function' ? url() : url,
        params: options.params
    }));


    return useAsyncData(key, () => useGet<T>(url, options), { 
        lazy: options.lazy,
        immediate: options.immediate,
        transform(input) {
            expirations.value[key] = addSeconds(new Date(), 5).toISOString();
            return input;
        },
        getCachedData(key, nuxtApp, ctx) {
            
            if (ctx.cause === 'refresh:manual') {
                // Skip cache on manual refresh
                return
            }

            const expiration = options.cacheData && Object.hasOwn(expirations.value, key);

            if (!expiration) {
                return nuxtApp.isHydrating ? nuxtApp.payload.data[key] : nuxtApp.static.data[key];
            }

            if (parseISO(expirations.value[key]) > new Date()) {
                return nuxtApp.payload.data[key] ?? nuxtApp.static.data[key];
            }
            
            return undefined;
        }
    });
    //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
}