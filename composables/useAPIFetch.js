export default function(url, options) {
    return useAsyncData(url, () => useGet(url, options));
}