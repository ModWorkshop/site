import type { UseFetchOptions } from '#app';
import { Paginator } from '~/types/paginator';
/**
    Fetches stuff from the API that are many, essentially wrapping it in a paginator.
 */
export default async function<T = any>(url: string | (() => string), options?: UseFetchOptions<Paginator<T>> & { watchDelay?: number }) {
	// TODO: deprecate use of .params
	const watchRefs = options?.watch ?? Object.values(toRaw(options?.query ?? options?.params) ?? {});

	if (options?.watchDelay && watchRefs) {
		const newWatch: Ref[] = [];
		for (const v of watchRefs) {
			if (typeof v === 'object') {
				newWatch.push(refDebounced(v as Ref, options.watchDelay));
			}
		}

		options.watch = newWatch;
	}

	const ret = await useFetchData<Paginator<T>>(url, options);
	const { status } = ret;

	return { ...ret, loading: computed(() => status.value === 'pending') };
}
