import { AsyncDataExecuteOptions } from 'nuxt/dist/app/composables/asyncData';
import { Ref } from 'vue';

/**
 * Handy composable that handles parameter changes
 */

export default function(
    refresh: (opts?: AsyncDataExecuteOptions | undefined) => Promise<void>, 
    watchSource: any,
    loading: Ref<boolean>,
    onChange: ((val: any, oldVal: any) => void)|null = null
) {
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

    delete watchSource['page'];

    watch(Object.values(watchSource), async (val, oldVal) => {
        if (page) {
            page.value = 1;
        }
        planLoad();
        onChange?.(val, oldVal);
    });
    
}