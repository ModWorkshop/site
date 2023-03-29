import { FetchOptions } from "ofetch";

export default function(url: string, body?: object, options?: FetchOptions) {
    return useGet(url, {
        method:"DELETE",
        body,
        ...options
    });
}