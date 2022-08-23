import { FetchOptions } from "ohmyfetch";
import { Ref } from "vue";

//TODO: replace immediate with a better solution by someone else.
// https://github.com/nuxt/framework/pull/5500

interface SearchParams {
    [key: string]: unknown;
}

export interface MoreFetchOptions extends FetchOptions {
    //Let's you not automatically execute the API call. Returns an empty ref instead
    immediate?: boolean;
    //Like regular params however you can return a function in case they contain ref values that chan change
    params?: SearchParams;
    //Seconds before watched params should trigger a refetch. Return params as an object to disable refetching.
}

export default async function<T>(url: string|(() => string), options: MoreFetchOptions = {}) {
    if (options.immediate !== false) {
        const key = typeof url == 'function' ? url() : url;
        return await useAsyncData(key, () => {
            const current = typeof url == 'function' ? url() : url;            

            return useGet<T>(current, options);
        }, { initialCache: false });
        //TODO: decide the thing with caching. Caching can be useful in certain situations, however we can't use it for a few things.
        //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
    } else {
        const data = ref<T>(null) as Ref<T>;
        const refresh = async function() {
            const current = typeof url == 'function' ? url() : url;
            data.value = await useGet<T>(current, options);
        };

        return {
            data,
            refresh,
            error: null
        };
    }
}