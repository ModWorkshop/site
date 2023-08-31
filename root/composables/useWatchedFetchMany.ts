import { SearchParams } from 'ohmyfetch';
import { DifferentFetchOptions } from './useFetchData';

export interface WatchedFetchManyOptions extends DifferentFetchOptions {
    onChange?: ((val: any, oldVal: any) => boolean)|null;
}

/**
    This is useFetchMany but tailored for handling parameter changes for lists and so on.
 */
export default async function<T=any>(url: string|(() => string), params: SearchParams, options?: WatchedFetchManyOptions, key?: string) {
    let loading: Ref<boolean>;
    const ret = await useFetchMany<T>(url, {
        params: reactive(params),
        async onRequest() {
            if (loading) {
                loading.value = true;
            }
        },
        async onResponse() {
            if (loading) {
                loading.value = false;
            }
        },
        ...options
    }, key);

    loading = useHandleParam(ret.refresh, params, options?.onChange);

    return { ...ret, loading: loading };
}