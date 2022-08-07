export default function(key: string, defaultValue: unknown) {
    const route = useRoute();
    const query = ref(route.query[key] ?? defaultValue);

    watch(query, val => {
        setQuery(key, val);
    });
    
    return query;
}