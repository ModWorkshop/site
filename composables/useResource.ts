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
export default async function<T>(name: string, url: string, errorMessages: Record<number|string, string> = {}, params: SearchParams = null, fallback?: T) {
    const route = useRoute();
    
    const { t } = useI18n();

    const id = route.params[`${name}Id`];

    if (!id) {
        return { data: ref(fallback), error: false };
    }

    const res = await useFetchData<T>(`${url}/${id}`, { params });
    const { error } = res;

    useHandleError(error, {
        404: t('error_404'),
        403: t('error_403'),
        ...errorMessages
    });

    return res;
}