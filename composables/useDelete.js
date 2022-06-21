export default function(url, body, options) {
    options = {
        method:"DELETE",
        body,
        ...options
    }
    return useGet(url, options);
}