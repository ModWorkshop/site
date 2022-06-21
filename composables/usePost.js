export default function(url, body, options) {
    options = {
        method: "POST",
        body,
        ...options
    }
    return useGet(url, options);
}