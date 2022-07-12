import { Paginator } from './../types/paginator';
import { FetchOptions } from 'ohmyfetch';

export default async function<T = unknown>(url: string, options?: FetchOptions) {
    return useGet<Paginator<T>>(url, options);
}