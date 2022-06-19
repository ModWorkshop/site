export default function(url, options) {
    return useAsyncData(url, () => useAPI(url, options));
}