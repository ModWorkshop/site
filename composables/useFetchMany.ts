import { Paginator } from './../types/paginator';
import { FetchOptions } from "ohmyfetch";

export default function<T>(url: string|Function, options?: FetchOptions) {
    return useAPIFetch<Paginator<T>>(url, options);
}