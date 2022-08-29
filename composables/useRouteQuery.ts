//This is only added due to VueUse's one breaking on Nuxt 3.

export default function<T extends number | string | string[]>(name: string, defaultValue?: T, cast?: (data: string|string[]) => T) {
    const router = useRouter();
    const route = useRoute();
    return computed({
        get() {
            const data = route.query[name];
            if (data == null) {
                return defaultValue ?? null;
            }
            if (Array.isArray(data)) {
                return data.filter(Boolean);
            }
            
            if (cast) {
                return cast(data);
            }

            return data;
        },
        set(v) {
            nextTick(() => {
                router.replace({ query: { ...route.query, [name]: v === defaultValue || v === null ? undefined : v } });
            });
        },
    });
}