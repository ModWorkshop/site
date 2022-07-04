import { FetchOptions } from "ohmyfetch";

export default function(url: string, body?: object, options?: FetchOptions) {
    return useGet(url, {
        method:"DELETE",
        body,
        ...options
    });
}