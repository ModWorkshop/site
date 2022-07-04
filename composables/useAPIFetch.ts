import { FetchOptions } from "ohmyfetch";

export default function(url: string|Function, options: FetchOptions) {
    const key = typeof url == 'function' ? url() : url;
    return useAsyncData(key, () => {
        const current = typeof url == 'function' ? url() : url;
        return useGet(current, options);
    }, { initialCache: false });
    //TODO: decide the thing with caching. Caching can be useful in certain situations, however we can't use it for a few things.
    //For example we can't use it for mods, they constantly change and unless we have time option it'll cause a lot of issues.
}