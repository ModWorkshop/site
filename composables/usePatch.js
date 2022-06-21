export default function(url, body, options) {
    options = {
        method: "PATCH",
        body,
        ...options
    }
    return useGet(url, options);
}