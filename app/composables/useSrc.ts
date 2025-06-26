const oldPrefixToDebug = {
    'users/images': 'images',
    'games/images': 'previews',
    'mods/images': 'previews',
};

export default function(prefix: string, src?: string|object|Blob, isAsset = false, useThumb = false) {
    const { public: config } = useRuntimeConfig();

    if (src) {
        if (typeof src === 'object') {
            return src.toString();
        } else if (isSrcExternal(src)) {
            return src.toString();
        } else {
            return `${isAsset ? `/assets` : config.storageUrl}/${prefix ? prefix + '/' : ''}${useThumb ? 'thumbnail_' : ''}${src}`;
        }
    } else {
        return undefined;
    }
}