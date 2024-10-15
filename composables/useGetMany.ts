import { Paginator } from '~~/types/paginator';
import type { FetchOptions } from 'ofetch';

export default async function<T = unknown>(url: string, options?: FetchOptions) {
    return useGet<Paginator<T>>(url, options);
}