import { Ref } from 'nuxt/dist/app/compat/capi';
import { SearchParams } from "ohmyfetch";
import { useI18n } from "vue-i18n";

export default async function<T>(name: string, url: string, template: T|null=null, params?: SearchParams) {
    const route = useRoute();
    const { t } = useI18n();

    const id = route.params[`${name}Id`];

    if (template && (id === undefined || id == 'new')) {
        return { data: ref<T>(template) };
    }
    else {
        const res = await useFetchData<T>(`${url}/${id}`, { params });
        const { error } = res;

        useHandleError(error, {
            404: t('error_resource_404')
        });

        return {
            refresh: res.refresh,
            error: res.error,
            // This gets handled, in this function we DO NOT expect a null/undefined result
            // If it does return that, then there's something wrong.
            // This function clearly returns either a template or errors in case something goes wrong.
            // Why? So I don't have to write if if if a million times in places I don't expect it to be null...
            data: res.data as Ref<T>
        };
    }
}