import { FetchOptions } from "ofetch";
import hash from 'object-hash';

export interface DifferentFetchOptions extends FetchOptions {
    //Let's you not automatically execute the API call. Returns an empty ref instead
    immediate?: boolean;
    lazy?: boolean;
}

export default function<T>(url: string|(() => string), options: DifferentFetchOptions = {}, key?: string) {
    key ??='';
    key += hash(JSON.stringify({
        url: typeof url == 'function' ? url() : url,
        params: options.params
    }));

    return useAsyncData(key ?? '', () =>  useGet<T>(typeof url == 'function' ? url() : url, options), { 
        lazy: options.lazy,
        immediate: options.immediate
    });
    //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
}