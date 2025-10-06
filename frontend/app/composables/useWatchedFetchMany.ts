import type { UseFetchOptions } from '#app';
import type { SearchParameters } from 'ofetch';
import { Paginator } from '~/types/paginator';

export interface WatchedFetchManyOptions<T> extends UseFetchOptions<T> {
	onChange?: ((val: any, oldVal: any) => boolean) | null;
}

/**
    This is useFetchMany but tailored for handling parameter changes for lists and so on.
 */
export default async function<T = any>(url: string | (() => string), params: SearchParameters, options?: WatchedFetchManyOptions<Paginator<T>>) {
	const ret = await useFetchMany<T>(url, {
		params: reactive(params),
		watch: false,
		...options
	});

	const { data, status, refresh } = ret;

	const { start: startPlanLoad } = useTimeoutFn(async () => {
		if (typeof url === 'function' ? url() : url) {
			await refresh();
		}
	}, 250, { immediate: false });

	const page = params.page;
	if (page) {
		watch(page, () => {
			startPlanLoad();
		});
	}

	const watchStuff: any[] = [];

	for (const [key, value] of Object.entries(params)) {
		if (key !== 'page' && typeof value === 'object') {
			watchStuff.push(value);
		}
	}

	watch(watchStuff, async () => {
		if (page) {
			page.value = 1;
		}
		startPlanLoad();
	});

	return { ...ret, data: reactive(data ?? new Paginator()), loading: computed(() => status.value === 'pending') };
}
