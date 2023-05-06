import { FetchOptions } from "ofetch";

export default function<T>(url: string, body?: object|null, options?: FetchOptions) {
    return useGet<T>(url, {
        method:"POST",
        body,
        ...options
    });
}