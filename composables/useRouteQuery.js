import clone from 'rfdc/default';

//This is only added due to VueUse's one breaking on Nuxt 3 + Adding casting support because typescript is shit sometimes

//Trying to make this work with typescript is just... No

export default function(name, defaultValue, cast=null) {
    const router = useRouter();
    const route = useRoute();

    if (Array.isArray(defaultValue)) {
        cast = 'array';
    }

    cast ??= typeof defaultValue;
     
    return computed({
        get() {
            const data = route.query[name];
            if (data == null || data == undefined) {
                return defaultValue ?? null;
            }
            
            if (cast === 'number') {
                return parseInt(data);
            } else if (cast === 'boolean') {
                return data ? 1 : 0;
            } else if (typeof cast == 'function') {
                return cast(data);
            } else if (cast == 'array') {
                if (typeof data == 'string') {
                    return data.split(',').map(value => parseInt(value));
                } else {
                    return clone(defaultValue);
                }
            }

            return data;
        },
        set(v) {
            nextTick(() => {
                if (cast == 'array') {
                    if (v.length) {
                        v = v.join(',');
                    }
                }
                router.replace({ query: { ...route.query, [name]: v === defaultValue || v === null ? undefined : v } });
            });
        },
    });
}