import { useI18n } from "vue-i18n";

/**
 * Attempts to fetch a resoure based on name and URL, if the param doesn't exist, assumes the resources is optional
 * For example forum vs game/payday-2/forum, both are forum but one has a game and one doesn't.
 * 
 * @param name Name of the resource, used for the param name like game -> gameId
 * @param url URL path to fetch stuff from like games -> games/:gameId
 * @returns 
 */
export default async function<T>(name: string, url: string) {
    const route = useRoute();
    const { t } = useI18n();

    const id = route.params[`${name}Id`];

    if (!id) {
        return { data: ref(null) };
    }

    const res = await useFetchData<T>(`${url}/${id}`);
    const { error } = res;

    useHandleError(error, {
        404: t('error_404')
    });

    return res;
}