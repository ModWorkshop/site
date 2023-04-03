import { SearchParams } from 'ohmyfetch';
import { DifferentFetchOptions } from './useFetchData';

/**
    This is useFetchMany but tailored for handling parameter changes for lists and so on.
 */
export default async function<T=any>(url: string|(() => string), params: SearchParams, options?: DifferentFetchOptions, key?: string) {
    const ret = await useFetchMany<T>(url, {
        params: reactive(params),
        ...options
    }, key);

    return { ...ret, loading: useHandleParam(ret.refresh, params) };
}