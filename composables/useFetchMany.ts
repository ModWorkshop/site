import { MoreFetchOptions } from './useFetchData';
import { Paginator } from './../types/paginator';

/**
    Fetches stuff from the API that are many, essentially wrapping it in a paginator.
 */
export default function<T>(url: string|Function, options?: MoreFetchOptions) {
    return useFetchData<Paginator<T>>(url, options);
}