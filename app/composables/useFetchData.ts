import type { UseFetchOptions } from "#app";

export default function<T>(url: string|(() => string), options?: UseFetchOptions<T>) {
    const { $mwsAPI } = useNuxtApp();

    return useFetch(url, {
        ...options,
        $fetch: $mwsAPI
    });
}