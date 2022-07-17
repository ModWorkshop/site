import { FetchOptions } from "ohmyfetch";
import { Ref } from "vue";

//TODO: replace immediate with a better solution by someone else.
// https://github.com/nuxt/framework/pull/5500

export interface MoreFetchOptions extends FetchOptions {
    immediate: boolean; //Let's you not automatically execute the API call. Returns an empty ref instead
}

export default function<T>(url: string|Function, options?: MoreFetchOptions) {
    if (options) {
        console.log(options.immediate);
    }
    
    if (!options || options.immediate) {
        const key = typeof url == 'function' ? url() : url;
        return useAsyncData(key, () => {
            const current = typeof url == 'function' ? url() : url;            
            return useGet<T>(current, options);
        }, { initialCache: false });
        //TODO: decide the thing with caching. Caching can be useful in certain situations, however we can't use it for a few things.
        //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
    } else {
        const data = ref<T>(null) as Ref<T>;
        async function refresh() {
            const current = typeof url == 'function' ? url() : url;
            data.value = await useGet<T>(current, options);
        }

        return {
            data,
            refresh,
            error: null
        };
    }
}