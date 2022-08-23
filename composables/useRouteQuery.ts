//This is only added due to VueUse's one breaking on Nuxt 3.

export default function<T extends string | string[]>(name: string, defaultValue?: T) {
    const router = useRouter();
    const route = useRoute();
    return computed<any>({
        get() {
            const data = route.query[name];
            if (data == null)
                return defaultValue ?? null;
            if (Array.isArray(data))
                return data.filter(Boolean);
            return data;
        },
        set(v) {
            nextTick(() => {
                router.replace({ query: { ...route.query, [name]: v === defaultValue || v === null ? undefined : v } });
            });
        },
    });
}