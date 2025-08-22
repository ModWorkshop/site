import type { UseFetchOptions } from '#app';
import { Paginator } from '~/types/paginator';

/**
    Fetches stuff from the API that are many, essentially wrapping it in a paginator.
 */
export default function<T = any>(url: string | (() => string), options?: UseFetchOptions<Paginator<T>>) {
	return useFetchData<Paginator<T>>(url, options);
}
