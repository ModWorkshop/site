import { FetchOptions } from "ohmyfetch";

export default function<T>(url: string, body?: object, options?: FetchOptions) {
    return useGet<T>(url, {
        method:"POST",
        body,
        ...options
    });
}