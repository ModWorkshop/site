
/**
 * For dynamic items that require no caching (as we have many of those)
 * 
 * @param {string} key 
 * @param {function} func 
 * @param {object} options 
 */
export default function(key, func, options={}) {
    options.initialCache = false;
    return useAsyncData(key, func, options);
}