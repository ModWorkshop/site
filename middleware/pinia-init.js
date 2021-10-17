import { useStore } from '../store';

export default async function ({ $pinia }) {
    if (process.server) {
        const store = useStore($pinia);
        await store.nuxtServerInit();
    }
}