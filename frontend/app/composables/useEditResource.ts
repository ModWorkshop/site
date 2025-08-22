import type { SearchParameters } from 'ofetch';
import { useI18n } from 'vue-i18n';
import clone from 'rfdc/default';

export default async function<T extends object>(name: string, url: string, template: T | null = null, params?: SearchParameters) {
	const route = useRoute();
	const { t } = useI18n();

	const id = route.params[`${name}`];

	if (template && (id === undefined || id == 'new')) {
		return { data: ref(clone(template)) as Ref<T> };
	} else {
		const res = await useFetchData<T>(`${url}/${id}`, { params });
		const { error } = res;

		useHandleError(error, {
			404: t('page_error_404')
		});

		return {
			refresh: res.refresh,
			error: res.error,
			// No idea why this is necessary, but this seems to fix reactivity issues
			// If anyone has a better way to handle this + m-form I would be happy for a pull
			data: ref(clone(res.data.value as T)) as Ref<T>
		};
	}
}
