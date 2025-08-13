import type { UseFetchOptions } from '#app';
import type { SearchParameters } from 'ofetch';
import { Paginator } from '~/types/paginator';

export interface WatchedFetchManyOptions<T> extends UseFetchOptions<T> {
    onChange?: ((val: any, oldVal: any) => boolean)|null;
}

/**
    This is useFetchMany but tailored for handling parameter changes for lists and so on.
 */
export default async function<T=any>(url: string|(() => string), params: SearchParameters, options?: WatchedFetchManyOptions<Paginator<T>>) {
    // eslint is being stupid about this because it clearly doesn't need to be a const.
    // eslint-disable-next-line prefer-const 
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
    });

    loading = useHandleParam(async () => {
        if (typeof url == 'function' ? url() : url) {
            await ret.refresh();
        }
    }, params, options?.onChange);

    const { data } = ret;

    return { ...ret, data: reactive(data ?? new Paginator()), loading: loading };
}