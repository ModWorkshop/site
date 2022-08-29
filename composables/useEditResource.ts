import { useI18n } from "vue-i18n";

export default async function<T>(name: string, url: string, template: T) {
    const route = useRoute();
    const { t } = useI18n();

    const id = route.params[`${name}Id`];

    if (id === undefined || id == 'new') {
        return { data: ref<T>(template) };
    }
    else {
        const res = await useFetchData<T>(`${url}/${id}`);
        const { error } = res;

        useHandleError(error, {
            404: t('error_404')
        });

        return res;
    }
}