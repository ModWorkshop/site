import { AsyncDataExecuteOptions } from 'nuxt/dist/app/composables/asyncData';
import { SearchParams } from 'ohmyfetch';
import { useI18n } from "vue-i18n";

/**
 * Attempts to fetch a resoure based on name and URL, if the param doesn't exist, assumes the resources is optional
 * For example forum vs game/payday-2/forum, both are forum but one has a game and one doesn't.
 * 
 * @param name Name of the resource, used for the param name like game -> gameId
 * @param url URL path to fetch stuff from like games -> games/:gameId
 * @param fallback If ID is optional, falls back to this object
 * @returns 
 */

interface _AsyncData<DataT, ErrorT> {
    data: Ref<DataT>;
    pending: Ref<boolean>;
    refresh: (opts?: AsyncDataExecuteOptions) => Promise<void>;
    execute: (opts?: AsyncDataExecuteOptions) => Promise<void>;
    error: Ref<ErrorT | null>;
}

// Basically since this function errors in case we hit an error in the fetch we can be sure something will return.
export default async function<T>(
    name: string,
    url: string,
    errorMessages: Record<number|string, string> = {},
    params?: SearchParams,
    fallback?: T,
    key?: string
): Promise<_AsyncData<T, Error | null>> {
    const route = useRoute();
    
    const { t } = useI18n();

    const id = route.params[`${name}`];

    const res = await useFetchData<T>(`${url}/${id}`, { params, immediate: !!id }, key) as _AsyncData<T, Error | null>;

    //I sometimes really hate typescript, just fucking look at the length of this crap...
    if (!id && fallback) {
        res.data.value = fallback;
    }

    const { error } = res;

    useHandleError(error, {
        404: t('page_error_404'),
        403: t('page_error_403'),
        429: t('error_429'),
        ...errorMessages
    });

    return res;
}