import type { UseFetchOptions } from '#app';
import { Paginator } from '~/types/paginator';
/**
    Fetches stuff from the API that are many, essentially wrapping it in a paginator.
 */
export default async function<T = any>(url: string | (() => string), options?: UseFetchOptions<Paginator<T>>) {
	// Resets page when any other query param changes
	if (options?.query && options?.query['page']) {
		watch(Object.values({ ...options.query, page: undefined }), () => {
			if (options.query) {
				options.query['page'].value = 1;
			}
		});
	}

	const ret = await useFetchData<Paginator<T>>(url, options);
	const { status, data } = ret;

	return { ...ret, data: reactive(data), loading: computed(() => status.value === 'pending') };
}
