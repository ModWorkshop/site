import { AsyncDataExecuteOptions } from 'nuxt/dist/app/composables/asyncData';

/**
 * Handy composable that handles parameter changes
 */

export default function(
    refresh: (opts?: AsyncDataExecuteOptions | undefined) => Promise<void>, 
    watchSource: any,
    onChange: ((val: any, oldVal: any) => void)|null = null
) {
    const loading = ref(false);

    const { start: startPlanLoad } = useTimeoutFn(async () => {
        await refresh();
        loading.value = false;
    }, 250, { immediate: false });

    const planLoad = () => {
        loading.value = true;
        startPlanLoad();
    };

    const page = watchSource.page;
    if (page) {
        watch(page, () => {
            planLoad();
        });
    }

    const watchStuff: any[] = [];

    for (const [key, value] of Object.entries(watchSource)) {
        if (key != 'page' && typeof value == 'object') {
            watchStuff.push(value);
        }
    }

    watch(watchStuff, async (val, oldVal) => {
        if (page) {
            page.value = 1;
        }
        planLoad();
        onChange?.(val, oldVal);
    });
    
    return loading;
}